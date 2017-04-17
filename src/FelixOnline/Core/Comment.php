<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Comment class
 * Deals with both comment retrieval and comment submission
 *
 * Fields:
 *	  id
 *	  article
 *	  user
 *	  comment
 *	  timestamp
 *	  active
 *	  reply
 *	  likes
 *	  dislikes
 *
 *	  IP
 *	  pending
 *	  spam
 *
 * Comment flags:
 *		  active | pending | spam
 *			 0   |	0	|   0	  rejected comment
 *			 1   |	0	|   0	  approved comment
 *			 1   |	1	|   0	  pending moderation comment
 *			 0   |	0	|   1	  spam comment
 *			 0   |	1	|   0	  INVALID
 *			 1   |	0	|   1	  INVALID
 *			 0   |	1	|   1	  INVALID
 *			 1   |	1	|   1	  INVALID
 *
 * Examples
 *	  // Get comment
 *	  $comment = new Comment(300);
 *	  echo $comment->getComment();
 *
 *	  // Submit comment
 *	  $comment = new Comment();
 *	  $comment->setArticle(100); // article id
 *	  $comment->setComment('Hello world');
 *	  $comment->setUser('felix'); // if we know the user
 *    $comment->setName('Felix');
 *    $comment->setEmail('felix@imperial.ac.uk');
 *	  if($id = $comment->save()) echo 'Success!';
 */
class Comment extends BaseDB
{
	const EXTERNAL_COMMENT_ID = 80000000; // external comment id start

	private $article; // article class comment is on
	private $user; // user class
	private $reply; // comment class of reply
	private $commentsToApprove;

	public $dbtable = 'comment';

	/**
	 * Constructor for Comment class
	 * If initialised with an id then store relevant data
	 * Do nothing if not
	 *
	 * $id - ID of comment
	 *
	 * Returns comment object.
	 */
	public function __construct($id = NULL)
	{
		// common fields
		$fields = array(
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'user' => new Type\ForeignKey('FelixOnline\Core\User'),
			'name' => new Type\CharField(array(
				'transformers' => array(
					Type\BaseType::TRANSFORMER_NO_HTML
				)
			)),
			'comment' => new Type\TextField(array(
				'transformers' => array(
					Type\BaseType::TRANSFORMER_NO_HTML
				)
			)),
			'timestamp' => new Type\DateTimeField(),
			'ip' => new Type\CharField(),
			'email' => new Type\CharField(),
			'useragent' => new Type\CharField(),
			'referer' => new Type\CharField(),
			'active' => new Type\BooleanField(),
			'pending' => new Type\BooleanField(),
			'spam' => new Type\BooleanField(),
			'reply' => new Type\ForeignKey('FelixOnline\Core\Comment'),
			'likes' => new Type\IntegerField(array(
				'null' => false
			)),
			'dislikes' => new Type\IntegerField(array(
				'null' => false
			)),
		);

		parent::__construct($fields, $id);
	}

	/**
	 * Public: Get comment content
	 */
	public function getContent()
	{
		$output = nl2br(trim($this->getComment()));
		return $output;
	}

	/**
	 * Public: Is any of the comment's parents moderated out
	 */
	public function isAccessible() {
		$loop = $this;
		$access = true;

		while($loop->getReply() != null) {
			$loop = $loop->getReply();

			if($loop->getSpam() == 1 || $loop->getActive() == 0 || $loop->isEmailValid() == 0) {
				$access = false;
			}
		}

		return $access;
	}

	/**
	 * Public: Is the comment's email valid?
	 */
	public function isEmailValid()
	{
		$validation = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation')
				->filter('email = "%s"', array($this->getEmail()))
				->filter("confirmed = 1");

		return $validation->count();
	}

	/**
	 * Public: Get commenter's name
	 */
	public function getName()
	{
		if ($this->fields['name']->getValue()) { // if external commenter has a name
			return $this->fields['name']->getValue();
		} else {
			return 'Anonymous'; // else return Anonymous
		}
	}

	/**
	 * Public: Get url
	 */
	public function getURL()
	{
		return $this->getArticle()->getURL().'#comment'.$this->getId();
	}

	/**
	 * Public: Check if comment is from author of article
	 *
	 * Returns true if is author. False if not.
	 */
	public function byAuthor()
	{
		if (in_array($this->getUser(), $this->getArticle()->getAuthors())) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Public: Check if comment is rejected
	 */
	public function isRejected()
	{
		if (!$this->getActive()
		   || !$this->getActive() && !$this->getPending()) { // if comment that is rejected
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Public: Check if comment is pending approval
	 */
	public function isPending()
	{
		$app = App::getInstance();

		if ($this->getActive()
			&& $this->getPending()
			&& $this->getIp() == $app['env']['REMOTE_ADDR']
		) { // if comment is pending for this ip address
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Public: Check if current user has liked or disliked the comment
	 *
	 * $user - user object
	 *
	 * Returns true or false
	 */
	public function userLikedOrDislikedComment($ip, $useragent)
	{
		$count = BaseManager::build('FelixOnline\Core\Comment', 'comment_like')
			->filter("ip = '%s'", array($ip))
			->filter("user_agent = '%s'", array($useragent))
			->filter("comment = %i", array($this->getId()))
			->count();

		return (boolean) $count;
	}

	/**
	 * Public: Check if current user has liked the comment
	 *
	 * $user - user object
	 *
	 * Returns true or false
	 */
	public function userLikedComment($ip, $useragent)
	{
		$count = BaseManager::build('FelixOnline\Core\Comment', 'comment_like')
			->filter("ip = '%s'", array($ip))
			->filter("user_agent = '%s'", array($useragent))
			->filter("comment = %i", array($this->getId()))
			->filter("binlike = 1")
			->count();

		return (boolean) $count;
	}

	/**
	 * Public: Check if current user has disliked the comment
	 *
	 * $user - user object
	 *
	 * Returns true or false
	 */
	public function userDislikedComment($ip, $useragent)
	{
		$count = BaseManager::build('FelixOnline\Core\Comment', 'comment_like')
			->filter("ip = '%s'", array($ip))
			->filter("user_agent = '%s'", array($useragent))
			->filter("comment = %i", array($this->getId()))
			->filter("binlike = 0")
			->count();

		return (boolean) $count;
	}

	/*
	 * Public: Like comment
	 *
	 * $user - user object
	 *
	 * Returns number of likes
	 */
	public function likeComment($ip, $useragent)
	{
		$app = App::getInstance();

		if (!$this->userLikedOrDislikedComment($ip, $useragent)) { // check user hasn't already liked the comment
			$sql = $app['safesql']->query(
				"INSERT INTO `comment_like`
				(
					ip,
					user_agent,
					comment,
					binlike,
					deleted
				) VALUES (
					'%s',
					'%s',
					%i,
					1,
					0
				)",
				array(
					$ip,
					$useragent,
					$this->getId(),
				));
			$app['db']->query($sql);

			$likes = $this->getLikes() + 1;
			$this->setLikes($likes)
				->save();

			return $likes;
		} else {
			return false;
		}
	}

	/*
	 * Public: Dislike comment
	 *
	 * $user - user object
	 *
	 * Returns number of dislikes
	 */
	public function dislikeComment($ip, $useragent)
	{
		$app = App::getInstance();

		if (!$this->userLikedOrDislikedComment($ip, $useragent)) { // check user hasn't already liked the comment
			$sql = $app['safesql']->query(
				"INSERT INTO `comment_like`
				(
					ip,
					user_agent,
					comment,
					binlike,
					deleted
				) VALUES (
					'%s',
					'%s',
					%i,
					0,
					0
				)",
				array(
					$ip,
					$useragent,
					$this->getId(),
				));
			$app['db']->query($sql);

			$dislikes = $this->getDislikes() + 1;
			$this->setDislikes($dislikes)
				->save();

			return $dislikes;
		} else {
			return false;
		}
	}


	/**
	 * Public: Check if comment already exists
	 *
	 * Returns boolean
	 */
	public function commentExists()
	{
		$comments = (new CommentManager())
			->filter('article = %i', array($this->getArticle()->getId()))
			->filter("comment = '%s'", array($this->getComment()))
			->filter("name = '%s'", array($this->fields['name']->getValue()));

		$count = $comments->count();
		return (boolean) $count;
	}

	/**
	 * Public: Save new comment into database
	 *
	 * Returns id of new comment
	 */
	public function save()
	{
		// Email address validation tests are NOT handled here as different workflows may have different rules for auto validation
		$app = App::getInstance();

		// If an update
		if ($this->pk && $this->fields[$this->pk]->getValue()) {
			return parent::save();
		}

		$this->setIp($app['env']['REMOTE_ADDR']);
		$this->setUseragent($app['env']['HTTP_USER_AGENT']);
		$this->setReferer($app['env']['HTTP_REFERER']);

		if (!$this->getUser()) {
			// check key
			$key_check = $app['akismet']->keyCheck(
				$app->getOption('akismet_api_key', ''),
				$app->getOption('base_url')
			);

			if ($key_check == false) {
				throw new \FelixOnline\Exceptions\InternalException('Akismet key is invalid');
			}

			// check spam using akismet
			$check = $app['akismet']->check(array(
				'permalink' => $this->getArticle()->getURL(),
				'comment_type' => 'comment',
				'comment_author' => $this->fields['name']->getValue(),
				'comment_content' => $this->getComment(),
				'comment_author_email' => $this->getEmail(),
				'user_ip' => $this->getIp(),
				'user_agent' => $this->getUseragent(),
				'referrer' => $this->getReferer(),
			));

			// check for akismet errors
			if (!is_null($app['akismet']->getError())) {
				throw new \FelixOnline\Exceptions\InternalException($app['akismet']->getError());
			}

			if ($check == true) { // if comment is spam
				$this->setActive(0);
				$this->setPending(0);
				$this->setSpam(1);
			} else { // Not spam
				$this->setActive(1);
				$this->setPending(1);
				$this->setSpam(0);
			}
		} else {
			$this->setActive(1);
			$this->setPending(0);
			$this->setSpam(0);
		}

		parent::save();

		if(!$this->getSpam()) {
			// Send emails
			if (!$this->getUser()) {
				$log_entry = new \FelixOnline\Core\AkismetLog();
				$log_entry->setCommentId($this)
					->setAction('check')
					->setIsSpam($check)
					->setError($app['akismet']->getError())
					->save();

				// If pending comment
				if (!$this->getSpam() && $this->getPending() && $this->getActive()) {
					$this->emailComment();
				}
			}
		}

		return $this->getId(); // return new comment id
	}

	public function markAsSpam()
	{
		$app = App::getInstance();

		// check key
		$key_check = $app['akismet']->keyCheck(
			$app->getOption('akismet_api_key', ''),
			$app->getOption('base_url')
		);

		if ($key_check == false) {
			throw new \FelixOnline\Exceptions\InternalException('Akismet key is invalid');
		}

		// check spam using akismet
		$check = $app['akismet']->sendSpam(array(
			'permalink' => $this->getArticle()->getURL(),
			'comment_type' => 'comment',
			'comment_author' => $this->fields['name']->getValue(),
			'comment_content' => $this->getComment(),
			'comment_author_email' => $this->getEmail(),
			'user_ip' => $this->getIp(),
			'user_agent' => $this->getUseragent(),
			'referrer' => $this->getReferer(),
		));

		$log_entry = new \FelixOnline\Core\AkismetLog();
		$log_entry->setCommentId($this)
			->setAction('check')
			->setIsSpam($check)
			->setError($app['akismet']->getError())
			->save();

		// check for akismet errors
		if (!is_null($app['akismet']->getError())) {
			throw new \FelixOnline\Exceptions\InternalException($app['akismet']->getError());
		}

		$this->setActive(0);
		$this->setPending(0);
		$this->setSpam(1);

		$this->save();
	}

	public function markAsHam()
	{
		$app = App::getInstance();

		// check key
		$key_check = $app['akismet']->keyCheck(
			$app->getOption('akismet_api_key', ''),
			$app->getOption('base_url')
		);

		if ($key_check == false) {
			throw new \FelixOnline\Exceptions\InternalException('Akismet key is invalid');
		}

		// check spam using akismet
		$check = $app['akismet']->sendHam(array(
			'permalink' => $this->getArticle()->getURL(),
			'comment_type' => 'comment',
			'comment_author' => $this->fields['name']->getValue(),
			'comment_content' => $this->getComment(),
			'comment_author_email' => $this->getEmail(),
			'user_ip' => $this->getIp(),
			'user_agent' => $this->getUseragent(),
			'referrer' => $this->getReferer(),
		));

		$log_entry = new \FelixOnline\Core\AkismetLog();
		$log_entry->setCommentId($this)
			->setAction('check')
			->setIsSpam($check)
			->setError($app['akismet']->getError())
			->save();

		// check for akismet errors
		if (!is_null($app['akismet']->getError())) {
			throw new \FelixOnline\Exceptions\InternalException($app['akismet']->getError());
		}

		$this->setActive(1);
		$this->setPending(1);
		$this->setSpam(0);

		$this->save();
	}

	public function reportAbuse()
	{
		$this->setActive(1);
		$this->setPending(1);
		$this->setSpam(0);

		$this->save();

		$this->emailAbuseComment();
	}

	/**
	 * Public: Email authors of article
	 */
	public function emailAuthors()
	{
		$app = App::getInstance();
		$authors = $this->getArticle()->getAuthors();

		if (in_array($this->getUser(), $authors)) { // if author of comment is one of the authors
			$key = array_search($this->getUser(), $authors);
			unset($authors[$key]);
		}

		// Create message
		$message = \Swift_Message::newInstance()
			->setSubject($this->getName().' has commented on "'.$this->getArticle()->getTitle().'"')
			->setFrom(array(Settings::get('email_replyto_addr') => Settings::get('email_replyto_name')));

		foreach ($authors as $author) {
			// Get content
			ob_start();
			$data = array(
				'app' => $app,
				'user' => $author,
				'comment' => $this,
			);

			// Render email template
			call_user_func(function() use($data) {
				extract($data);
				include realpath(__DIR__ . '/../../../templates/') . '/comment_notification.php';
			});
			$content = ob_get_contents();
			ob_end_clean();

			$message->setBody($content, 'text/html')
				->setTo(array(
					$author->getEmail() => $author->getName(),
				));

			// Send email
			$app['email']->send($message);
		}

		return true;
	}

	/*
	 * Private: Email comment author with reply
	 */
	public function emailReply()
	{
		$app = App::getInstance();
		$reply = $this->getReply();

		// Get content
		ob_start();
		$data = array(
			'app' => $app,
			'comment' => $this,
			'reply' => $reply,
		);
		// Render email template
		call_user_func(function() use($data) {
			extract($data);
			include realpath(__DIR__ . '/../../../templates/') . '/comment_reply_notification.php';
		});
		$content = ob_get_contents();
		ob_end_clean();

		// Create message
		$message = \Swift_Message::newInstance()
			->setSubject($this->getName() . ' has replied to your comment on "'.$this->getArticle()->getTitle().'"')
			->setTo(array(
				$reply->getEmail() => $reply->getName(),
			))
			->setFrom(array(Settings::get('email_replyto_addr') => Settings::get('email_replyto_name')))
			->setBody($content, 'text/html');

		// Send message
		return $app['email']->send($message);
	}

	/*
	 * Private: Email felix on new non-logged-in comment
	 */
	private function emailComment()
	{
		$app = App::getInstance();

		// Get content
		ob_start();
		$data = array(
			'app' => $app,
			'comment' => $this,
		);

		// Render email template
		call_user_func(function() use($data) {
			extract($data);
			include realpath(__DIR__ . '/../../../templates/') . '/new_external_comment.php';
		});
		$content = ob_get_contents();
		ob_end_clean();

		// Create message
		$message = \Swift_Message::newInstance()
			->setSubject('New comment to moderate on "'.$this->getArticle()->getTitle().'"')
			->setTo(explode(", ", Settings::get('email_extcomment_notifyaddr')))
			->setFrom(array(Settings::get('email_replyto_addr') => Settings::get('email_replyto_name')))
			->setBody($content, 'text/html');

		// Send message
		return $app['email']->send($message);
	}

	/*
	 * Private: Email felix on abuse report
	 */
	private function emailAbuseComment()
	{
		$app = App::getInstance();

		// Get content
		ob_start();
		$data = array(
			'app' => $app,
			'comment' => $this,
		);

		// Render email template
		call_user_func(function() use($data) {
			extract($data);
			include realpath(__DIR__ . '/../../../templates/') . '/new_abuse_comment.php';
		});
		$content = ob_get_contents();
		ob_end_clean();

		// Create message
		$message = \Swift_Message::newInstance()
			->setSubject('Abuse reported for comment on "'.$this->getArticle()->getTitle().'"')
			->setTo(explode(", ", Settings::get('email_extcomment_notifyaddr')))
			->setFrom(array(Settings::get('email_replyto_addr') => Settings::get('email_replyto_name')))
			->setBody($content, 'text/html');

		// Send message
		return $app['email']->send($message);
	}

	/**
	 * Get a number of comments to approve
	 */
	public function getNumCommentsToApprove()
	{
		return (new CommentManager())
			->filter('pending = 1')
			->filter('active = 1')
			->filter('spam = 0')
			->count();
	}

	public function getValidatedReplyManager()
	{
		$comments = (new CommentManager())
			->filter("active = 1")
			->filter("spam = 0 ")
			->filter("reply = %i", array($this->getId()))
			->order("timestamp", "ASC");

		$validation = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation')
			->filter("confirmed = 1");

		$comments->join($validation, null, 'email', 'email');

		return $comments;
	}

	/*
	 * Public: Get replies with validated email addresses
	 *
	 * Returns array
	 */
	public function getValidatedReplies() {
		$comments = $this->getValidatedReplyManager();

		$comments = $comments->values();

		$comments = is_null($comments) ? array() : $comments;

		return $comments;
	}

	/*
	 * Public: Get replies with validated email addresses
	 *
	 * Returns array
	 */
	public function getNumValidatedReplies() {
		$comments = $this->getValidatedReplyManager();

		$comments = $comments->count();

		return $comments;
	}
}

<?php
namespace FelixOnline\Core;
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
 *	  Internal:
 *		  active - comment is active or not
 *
 *	  External:
 *		  active | pending | spam  
 *			 0   |	0	|   0	  rejected comment
 *			 1   |	0	|   0	  approved comment
 *			 1   |	1	|   0	  pending comment
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
 *	  $comment->setExternal(false); // internal comment
 *	  $comment->setArticle(100); // article id
 *	  $comment->setComment('Hello world');
 *	  $comment->setUser('felix');
 *	  if($id = $comment->save()) echo 'Success!';
 */
class Comment extends BaseDB
{
	const EXTERNAL_COMMENT_ID = 80000000; // external comment id start

	private $article; // article class comment is on
	private $user; // user class
	private $reply; // comment class of reply
	private $external = false; // if comment is external or not. Default false
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
			'external' => new Type\BooleanField(),
			'user' => new Type\ForeignKey('FelixOnline\Core\User'),
			'name' => new Type\CharField(array(
				'transformers' => array(
					Type\BaseType::TRANSFORMER_NO_HTML
				)
			)),
			'comment' => new Type\CharField(array(
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
	 * Public: Get user class
	 */
	public function getUser()
	{
		if ($this->getExternal()) {
			throw new \FelixOnline\Exceptions\InternalException('External comment does not have a user');
		}

		return $this->fields['user']->getValue();
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
	 * Public: Get commenter's name
	 */
	public function getName()
	{
		if ($this->getExternal()) {
			if ($this->fields['name']->getValue()) { // if external commenter has a name
				return $this->fields['name']->getValue();
			} else {
				return 'Anonymous'; // else return Anonymous
			}
		} else {
			return $this->getUser()->getName();
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
		if ($this->getExternal()) {
			return false;
		} else {
			if (in_array($this->getUser(), $this->getArticle()->getAuthors())) {
				return true;
			} else {
				return false;
			}
		}
	}

	/*
	 * Public: Check if comment is rejected
	 */
	public function isRejected()
	{
		if (!$this->getExternal() && !$this->getActive() 
		   || $this->getExternal() && !$this->getActive() && !$this->getPending()) { // if comment that is rejected
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

		if ($this->getExternal()
			&& $this->getActive()
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
	public function userLikedComment($user)
	{
		$count = BaseManager::build(null, 'comment_like')
			->filter("user = '%s'", array($user->getUser()))
			->filter("comment = %i", array($this->getId()))
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
	public function likeComment($user)
	{
		$app = App::getInstance();

		if (!$this->userLikedComment($user)) { // check user hasn't already liked the comment
			$sql = $app['safesql']->query(
				"INSERT INTO `comment_like` 
				(
					user,
					comment,
					binlike
				) VALUES (
					'%s',
					%i,
					1
				)",
				array(
					$user->getUser(),
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
	public function dislikeComment($user)
	{
		$app = App::getInstance();

		if (!$this->userLikedComment($user)) { // check user hasn't already liked the comment
			$sql = $app['safesql']->query(
				"INSERT INTO `comment_like` 
				(
					user,
					comment,
					binlike
				) VALUES (
					'%s',
					%i,
					0
				)",
				array(
					$user->getUser(),
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
			->filter("comment = '%s'", array($this->getComment()));

		if ($this->getExternal()) {
			$comments->filter("name = '%s'", array($this->fields['name']->getValue()));
		} else {
			$comments->filter("user = '%s'", array($this->getUser()->getUser()));
		}

		$count = $comments->count();
		return (boolean) $count;
	}

	/**
	 * Public: Save new comment into database
	 *
	 * TODO
	 *
	 * Returns id of new comment
	 */
	public function save()
	{
		$app = App::getInstance();

		// If an update
		if ($this->pk && $this->fields[$this->pk]->getValue()) {
			return parent::save();
		}

		$this->setIp($app['env']['REMOTE_ADDR']);
		$this->setUseragent($app['env']['HTTP_USER_AGENT']);
		$this->setReferer($app['env']['HTTP_REFERER']);

		if ($this->getExternal()) {
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

			$log_entry = new \FelixOnline\Core\AkismetLog()
							->setCommentId($this)
							->setAction('check')
							->setIsSpam($check)
							->setError($app['akismet']->getError())
							->setRequest($app['akismet']->getRequest())
							->setResponse($app['akismet']->getResponse())
							->save();

			// check for akismet errors
			if (!is_null($app['akismet']->getError())) {
				throw new \FelixOnline\Exceptions\ExternalException($app['akismet']->getError());
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

		// Send emails
		if ($this->getExternal()) {
			// If pending comment
			if (!$this->getSpam() && $this->getPending() && $this->getActive()) {
				$this->emailExternalComment();
			}
		} else { // internal emails
			if ($this->getReply()) { // if comment is replying to an internal comment 
				$this->emailReply();
			}

			// email authors of article
			$this->emailAuthors();
		}
		
		return $this->getId(); // return new comment id
	}

	public function markAsSpam() {
		if ($this->getExternal()) {
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

			$log_entry = new \FelixOnline\Core\AkismetLog()
							->setCommentId($this)
							->setAction('check')
							->setIsSpam($check)
							->setError($app['akismet']->getError())
							->setRequest($app['akismet']->getRequest())
							->setResponse($app['akismet']->getResponse())
							->save();

			// check for akismet errors
			if (!is_null($app['akismet']->getError())) {
				throw new \FelixOnline\Exceptions\ExternalException($app['akismet']->getError());
			}

			$this->setActive(0);
			$this->setPending(0);
			$this->setSpam(1);

			$this->save();
		} else {
			throw new \FelixOnline\Exceptions\InternalException('Trying to mark internal comment as spam');
		}
	}

	public function markAsHam() {
		if ($this->getExternal()) {
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

			$log_entry = new \FelixOnline\Core\AkismetLog()
							->setCommentId($this)
							->setAction('check')
							->setIsSpam($check)
							->setError($app['akismet']->getError())
							->setRequest($app['akismet']->getRequest())
							->setResponse($app['akismet']->getResponse())
							->save();

			// check for akismet errors
			if (!is_null($app['akismet']->getError())) {
				throw new \FelixOnline\Exceptions\ExternalException($app['akismet']->getError());
			}

			$this->setActive(1);
			$this->setPending(1);
			$this->setSpam(0);

			$this->save();
		} else {
			throw new \FelixOnline\Exceptions\InternalException('Trying to mark internal comment as spam');
		}
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
			->setFrom(array('no-reply@imperial.ac.uk' => 'Felix Online'));

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
	private function emailReply()
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
				$reply->getUser()->getEmail() => $reply->getUser()->getName(),
			))
			->setFrom(array('no-reply@imperial.ac.uk' => 'Felix Online'))
			->setBody($content, 'text/html');

		// Send message
		return $app['email']->send($message);
	}

	/*
	 * Private: Email felix on new external comment
	 */
	private function emailExternalComment()
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
			->setSubject('New comment to approve on "'.$this->getArticle()->getTitle().'"')
			->setTo(explode(", ", EMAIL_EXTCOMMENT_NOTIFYADDR))
			->setFrom(array('no-reply@imperial.ac.uk' => 'Felix Online'))
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
}

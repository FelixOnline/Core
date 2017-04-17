<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article class
 * Deals with both article retrieval and article submission
 *
 * Fields:
 *	  id			  - id of article
 *	  title		   - title of article
 *	  teaser		  - article teaser
 *	  category		- id of category article is in
 *	  date			- timestamp when article was added to site
 *	  approvedby	  - user who approved the article to be published
 *	  published	   - timestamp when article was published
 *	  hidden		  - if article is hidden from engine
 *	  searchable       - can article be seen by search engines?
 *	  text1		   - id of main article text
 *	  img1			- id of main article image
 */
class Article extends BaseDB {
	const TEASER_LENGTH = 200;

	private $authors; // array of authors of article
	private $approvedby; // user object of user who approved article
	private $category_cat; // category cat (short version)
	private $category_label; // category label
	private $content; // article content
	private $image; // image class
	private $image_title; // image title
	private $num_comments; // number of comments
	private $category; // category class
	private $search = array('@<>@',
		'@<script[^>]*?>.*?</script>@siU',  // javascript
		'@<style[^>]*?>.*?</style>@siU',	// style tags
		'@<embed[^>]*?>.*?</embed>@siU',	// embed
		'@<object[^>]*?>.*?</object>@siU',	// object
		'@<iframe[^>]*?>.*?</iframe>@siU',	// iframe
		'@<![\s\S]*?--[ \t\n\r]*>@',		// multi-line comments including CDATA
		'@</?[^>]*>*@' 		  // html tags
	);

	public $dbtable = 'article';

	/*
	 * Constructor for Article class
	 * If initialised with id then store relevant data in object
	 *
	 * $id - ID of article (optional)
	 *
	 * Returns article object
	 */
	function __construct($id = NULL)
	{
		$fields = array(
			'title' => new Type\CharField(),
			'teaser' => new Type\CharField(),
			'reviewedby' => new Type\ForeignKey('FelixOnline\Core\User'),
			'category' => new Type\ForeignKey('FelixOnline\Core\Category'),
			'date' => new Type\DateTimeField(),
			'hidden' => new Type\BooleanField(array(
				'null' => false,
			)),
			'searchable' => new Type\BooleanField(array(
				'null' => false,
			)),
			'text1' => new Type\ForeignKey('FelixOnline\Core\Text'),
			'img1' => new Type\ForeignKey('FelixOnline\Core\Image'),
			'video_url' => new Type\CharField(),
			'img_caption' => new Type\CharField(),
			'comment_status' => new Type\ForeignKey('FelixOnline\Core\ArticleCommentStatus'),
			'is_live' => new Type\BooleanField(array(
				'null' => false,
			)),
			'blog' => new Type\ForeignKey('FelixOnline\Core\Blog')
		);

		parent::__construct($fields, $id);

		if($id != NULL && $this->getCategory() == NULL) {
			throw new \FelixOnline\Exceptions\ModelNotFoundException("This article is in a secret category which you cannot access", "Article", $id);
		}
	}

	/*
	 * Public: Get array of topics of article
	 *
	 * Returns array
	 */
	public function getTopics()
	{

		$topic = BaseManager::build('FelixOnline\Core\ArticleTopic', 'article_topic', 'topic')
			->filter('article = %i', array($this->getId()));

		$topics = BaseManager::build('FelixOnline\Core\Topic', 'topic', 'slug')
			->join($topic, 'LEFT', 'slug', 'topic')
			->filter('disabled = 0')
			->values();

		return $topics;
	}

	/*
	 * Public: Get array of authors of article
	 *
	 * Returns array
	 */
	public function getAuthors()
	{
		$authors = BaseManager::build('FelixOnline\Core\User', 'article_author', 'author')
			->filter('article = %i', array($this->getId()))
			->values();

		return $authors;
	}

	/**
	 * Public: Get list of authors in english
	 *
	 * Returns html string of article authors
	 */
	public function getAuthorsEnglish() {
		$array = $this->getAuthors();
		// sanity check
		if (!$array || !count ($array))
			return '';
		// change array into linked usernames
		foreach ($array as $key => $user) {
			$full_array[$key] = '<a href="'.$user->getURL().'">'.$user->getName().'</a>';
		}
		// get last element
		$last = array_pop($full_array);
		// if it was the only element - return it
		if (!count ($full_array))
			return $last;
		return implode (', ', $full_array).' and '.$last;
	}

	/**
	 * Public: Get article content
	 */
	public function getContent() {
		if($this->getText1() == null) {
			throw new \FelixOnline\Exceptions\InternalException('Article text has gone missing!');
		}

		$string = $this->getText1()->getContent();

		return $string;
	}

	/**
	 * Public: Get article teaser
	 *
	 * Returns string
	 */
	public function getTeaserFull() {
		if ($this->getTeaser()) {
			return str_replace('<br/>', '', strip_tags($this->getTeaser()));
		} else {
			$text = trim(preg_replace( "/\r|\n/", " ", strip_tags($this->getContent())));
			return trim(
				substr(
					$text,
					0,
					strrpos(
						substr(
							$text,
							0,
							self::TEASER_LENGTH
						),
						' '
					)
				)
			).'...';
		}
	}

	/*
	 * Public: Get article preview with word limit
	 * Shortens article content to word limit
	 *
	 * $limit - word limit [defaults to 50]
	 */
	public function getPreview($limit = 50) {
		$string = trim(preg_replace( "/\r|\n/", " ", strip_tags($this->getContent())));
		$words = explode(" ", $string);
		$append = ' ... <br/><a href="'.$this->getURL().'" title="Read more" id="readmorelink">Read more</a>'; // TODO move into template
		return implode(" ", array_splice($words, 0, $limit)) . $append;
	}

	/*
	 * Public: Get short description
	 * Limit article content to a certain character length
	 *
	 * $limit - character limit for description [defaults to 80]
	 */
	public function getShortDesc($limit = 80) {
		return substr(trim(preg_replace( "/\r|\n/", " ", strip_tags($this->getContent()))), 0, $limit);
	}

	/*
	 * Public: Get number of comments on article
	 *
	 * Returns int
	 */
	public function getNumComments()
	{
		$count = (new CommentManager())
			->filter("article = %i", array($this->getId()))
			->filter("active = 1")
			->filter("spam = 0 ");

		$count = $count->values();

		$num = 0;

		if($count) {
			foreach($count as $item) {
				if($item->isAccessible()) {
					$num++;
				}
			}
		}

		return $num;
	}

	/*
	 * Public: Get number of comments which have been validated on article
	 *
	 * Returns int
	 */
	public function getNumValidatedComments()
	{
		$count = (new CommentManager())
			->filter("article = %i", array($this->getId()))
			->filter("active = 1")
			->filter("spam = 0 ");

		$validation = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation')
			->filter("confirmed = 1");

		$count->join($validation, null, 'email', 'email');
		$num = 0;

		$count = $count->values();

		if($count) {
			foreach($count as $item) {
				if($item->isAccessible()) {
					$num++;
				}
			}
		}

		return $num;
	}

	/*
	 * Public: Get comments
	 *
	 * $ip - server ip
	 *
	 * Returns array
	 */
	public function getComments($ip = NULL) {
		$app = App::getInstance();

		$comments = (new CommentManager())
			->filter("article = %i", array($this->getId()))
			->filter("active = 1")
			->filter("spam = 0 ")
			->values();

		$comments = is_null($comments) ? array() : $comments;

		return $comments;
	}

	/*
	 * Public: Get comments with validated email addresses
	 *
	 * Returns array
	 */
	public function getValidatedComments() {
		$app = App::getInstance();

		$comments = (new CommentManager())
			->filter("article = %i", array($this->getId()))
			->filter("active = 1")
			->filter("spam = 0 ");

		$validation = BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation')
			->filter("confirmed = 1");

		$comments->join($validation, null, 'email', 'email');

		$comments = $comments->values();

		$comments = is_null($comments) ? array() : $comments;

		return $comments;
	}

	/*
	 * Public: Get image class
	 */
	public function getImage() {
		return $this->getImg1();
	}

	/*
	 * Public: Get number of visits the article has
	 *
	 * Returns integer
	 */
	public function getHits() {
		$app = App::getInstance();
		$sql = $app['safesql']->query(
			"SELECT
				COUNT(id)
			FROM
				`article_visit`
			WHERE article = %i
			AND `article_visit`.deleted = 0",
			array(
				$this->getId()
			)
		);

		return $app['db']->get_var($sql);
	}

	/*
	 * Public: Get number of unique visits the article has
	 *
	 * Returns integer
	 */
	public function getUniqueHits() {
		$app = App::getInstance();
		$sql = $app['safesql']->query(
			"SELECT
				COUNT(id)
			FROM
				`article_visit`
			WHERE article = %i
			AND repeat_visit = 0
			AND `article_visit`.deleted = 0",
			array(
				$this->getId()
			)
		);

		return $app['db']->get_var($sql);
	}

	/*
	 * Public: Get full article url
	 *
	 * Returns string
	 */
	public function getURL() {
		$app = App::getInstance();
		return $app->getOption('base_url') . $this->constructURL();
	}

	/*
	 * Private: Construct url for article from title and category label
	 *
	 * Returns string
	 */
	private function constructURL() {
		$cat = $this->getCategory()->getCat();
		$dashed = Utility::urliseText($this->getTitle());
		$output = $cat.'/'.$this->getId().'/'.$dashed.'/'; // output: CAT/ID/TITLE/
		return $output;
	}

	/*
	 * Public: Log visit and increment hit count on article
	 * Check if user has visited page before (based on ip or user for a set length of time)
	 */
	public function logVisit() {
		if (!$this->recentlyVisited()) {
			$this->logVisitor();
		} else {
			$this->logVisitor(1);
		}
	}

	/*
	 * Private: Add log of visitor into article_vist table
	 */
	private function logVisitor($repeat = 0) {
		$app = App::getInstance();

		$user = NULL;
		if ($app['currentuser']->isLoggedIn()) {
			$user = $app['currentuser']->getUser();

			$sql = $app['safesql']->query(
				"INSERT INTO
					article_visit
				(
					article,
					user,
					IP,
					browser,
					referrer,
					repeat_visit,
					deleted
				) VALUES (%q)",
					array(
						array(
							$this->getId(),
							$user,
							$app['env']['REMOTE_ADDR'],
							$app['env']['HTTP_USER_AGENT'],
							$app['env']['HTTP_REFERER'],
							$repeat,
							0
						)
					)
				);
		} else {
		$sql = $app['safesql']->query(
			"INSERT INTO
				article_visit
			(
				article,
				IP,
				browser,
				referrer,
				repeat_visit,
				deleted
			) VALUES (%q)",
				array(
					array(
						$this->getId(),
						$app['env']['REMOTE_ADDR'],
						$app['env']['HTTP_USER_AGENT'],
						$app['env']['HTTP_REFERER'],
						$repeat,
						0,
					)
				)
			);
		}

		return $app['db']->query($sql);
	}

	/*
	 * Private: Check if user has recently visited article
	 *
	 * Returns boolean
	 */
	private function recentlyVisited() {
		$app = App::getInstance();

		if ($app['currentuser']->isLoggedIn()) {
			$sql = $app['safesql']->query(
				"SELECT
					COUNT(id)
				FROM
					`article_visit`
				WHERE user = '%s'
				AND article = '%s'
				AND `article_visit`.deleted = 0
				AND timestamp >= NOW() - INTERVAL 4 WEEK",
				array(
					$app['currentuser']->getUser(),
					$this->getId()
				)
			);
			return $app['db']->get_var($sql);
		} else {
			$sql = $app['safesql']->query(
				"SELECT
					COUNT(id)
				FROM
					`article_visit`
				WHERE IP = '%s'
				AND browser = '%s'
				AND article = %i
				AND `article_visit`.deleted = 0
				AND timestamp >= NOW() - INTERVAL 4 WEEK",
				array(
					$app['env']['REMOTE_ADDR'],
					$app['env']['HTTP_USER_AGENT'],
					$this->getId()
				)
			);

			return $app['db']->get_var($sql);
		}
	}

	/**
	 * Public: Set content to article
	 */
	public function setContent($content) {
		$app = App::getInstance();

		$text = new \FelixOnline\Core\Text();
		$text->setContent($content);
		$text->save();

		$this->setText1($text);

		return $this;
	}

	/**
	 * Public: Add authors to article
	 */
	public function addAuthors($authors) {
		$app = App::getInstance();

		foreach ($authors as $author) {
			$authorRecord = new ArticleAuthor();
			$authorRecord->setArticle($this)->setAuthor($author)->save();
		}
		return $authors;
	}

	/**
	 * Public: Are comments enabled
	 */
	public function canComment($user = NULL) {
		if ($this->getCommentStatus()->getId() == ArticleCommentStatus::ARTICLE_COMMENTS_OFF) {
			return false;
		}

		if ($this->getCommentStatus()->getId() == ArticleCommentStatus::ARTICLE_COMMENTS_INTERNAL) {
			if ($user && $user->isLoggedIn()) {
				return true;
			} else {
				return false;
			}
		}

		if ($this->getCommentStatus()->getId() == ArticleCommentStatus::ARTICLE_COMMENTS_ON) {
			return true;
		}

		return false;
	}

	/**
	 * Public: Convert video URL to a valid video URL
	 */
	public function getVideo() {
		// This function is based on code by Wouter Sioen released under the MIT license
		// see woutersioen/sir-trevor-php
		// Regexps from http://stackoverflow.com/questions/10488943/easy-way-to-get-vimeo-id-from-a-vimeo-url
		// and http://stackoverflow.com/questions/3452546/javascript-regex-how-to-get-youtube-video-id-from-url
		if(preg_match('/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/', $this->getVideoUrl(), $matches)) {
			$data = array(
				'source' => 'youtube',
				'remote_id' => $matches[1]
			);
		} elseif(preg_match('/(?:https?:\/\/)?(?:www\.)?(?:player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $this->getVideoUrl(), $matches)) {
			$data = array(
				'source' => 'vimeo',
				'remote_id' => $matches[2]
			);
		} else {
			throw new \FelixOnline\Exceptions\InternalException('The specified video URL is not valid');
		}

		if($data['source'] == 'youtube') {
			$html = '<iframe src="//www.youtube.com/embed/' . $data['remote_id'] . '?rel=0" ';
			$html .= 'frameborder="0" allowfullscreen></iframe>' . "\n";
		} else {
			$html = '<iframe src="//player.vimeo.com/video/' . $data['remote_id'];
			$html .= '?title=0&amp;byline=0" frameborder="0"></iframe>' . "\n";
		}

		return $html;
	}

	/**
	 * Public: Get the date the article was originally published
	 * Legacy compatability function
	 */
	public function getPublished() {
		$manager = BaseManager::build('\FelixOnline\Core\ArticlePublication', 'article_publication');

		try {
			$date = $manager->filter('article = %i', array($this->getId()))
							->filter('republished = 0')
							->one();
		} catch(\FelixOnline\Exceptions\InternalException $e) {
			return null;
		}

		return($date->getPublicationDate());
	}
}

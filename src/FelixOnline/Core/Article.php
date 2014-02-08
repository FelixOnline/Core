<?php
namespace FelixOnline\Core;

use FelixOnline\Core\Type;
/*
 * Article class
 * Deals with both article retrieval and article submission
 *
 * Fields:
 *	  id			  - id of article
 *	  title		   - title of article
 *	  short_title	 - short title of article for boxes on front page [optional]
 *	  teaser		  - article teaser
 *	  author		  - first author of article, superseded by article_author table [depreciated]
 *	  category		- id of category article is in
 *	  date			- timestamp when article was added to site
 *	  approvedby	  - user who approved the article to be published
 *	  published	   - timestamp when article was published
 *	  hidden		  - if article is hidden from engine
 *	  searchable       - can article be seen by search engines?
 *	  text1		   - id of main article text
 *	  img1			- id of main article image
 *	  text2		   - id of second article text [depreciated]
 *	  img2			- id of second image text [depreciated]
 *	  img2lr		  - not quite sure [TODO]
 *	  hits			- number of views the article has had
 *	  short_desc	  - short description of article for boxes on front page [optional]
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
			'short_title' => new Type\CharField(),
			'teaser' => new Type\CharField(),
			//'author' => new ForeignKey('User'),
			'approvedby' => new Type\ForeignKey('FelixOnline\Core\User'),
			'category' => new Type\ForeignKey('FelixOnline\Core\Category'),
			'date' => new Type\DateTimeField(),
			'published' => new Type\DateTimeField(),
			'hidden' => new Type\BooleanField(),
			'searchable' => new Type\BooleanField(),
			'text1' => new Type\ForeignKey('FelixOnline\Core\Text'),
			'img1' => new Type\ForeignKey('FelixOnline\Core\Image'),
			'hits' => new Type\IntegerField(),
			'short_desc' => new Type\CharField(),
		);

		parent::__construct($fields, $id);
	}

	/*
	 * Public: Get array of authors of article
	 *
	 * Returns array
	 */
	public function getAuthors() {
		$app = App::getInstance();

		if (!$this->authors) {
			$sql = $app['safesql']->query(
				"SELECT
					article_author.author as author
				FROM `article_author`
				INNER JOIN `article`
				ON (article_author.article=article.id)
				WHERE article.id=%i",
				array(
					$this->getId()
				)
			);
			$authors = $app['db']->get_results($sql);
			foreach($authors as $author) {
				$this->authors[] = new User($author->author);
			}
		}
		return $this->authors;
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
		return $this->getText1()->getContent();
	}

	/**
	 * Private: Clean text
	 */
	private function cleanText($text) {
		$result = strip_tags($text, '<p><a><div><b><i><br><blockquote><object><param><embed><li><ul><ol><strong><img><h1><h2><h3><h4><h5><h6><em><iframe><strike>'); // Gets rid of html tags except <p><a><div>
		$result = preg_replace('#<div[^>]*(?:/>|>(?:\s|&nbsp;)*</div>)#im', '', $result); // Removes empty html div tags
		$result = preg_replace('#<span*(?:/>|>(?:\s|&nbsp;)[^>]*</span>)#im', '', $result); // Removes empty html div tags
		$result = preg_replace('#<p[^>]*(?:/>|>(?:\s|&nbsp;)*</p>)#im', '', $result); // Removes empty html p tags
		$result = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $result); // Remove style attributes
		return $result;
	}

	/**
	 * Public: Get article teaser
	 * TODO
	 *
	 * Returns string
	 */
	public function getTeaserFull() {
		if ($this->getTeaser()) {
			return str_replace('<br/>', '', strip_tags($this->getTeaser()));
		} else {
			$text = strip_tags($this->getContent());
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
		$string = strip_tags($this->getContent());
		$words = explode(" ", $string);
		$append = ' ... <br/><a href="'.$this->getURL().'" title="Read more" id="readmorelink">Read more</a>'; // TODO move into template
		return implode(" ", array_splice($words, 0, $limit)) . $append;
	}

	/*
	 * Public: Get short description
	 * If a short description is specified in the database then use that.
	 * Otherwise limit article content to a certain character length
	 *
	 * $limit - character limit for description [defaults to 80]
	 */
	public function getShortDesc($limit = 80) {
		if(array_key_exists('short_desc', $this->fields) && $this->fields['short_desc']->getValue()) {
			return substr($this->fields['short_desc']->getValue(), 0, $limit);
		} else {
			return substr(trim(strip_tags($this->getContent())), 0, $limit);
		}
	}

	/*
	 * Public: Get number of comments on article
	 *
	 * Returns int
	 */
	public function getNumComments() {
		$app = App::getInstance();

		if (!isset($this->num_comments)) {
			$sql = $app['safesql']->query(
				"SELECT
					SUM(count) AS count
				FROM (
					SELECT article,COUNT(*) AS count
					FROM `comment`
					WHERE article=%i
					AND `active`=1
					GROUP BY article
					UNION ALL
					SELECT article,COUNT(*) AS count
					FROM `comment_ext`
					WHERE article=%i
					AND `active`=1
					AND `pending`=0
					GROUP BY article
				) AS t GROUP BY article",
				array(
					$this->getId(),
					$this->getId()
				)
			);
			$this->num_comments = $app['db']->get_var($sql);
			if(!isset($this->num_comments)) $this->num_comments = 0;
		}
		return $this->num_comments;
	}

	/*
	 * Public: Get comments
	 *
	 * $ip - server ip
	 *
	 * Returns db object
	 */
	public function getComments($ip = NULL) {
		$app = App::getInstance();

		if (is_null($ip)) {
			$ip = $app['env']['REMOTE_ADDR'];
		}

		$sql = $app['safesql']->query(
			"SELECT
				id
			FROM (
				SELECT
					comment.id,
					UNIX_TIMESTAMP(comment.timestamp) AS timestamp
				FROM `comment`
				WHERE article=%i
				AND active=1 # select all internal comments
				UNION SELECT
					comment_ext.id,
					UNIX_TIMESTAMP(comment_ext.timestamp) AS timestamp
				FROM `comment_ext`
				WHERE article=%i
				AND active = 1
				AND pending  =0
				AND spam = 0 # select external comments that are not spam
				UNION SELECT
					comment_ext.id,
					UNIX_TIMESTAMP(comment_ext.timestamp) AS timestamp
					FROM `comment_ext`
				WHERE article=%i
				AND IP = '%s'
				AND active = 1
				AND pending = 1
				AND spam = 0 # select external comments that are pending and are from current ip
			) AS t
			ORDER BY timestamp ASC
			LIMIT 500",
			array(
				$this->getId(),
				$this->getId(),
				$this->getId(),
				$ip,
			)
		);
		$comments = array();
		$rsc = $app['db']->get_results($sql);
		if ($rsc) {
			foreach($rsc as $key => $obj) {
				$comments[] = new Comment($obj->id);
			}
		}
		return $comments;
	}

	/*
	 * Public: Get image class
	 */
	public function getImage() {
		return $this->getImg1();
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
			$this->hitArticle();
		} else {
			$this->logVisitor(1);
		}
	}

	/*
	 * Private: Increment hit count on article
	 */
	private function hitArticle() {
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"UPDATE
				`article`
			SET hits=hits+1
			WHERE id=%i",
			array(
				$this->getId()
			)
		);
		return $app['db']->query($sql);
	}

	/*
	 * Private: Add log of visitor into article_vist table
	 */
	private function logVisitor($repeat = 0) {
		$app = App::getInstance();

		$user = NULL;
		if ($app['currentuser']->isLoggedIn()) {
			$user = $app['currentuser']->getUser();
		}

		$sql = $app['safesql']->query(
			"INSERT INTO
				article_visit
			(
				article,
				user,
				IP,
				browser,
				referrer,
				repeat_visit
			) VALUES (%q)",
				array(
					array(
						$this->getId(),
						$user,
						$app['env']['REMOTE_ADDR'],
						$app['env']['HTTP_USER_AGENT'],
						$app['env']['HTTP_REFERER'],
						$repeat
					)
				)
			);
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
			$sql = $app['safesql']->query(
				"INSERT INTO article_author (`article`, `author`) VALUES (%i, '%s')",
				array($this->getId(), $author->getUser())
			);
			$app['db']->query($sql);
		}
		return $authors;
	}
}

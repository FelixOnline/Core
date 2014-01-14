<?php
namespace FelixOnline\Core;
/*
 * User class
 *
 * Fields:
 *	  user		-
 *	  name		-
 *	  visits	  -
 *	  ip		  -
 *	  timestamp   -
 *	  role		-
 *	  description -
 *	  email	   -
 *	  facebook	-
 *	  twitter	 -
 *	  websitename -
 *	  websiteurl  -
 *	  img		 -
 */
class User extends BaseModel {
	private $articles;
	private $count;
	private $popArticles = array();
	private $comments = array();
	private $likes;
	private $dislikes;
	private $image;
	protected $dbtable = 'user';
	protected $primaryKey = 'user';
	protected $transformers = array(
		'description' => parent::TRANSFORMER_NO_HTML,
		'email' => parent::TRANSFORMER_NO_HTML,
		'facebook' =>parent::TRANSFORMER_NO_HTML,
		'twitter' => parent::TRANSFORMER_NO_HTML,
		'websitename' => parent::TRANSFORMER_NO_HTML,
		'websiteurl' => parent::TRANSFORMER_NO_HTML
	);

	function __construct($uname = NULL) {
		$app = App::getInstance();

		if ($uname !== NULL) {
			$sql = $app['safesql']->query(
				"SELECT 
					`user`,
					`name`,
					`visits`,
					`ip`,
					UNIX_TIMESTAMP(`timestamp`) as timestamp,
					`role`,
					`info`,
					`description`,
					`email`,
					`facebook`,
					`twitter`,
					`websitename`,
					`websiteurl`,
					`img` 
				FROM `user` 
				WHERE user='%s'",
				array(
					$uname
				)
			);

			parent::__construct($app['db']->get_row($sql), $uname);
		} else {
		}
	}

	/*
	 * Public: Get url for user
	 *
	 * $page - page to link to
	 */
	public function getURL($pagenum = NULL) {
		$app = App::getInstance();

		$output = $app->getOption('base_url') . 'user/'.$this->getUser().'/'; 
		if ($pagenum != NULL) {
			$output .= $pagenum.'/';
		}
		return $output;
	}

	/*
	 * Public: Get articles
	 * Get all articles from user
	 */
	public function getArticles($page = NULL) {
		$app = App::getInstance();

		$sql = "SELECT 
				id 
			FROM `article` 
			INNER JOIN `article_author` 
				ON (article.id=article_author.article) 
			WHERE article_author.author='%s'
			AND published < NOW()
			ORDER BY article.date DESC";
		$values = array(
			$this->getUser()
		);

		if ($page) {
			$sql .= " LIMIT %i, %i";
			$values[] = ($page-1) * ARTICLES_PER_USER_PAGE;
			$values[] = ARTICLES_PER_USER_PAGE;
		}

		$sql = $app['safesql']->query($sql, $values);

		$results = $app['db']->get_results($sql);	
		$articles = array();
		
		foreach ($results as $article) {
			$articles[] = new Article($article->id);
		}

		return $articles;
	}

	/*
	 * Public: Get popular articles
	 * Get users popular articles
	 */
	public function getPopularArticles() {
		$app = App::getInstance();

		if (!$this->popArticles) {
			$sql = $app['safesql']->query(
				"SELECT 
					id 
				FROM `article` 
				INNER JOIN `article_author` 
					ON (article.id=article_author.article) 
				WHERE article_author.author='%s' 
				AND published < NOW()
				ORDER BY hits DESC LIMIT 0, %i",
				array(
					$this->getUser(),
					NUMBER_OF_POPULAR_ARTICLES_USER,
				));
			$articles = $app['db']->get_results($sql);
			foreach($articles as $key => $obj) {
				$this->popArticles[] = new Article($obj->id);
			}
		}
		return $this->popArticles;
	}

	/*
	 * Public: Get comments
	 * Get all comments from user
	 */
	public function getComments() {
		$app = App::getInstance();

		if (!$this->comments) {
			$sql = $app['safesql']->query(
				"SELECT 
					id
				FROM `comment` 
				WHERE user='%s' 
				ORDER BY timestamp DESC 
				LIMIT 0, %i",
				array(
					$this->getUser(),
					NUMBER_OF_POPULAR_COMMENTS_USER,
				));
			$comments = $app['db']->get_results($sql);	
			if($comments) {
				foreach($comments as $key => $obj) {
					$this->comments[] = new Comment($obj->id);
				} 
			}
		}
		return $this->comments;
	}

	/*
	 * Public: Get user comment popularity
	 *
	 * Returns percentage
	 */
	public function getCommentPopularity() {
		$total = $this->getLikes() + $this->getDislikes();
		if($total) {
			$popularity = 100 * ($this->getLikes() 
							/ ($this->getLikes() + $this->getDislikes()));
			return round($popularity);
		} else {
			return 0;
		}
	}

	/*
	 * Public: Get likes
	 * Get number of likes on comments by user
	 */
	public function getLikes() {
		$app = App::getInstance();

		if (!$this->likes) {
			$sql = $app['safesql']->query(
				"SELECT 
					SUM(likes) 
				FROM `comment` 
				WHERE user='%s'
				AND `active`=1",
				array(
					$this->getUser(),
				));
			$this->likes = $app['db']->get_var($sql);
		}
		return $this->likes;
	}

	/*
	 * Public: Get dislikes
	 * Get number of dislikes on comments by user
	 */
	public function getDislikes() {
		$app = App::getInstance();

		if (!$this->dislikes) {
			$sql = $app['safesql']->query(
				"SELECT 
					SUM(dislikes) 
				FROM `comment` 
				WHERE user='%s'
				AND `active`=1",
				array(
					$this->getUser(),
				));
			$this->dislikes = $app['db']->get_var($sql);
		}
		return $this->dislikes;
	}

	/*
	 * Public: Get number of pages in a category
	 *
	 * Returns int 
	 */
	public function getNumPages() {
		$app = App::getInstance();

		if (!$this->count) {
			$sql = $app['safesql']->query(
				"SELECT 
					COUNT(id) as count 
				FROM `article` 
				INNER JOIN `article_author` 
					ON (article.id=article_author.article) 
				WHERE article_author.author='%s'
				AND published < NOW()
				ORDER BY article.date DESC",
				array(
					$this->getUser()
				));
			$this->count = $app['db']->get_var($sql);
		}

		$pages = ceil(($this->count - ARTICLES_PER_USER_PAGE) / (ARTICLES_PER_USER_PAGE)) + 1;
		return $pages;
	}

	/*
	 * Public: Get first name of user
	 *
	 * Returns string
	 */
	public function getFirstName() {
		$name = explode(' ', $this->getName());
		return $name[0];
	}

	/*
	 * Public: Get last name of user
	 *
	 * Returns string
	 */
	public function getLastName() {
		$name = explode(' ', $this->getName());
		return $name[1];
	}

	/*
	 * Public: Get user info
	 * Decode json array of info
	 *
	 * Returns array
	 */
	public function getInfo() {
		return Utility::jsonDecode($this->fields['info']);
	}

	public function getFirstLogin() {
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT 
				UNIX_TIMESTAMP(timestamp) as timestamp 
			FROM `login` 
			WHERE user='%s' 
			ORDER BY timestamp ASC 
			LIMIT 1",
			array(
				$this->getUser()
			));
		$login = $app['db']->get_var($sql);
		if($login) {
			return $login;
		} else {
			return 1262304000; // 1st of January 2010
		}
	}

	public function getLastLogin() {
		$sql = $app['safesql']->query(
			"SELECT 
				UNIX_TIMESTAMP(timestamp) as timestamp 
			FROM `login` 
			WHERE user='%s' 
			ORDER BY timestamp DESC 
			LIMIT 1",
			array(
				$this->getUser()
			));
		$login = $app['db']->get_var($sql);
		if($login) {
			return $login;
		} else {
			return 1262304000; // 1st of January 2010
		}
	}

	/*
	 * Public: Has personal info
	 * Check to see whether user has personal info
	 */
	public function hasPersonalInfo() {
		if ($this->getDescription()
			|| $this->getFacebook()
			|| $this->getTwitter()
			|| $this->getEmail()
			|| $this->getWebsiteurl()
		) {
			return true;
		}
		return false;
	}

	/**
	 * Public: Get image
	 */
	public function getImage() {
		if (!$this->image) {
			if ($this->getImg()) {
				$this->image = new Image($this->getImg());
			}
		}
		return $this->image;
	}

	/*
	 * Public: has Articles Hidden from Robots
	 */
	public function hasArticlesHiddenFromRobots() {
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT
				COUNT(id)
			FROM `article_author`
			INNER JOIN `article` 
			ON article_author.article = article.id 
			WHERE article_author.`author` = '%s'
			AND searchable = 0",
			array(
				$this->getUser())
			);
		$result = $app['db']->get_var($sql);
		return ($result > 0 ? true: false);
	}
}

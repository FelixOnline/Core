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
class User extends BaseDB
{
	private $articles;
	private $count;
	private $popArticles = array();
	private $comments = array();
	private $likes;
	private $dislikes;
	private $image;
	public $dbtable = 'user';
	protected $transformers = array(
		'description' => parent::TRANSFORMER_NO_HTML,
		'email' => parent::TRANSFORMER_NO_HTML,
		'facebook' =>parent::TRANSFORMER_NO_HTML,
		'twitter' => parent::TRANSFORMER_NO_HTML,
		'websitename' => parent::TRANSFORMER_NO_HTML,
		'websiteurl' => parent::TRANSFORMER_NO_HTML
	);

	function __construct($uname = NULL) {
		$fields = array(
			'user' => new Type\CharField(array('primary' => true)),
			'name' => new Type\CharField(),
			'visits' => new Type\IntegerField(),
			'ip' => new Type\CharField(),
			'timestamp' => new Type\DateTimeField(),
			'role' => new Type\IntegerField(),
			'info' => new Type\CharField(),
			'description' => new Type\CharField(),
			'email' => new Type\CharField(),
			'facebook' => new Type\CharField(),
			'twitter' => new Type\CharField(),
			'websitename' => new Type\CharField(),
			'websiteurl' => new Type\CharField(),
			'img' => new Type\ForeignKey('FelixOnline\Core\Image'),
		);

		parent::__construct($fields, $uname);
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
		return Utility::jsonDecode($this->fields['info']->getValue());
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

<?php
namespace FelixOnline\Core;
/**
 * Category class
 *
 * Fields:
 *	  id			  - 
 *	  label		   -
 *	  cat			 -
 *	  active		  -
 *	  email		   -
 *	  twitter		 -
 *	  description	 -
 *	  hidden		  -
 */
class Category extends BaseDB
{
	private $editors = array();
	private $count; // number of articles in catgeory
	private $stories; // array of top story objects
	public $dbtable = 'category';

	function __construct($id = NULL)
	{
		$fields = array(
			'label' => new Type\CharField(),
			'cat' => new Type\CharField(),
			'active' => new Type\BooleanField(),
			'parent' => new Type\ForeignKey('FelixOnline\Core\Category'),
			'email' => new Type\CharField(),
			'twitter' => new Type\CharField(),
			'description' => new Type\TextField(),
			'hidden' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}

	/**
	 * Public: Get category url
	 */
	public function getURL($pagenum = NULL)
	{
		$app = App::getInstance();
		$output = $app->getOption('base_url').$this->getCat().'/';
		if ($pagenum != NULL) {
			$output .= $pagenum.'/';
		}
		return $output;
	}

	/**
	 * Public: Get category editors
	 *
	 * Returns array of user objects
	 */
	public function getEditors()
	{
		$editors = BaseManager::build('FelixOnline\Core\User', 'category_author', 'user')
			->filter("category = %i", array($this->getId()))
			->values();

		return $editors;
	}

	/**
	 * Public: Get category children
	 *
	 * Returns array of category objects
	 */
	public function getChildren()
	{
		$editors = BaseManager::build('FelixOnline\Core\Category', 'category', 'id')
			->filter("parent = %i", array($this->getId()))
			->values();

		return $editors;
	}

	/**
	 * Static: Get all categories
	 */
	public static function getCategories()
	{
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT
				`id`
			FROM `category`
			WHERE hidden = 0
			AND id > 0
			ORDER BY `order` ASC",
			array());
		$results = $app['db']->get_results($sql);
		$cats = array();

		if (!is_null($results)) {
			foreach($results as $cat) {
				$cats[] = new Category($cat->id);
			}
		}
		return $cats;
	}

	/**
	 * Static: Get all root categories
	 */
	public static function getRootCategories()
	{
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT
				`id`
			FROM `category`
			WHERE hidden = 0
			AND id > 0
			AND parent IS NULL
			ORDER BY `order` ASC",
			array());
		$results = $app['db']->get_results($sql);
		$cats = array();

		if (!is_null($results)) {
			foreach($results as $cat) {
				$cats[] = new Category($cat->id);
			}
		}
		return $cats;
	}
}

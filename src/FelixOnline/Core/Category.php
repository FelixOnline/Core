<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

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
			'order' => new Type\IntegerField(),
			'hidden' => new Type\BooleanField(),
			'secret' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);

		$currentuser = new CurrentUser();

		if($this->getSecret() && !$currentuser->isLoggedIn() && !Utility::isInCollege()) {
			throw new \FelixOnline\Exceptions\ModelNotFoundException("This is a secret category and you don't have permission to access it", "Category", $id);
		}
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
	 * Public: Get all parents
	 *
	 * Returns array of parents starting at the root
	 */
	public function getAllParents()
	{
		$parents = array();

		$parent = $this;

		while($parent = $parent->getParent()) {
			$parents[] = $parent;
		}

		return $parents;
	}

	/**
	 * Static: Get all categories
	 */
	public static function getCategories()
	{
		$app = App::getInstance();

		$manager = BaseManager::build('FelixOnline\Core\Category', 'category');

		try {
			$values = $manager->filter('hidden = 0')
							->filter('deleted = 0')
							->filter('id > 0')
							->order('order', 'ASC')
							->values();

			return $values;
		} catch(\Exception $e) {
			return array();
		}
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
			AND deleted = 0
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

<?php
namespace FelixOnline\Core;
/**
 * Category class
 *
 * Fields:
 *	  id			  - 
 *	  label		   -
 *	  cat			 -
 *	  uri			 - [depreciated]
 *	  colourclass	 - [depreciated]
 *	  active		  -
 *	  top_slider_1	-
 *	  top_slider_2	-
 *	  top_slider_3	-
 *	  top_slider_4	-
 *	  top_sidebar_1   -
 *	  top_sidebar_2   -
 *	  top_sidebar_3   -
 *	  top_sidebar_4   -
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
			'uri' => new Type\CharField(),
			'colourclass' => new Type\CharField(),
			'active' => new Type\BooleanField(),
			'top_slider_1' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_slider_2' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_slider_3' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_slider_4' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_sidebar_1' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_sidebar_2' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_sidebar_3' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'top_sidebar_4' => new Type\ForeignKey('FelixOnline\Core\Article'),
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
			->filter("admin = 1")
			->values();

		return $editors;
	}

	/**
	 * Get category top stories
	 *
	 * Returns array of articles
	 */
	public function getTopStories()
	{
		if (!$this->stories) {
			$this->stories = array();

			$sliders = array(
				'top_slider_1',
				'top_slider_2',
				'top_slider_3',
				'top_slider_4',
			);

			foreach($sliders as $slider) {
				$this->stories[] = $this->fields[$slider]->getValue();
			}
		}
		return $this->stories;
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
}

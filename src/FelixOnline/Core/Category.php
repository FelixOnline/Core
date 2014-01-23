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
class Category extends BaseModel
{
	protected $db;
	private $editors = array();
	private $count; // number of articles in catgeory
	private $stories; // array of top story objects

	function __construct($id = NULL) {
		$app = App::getInstance();

		if ($id !== NULL) {
			$sql = $app['safesql']->query(
				"SELECT
					id,
					label,
					cat,
					uri,
					colourclass,
					active,
					top_slider_1,
					top_slider_2,
					top_slider_3,
					top_slider_4,
					top_sidebar_1,
					top_sidebar_2,
					top_sidebar_3,
					top_sidebar_4,
					email,
					twitter,
					description,
					hidden
				FROM category
				WHERE id=%i",
				array($id)
			);
			parent::__construct($app['db']->get_row($sql), $id);
			return $this;
		} else {
		}
	}

	/**
	 * Public: Get category url
	 */
	public function getURL($pagenum = NULL) {
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
	public function getEditors() {
		$app = App::getInstance();

		if (!$this->editors) {
			$sql = $app['safesql']->query(
				"SELECT 
					user 
				FROM `category_author` 
				WHERE category=%i 
				AND admin=1",
				array(
					$this->getId()
				));
			$editors = $app['db']->get_results($sql);
			if (is_null($editors)) {
				$this->editors = null;
			} else {
				foreach ($editors as $key => $object) {
					$this->editors[] = new User($object->user);
				}
			}
		}
		return $this->editors;
	}

	/**
	 * TODO Remove? Should be done using managers
	 */

	/**
	 * Public: Get category articles
	 *
	 * $page - page number to limit article list
	 *
	 * Returns dbobject
	 */
	public function getArticles($page) {
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT 
				id 
			FROM `article` 
			WHERE published < NOW() 
			AND category=%i
			ORDER BY published DESC 
			LIMIT %i, %i",
			array(
				$this->getId(),
				($page-1) * ARTICLES_PER_CAT_PAGE,
				ARTICLES_PER_CAT_PAGE
			));
		return $app['db']->get_results($sql);
	}

	/**
	 * Public: Get number of pages in a category
	 *
	 * TODO
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
				WHERE published < NOW() 
				AND category=%i",
				array(
					$this->getId()
				));
			$this->count = $app['db']->get_var($sql);
		}
		$pages = ceil(($this->count - ARTICLES_PER_CAT_PAGE) / (ARTICLES_PER_SECOND_CAT_PAGE)) + 1;
		return $pages;
	}

	/**
	 * Get category top stories
	 *
	 * Returns array of articles
	 */
	public function getTopStories() {
		if (!$this->stories) {
			$this->stories = array();

			$sliders = array(
				'top_slider_1',
				'top_slider_2',
				'top_slider_3',
				'top_slider_4',
			);

			foreach($sliders as $slider) {
				if (!is_null($this->fields[$slider])) {
					$this->stories[] = new Article($this->fields[$slider]);
				} else {
					$this->stories[] = null;
				}
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

<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Front Page manager
 */
class FrontpageManager extends BaseManager
{
	public $table = 'frontpage';
	public $class = 'FelixOnline\Core\Frontpage';

	function getSection($section) {
		$articles = $this
				->filter('section = "%s"', array($section))
				->order('sort_order', 'ASC');

		$articleManager = BaseManager::build('FelixOnline\Core\Article', 'article');

		$categoryManager = BaseManager::build('FelixOnline\Core\Category', 'category');

		$app = App::getInstance();
		$currentuser = $app['currentuser'];

		$articleManager->join($categoryManager, null, 'category');

		$articles->join($articleManager, null, 'article');

		$articles = $articles->values();

		return $articles;
	}

	function getAll() {
		$articles = $this
				->order('sort_order', 'ASC');

		$articleManager = BaseManager::build('FelixOnline\Core\Article', 'article');

		$categoryManager = BaseManager::build('FelixOnline\Core\Category', 'category');

		$app = App::getInstance();
		$currentuser = $app['currentuser'];

		$articleManager->join($categoryManager, null, 'category');

		$articles->join($articleManager, null, 'article');

		$articles = $articles->values();

		return $articles;
	}
}

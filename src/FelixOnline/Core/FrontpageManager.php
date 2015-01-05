<?php
namespace FelixOnline\Core;
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
				->order('sort_order', 'ASC')
				->values();

		return $articles;
	}
}

<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Advert class
 */
class Advert extends BaseDB
{
	public $dbtable = 'advert';

	function __construct($id = NULL)
	{
		$fields = array(
			'details' => new Type\CharField(),
			'image' => new Type\ForeignKey('FelixOnline\Core\Image'),
			'url' => new Type\CharField(),
			'start_date' => new Type\DateTimeField(),
			'end_date' => new Type\DateTimeField(),
			'max_impressions' => new Type\IntegerField(),
			'views' => new Type\IntegerField(),
			'clicks' => new Type\IntegerField(),
			'frontpage' => new Type\BooleanField(),
			'categories' => new Type\BooleanField(),
			'articles' => new Type\BooleanField(),
			'sidebar' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}

	/**
	 * Public: Get categories that this advert is linked to
	 *
	 * Returns array of category objects
	 */
	public function getAllocatedCategories()
	{
		$categories = BaseManager::build('FelixOnline\Core\Category', 'advert_category', 'category')
			->filter('advert = %i', array($this->getId()))
			->values();

		return $categories;
	}

	public function viewAdvert() {
		$this->setViews($this->getViews() + 1)->save();

		return $this;
	}

	public function clickAdvert() {
		$this->setClicks($this->getClicks() + 1)->save();

		return $this;
	}

	public function getActive()
	{
		if($this->getMaxImpressions() <= $this->getViews()) {
			return false;
		}

		if($this->getEndDate() <= time()) {
			return false;
		}

		return true;
	}

	public static function randomPick($on = 'frontpage', $sidebar = 0, $category = null) {
		if($on != 'frontpage' && $on != 'categories' && $on != 'articles') {
			throw new \FelixOnline\Exceptions\InternalException('Trying to find advert on invalid on type');
		}

		$ads = BaseManager::build('FelixOnline\Core\Advert', 'advert')
			->filter('max_impressions > views')
			->filter('start_date < NOW()')
			->filter('end_date > NOW()')
			->filter($on.' = 1')
			->filter('sidebar = %i', array((bool) $sidebar))
			->randomise()
			->limit(0, 1);

		if($category) {
			$second = BaseManager::build('FelixOnline\Core\AdvertCategory', 'advert_category')
				->filter('category = '.$category->getId(), array(), array(array('category IS NULL', array())));

			$ads->join($second, 'LEFT OUTER', null, 'advert');
		} else {
			$second = BaseManager::build('FelixOnline\Core\AdvertCategory', 'advert_category')
				->filter('category IS NULL');

			$ads->join($second, 'LEFT OUTER', null, 'advert');
		}

		$ads = $ads->values();

		if($ads) {
			$ads = $ads[0];
		} else {
			return false;
		}

		return $ads;
	}
}

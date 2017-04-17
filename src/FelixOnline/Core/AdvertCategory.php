<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * @codeCoverageIgnore
 */

/**
 * Advert class
 */
class AdvertCategory extends BaseDB
{
	public $dbtable = 'advert_category';

	function __construct($id = NULL)
	{
		$fields = array(
			'advert' => new Type\ForeignKey('FelixOnline\Core\Advert'),
			'category' => new Type\ForeignKey('FelixOnline\Core\Category'),
		);

		parent::__construct($fields, $id);
	}
}

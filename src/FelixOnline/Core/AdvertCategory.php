<?php
namespace FelixOnline\Core;
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

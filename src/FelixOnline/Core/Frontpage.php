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

/*
 * Frontpage class
 * Represents the frontpage
 *
 * Fields:
 */
class Frontpage extends BaseDb
{
	public $dbtable = 'frontpage';

	/**
	 * Constructor
	 *
	 * @param integer $id = Frontpage slot record number
	 */
	function __construct($id = NULL)
	{
		$fields = array(
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'section' => new Type\CharField(),
			'sort_order' => new Type\IntegerField(),
		);

		parent::__construct($fields, $id);
	}
}

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
 * Link class
 * For link redirections
 */
class Link extends BaseDb
{
	public $dbtable = 'link';

	/**
	 * Constructor
	 *
	 */
	function __construct($link = NULL)
	{
		$fields = array(
			'link' => new Type\CharField(array('primary' => true)),
			'url' => new Type\CharField(),
			'active' => new Type\BooleanField()
		);

		parent::__construct($fields, $link);
	}
}

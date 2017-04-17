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
class UserRole extends BaseDB
{
	public $dbtable = 'user_roles';

	function __construct($id = NULL) {
		$fields = array(
			'user' => new Type\ForeignKey('FelixOnline\Core\User'),
			'role' => new Type\ForeignKey('FelixOnline\Core\Role'),
		);

		parent::__construct($fields, $id);
	}
}

<?php
namespace FelixOnline\Core;
/*
 * Article Poll class
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

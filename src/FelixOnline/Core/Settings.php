<?php
namespace FelixOnline\Core;
/*
 * Settings
 */
class Settings extends BaseDB {
	public $dbtable = 'settings';

	function __construct($key = NULL) {
		$fields = array(
			'setting' => new Type\CharField(array('primary' => true)),
			'description' => new Type\CharField(),
			'value' => new Type\CharField()
		);

		parent::__construct($fields, $key);
	}

	public static function get($key) {
		$setting = new static($key);

		return $setting->getValue();
	}
}


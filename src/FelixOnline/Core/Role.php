<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article Poll class
 */
class Role extends BaseDB
{
	public $dbtable = 'roles';

	function __construct($id = NULL) {
		$fields = array(
			'name' => new Type\CharField(),
			'description' => new Type\CharField(),
			'parent' => new Type\ForeignKey('FelixOnline\Core\Role'),
		);

		parent::__construct($fields, $id);
	}

	public function getChildRoles() {
		return $this->internalGetChildRoles($this->getId());
	}

	private function internalGetChildRoles($data) {
		$manager = BaseManager::build('FelixOnline\Core\Role', 'roles');

		if(is_int($parentId)) {
			$manager->filter("parent = %i", array($data));
		} else {
			$manager->filter("parent IN (%q)", array($data));
		}

		$values = $manager->values();

		if(!$values) {
			return array();
		}

		$children = array();
		$newIds = array();

		foreach($values as $value) {
			$children[] = $value;
			$newIds[] = $value->getId();
		}

		$newIds = array_unique($newIds);

		$moreChildren = $this->internalGetChildRoles($newIds);

		if($moreChildren) {
			$children = array_merge($moreChildren, $children);
		}

		return $children;
	}
}

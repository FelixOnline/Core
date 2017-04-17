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
 * Category manager
 */
class CategoryManager extends BaseManager
{
	public $table = 'category';
	public $class = 'FelixOnline\Core\Category';
}

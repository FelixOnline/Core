<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Category manager
 */

/**
 * @codeCoverageIgnore
 */
class CategoryManager extends BaseManager
{
    public $table = 'category';
    public $class = 'FelixOnline\Core\Category';
}

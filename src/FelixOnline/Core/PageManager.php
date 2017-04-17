<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Page manager
 */
class PageManager extends BaseManager
{
    public $table = 'pages';
    public $class = 'FelixOnline\Core\Page';
}

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
 * Comment manager
 */
class CommentManager extends BaseManager
{
	public $table = 'comment';
	public $class = 'FelixOnline\Core\Comment';
}

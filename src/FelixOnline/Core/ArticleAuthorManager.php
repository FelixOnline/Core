<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Article Author manager
 */

/**
 * @codeCoverageIgnore
 */
class ArticleAuthorManager extends BaseManager
{
	public $table = 'article_author';
	public $pk = 'article';
}

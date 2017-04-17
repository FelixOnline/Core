<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article Author Class
 */

/**
 * @codeCoverageIgnore
 */
class ArticleAuthor extends BaseDb {
	public $dbtable = 'article_author';

	function __construct($id = NULL) {
		$fields = array(
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'author' => new Type\ForeignKey('FelixOnline\Core\User')
		);

		parent::__construct($fields, $id);
	}
}

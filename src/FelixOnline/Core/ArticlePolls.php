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

/**
 * @codeCoverageIgnore
 */
class ArticlePolls extends BaseDB
{
	public $dbtable = 'article_polls';

	function __construct($id = NULL) {
		$fields = array(
			'poll' => new Type\ForeignKey('FelixOnline\Core\Poll'),
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
		);

		parent::__construct($fields, $id);
	}
}

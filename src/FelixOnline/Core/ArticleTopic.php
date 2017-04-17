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

/*
 * Article Topic class
 */
class ArticleTopic extends BaseDB
{
	public $dbtable = 'article_topic';

	function __construct($id = NULL) {
		$fields = array(
			'topic' => new Type\ForeignKey('FelixOnline\Core\Topic'),
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
		);

		parent::__construct($fields, $id);
	}
}

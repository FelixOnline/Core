<?php
namespace FelixOnline\Core;
/*
 * Article Poll class
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

<?php
namespace FelixOnline\Core;
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

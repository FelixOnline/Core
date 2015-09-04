<?php
namespace FelixOnline\Core;
/*
 * Article Author Class
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


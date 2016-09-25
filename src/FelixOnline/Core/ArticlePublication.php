<?php
namespace FelixOnline\Core;
/*
 * Article - Publication History
 */

class ArticlePublication extends BaseDb {
	public $dbtable = 'article_publication';

	function __construct($id = NULL)
	{
		$fields = array(
			'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
			'publication_date' => new Type\DateTimeField(),
			'published_by' => new Type\ForeignKey('FelixOnline\Core\User'),
			'republished' => new Type\BooleanField()
		);

		parent::__construct($fields, $id);
	}
}

<?php
namespace FelixOnline\Core;
/*
 * Article comment status (for admin pages)
 */
class ArticleCommentStatus extends BaseDb {
	public $dbtable = 'article_comment_status';

	const ARTICLE_COMMENTS_OFF = 0;
	const ARTICLE_COMMENTS_ON = 1;
	const ARTICLE_COMMENTS_INTERNAL = 2;

	function __construct($id = NULL) {
		$fields = array(
			'description' => new Type\CharField()
		);

		parent::__construct($fields, $id);
	}
}

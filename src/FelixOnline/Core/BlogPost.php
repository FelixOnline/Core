<?php
namespace FelixOnline\Core;
/*
 * Blog post class
 *
 * Fields:
 *	  id:		 - id of blog post
 *	  blog:	   - id of blog that post is on
 *	  content:	- content of post
 *	  timestamp:  - timestamp of post (updates on modification)
 *	  author:	 - uname of post author
 *	  type:	   - type of post [optional]
 *	  meta:	   - JSON encoded array of post meta [optional]
 *	  visible:	-
 */
class BlogPost extends BaseDb {
	public $dbtable = 'blog_post';

	function __construct($id = NULL) {
		$fields = array(
			'blog' => new Type\ForeignKey('FelixOnline/Core/Blog'),
			'content' => new Type\TextField(),
			'timestamp' => new Type\DateTimeField(),
			'author' => new Type\ForeignKey('FelixOnline/Core/User'),
			'type' => new Type\CharField(array('null' => true)),
			'meta' => new Type\TextField(array('null' => true)),
			'visible' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}
}

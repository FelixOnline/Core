<?php
namespace FelixOnline\Core;
/*
 * Blog Class
 *
 * Fields:
 *	  id:		 - id of page
 *	  name:	   - name of blog
 *	  slug:	   - url slug of page
 *	  controller: - name of controller used to handle blog
 *	  sticky:	 -
 */
class Blog extends BaseDb {
	public $dbtable = 'blogs';

	function __construct($id = NULL) {
		$fields = array(
			'name' => new Type\CharField(),
			'slug' => new Type\CharField(),
			'controller' => new Type\CharField(array('null' => true)),
			'sticky' => new Type\TextField(),
		);

		parent::__construct($fields, $id);
	}
}


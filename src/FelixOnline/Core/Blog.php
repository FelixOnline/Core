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
			'sprinkler_prefix' => new Type\CharField()
		);

		parent::__construct($fields, $id);
	}
}


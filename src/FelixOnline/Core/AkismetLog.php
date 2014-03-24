<?php
namespace FelixOnline\Core;
/**
 * Akismet log entry
 *
 * Fields:
 *	  id			  - 
 *	  comment_id
 *    timestamp
 *    action
 *    is_spam
 *    error
 *    request
 *    response
 */
class Category extends BaseDB
{
	public $dbtable = 'akismet_log';

	function __construct($id = NULL)
	{
		$fields = array(
			'comment_id' => new Type\ForeignKey('FelixOnline\Core\Comment'),
			'timestamp' => new Type\DateTimeField(),
			'action' => new Type\CharField(),
			'is_spam' => new Type\BooleanField(),
			'error' => new Type\TextField(),
			'request' => new Type\TextField(),
			'response' => new Type\TextField(),
		);

		parent::__construct($fields, $id);
	}
}

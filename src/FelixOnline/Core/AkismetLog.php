<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Akismet log entry
 *
 * Fields:
 *	  id
 *	  comment_id
 *    timestamp
 *    action
 *    is_spam
 *    error
 */

 /**
  * @codeCoverageIgnore
  */
class AkismetLog extends BaseDB
{
	public $dbtable = 'akismet_log';

	function __construct($id = NULL)
	{
		$fields = array(
			'comment_id' => new Type\ForeignKey('FelixOnline\Core\Comment'),
			'timestamp' => new Type\DateTimeField(),
			'action' => new Type\CharField(),
			'is_spam' => new Type\BooleanField(),
			'error' => new Type\TextField()
		);

		parent::__construct($fields, $id, null, true);
	}
}

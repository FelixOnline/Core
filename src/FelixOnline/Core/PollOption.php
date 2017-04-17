<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Poll Option class
 */

/**
 * @codeCoverageIgnore
 */
class PollOption extends BaseDB
{
	public $dbtable = 'polls_option';

	function __construct($id = NULL) {
		$fields = array(
			'poll' => new Type\ForeignKey('FelixOnline\Core\Poll'),
			'text' => new Type\TextField(),
		);

		parent::__construct($fields, $id);
	}
}

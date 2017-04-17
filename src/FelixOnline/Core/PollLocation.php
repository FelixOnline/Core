<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * @codeCoverageIgnore
 */

/*
 * Poll location (for admin pages)
 */
class PollLocation extends BaseDb {
	public $dbtable = 'polls_location';

	const POLL_BOTTOM = 0;
	const POLL_TOP = 1;
	const POLL_BOTH = 2;

	function __construct($id = NULL) {
		$fields = array(
			'description' => new Type\CharField()
		);

		parent::__construct($fields, $id);
	}
}

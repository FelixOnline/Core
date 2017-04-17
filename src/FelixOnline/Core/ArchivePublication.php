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
 * Issue Archive - Publication
 */

class ArchivePublication extends BaseDb {
	public $dbtable = 'archive_publication';

	function __construct($id = NULL)
	{
		$fields = array(
			'name' => new Type\CharField(),
			'inactive' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}
}

<?php
namespace FelixOnline\Core;
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

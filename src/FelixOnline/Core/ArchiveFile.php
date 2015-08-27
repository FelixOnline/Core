<?php
namespace FelixOnline\Core;
/*
 * Issue Archive - File
 */

class ArchiveFile extends BaseDb {
	public $dbtable = 'archive_file';

	function __construct($id = NULL)
	{
		$fields = array(
			'issue_id' => new Type\ForeignKey('FelixOnline\Core\ArchivePublication')
			'part' => new Type\CharField(),
			'filename' => new Type\CharField(),
			'content' => new Type\TextField()
		);

		parent::__construct($fields, $id);
	}
}

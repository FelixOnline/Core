<?php
namespace FelixOnline\Core;
/*
 * Issue Archive - Issue
 */

class ArchiveIssue extends BaseDb {
	public $dbtable = 'archive_issue';

	function __construct($id = NULL)
	{
		$fields = array(
			'issue' => new Type\IntegerField(),
			'date' => new Type\DateTimeField(),
			'publication' => new Type\ForeignKey('FelixOnline\Core\ArchivePublication'),
			'inactive' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}
}

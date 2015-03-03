<?php
namespace FelixOnline\Core;
/*
 * Poll Option class
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

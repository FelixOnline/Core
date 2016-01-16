<?php
namespace FelixOnline\Core;
/*
 * Poll Response class
 */
class PollResponse extends BaseDB
{
	public $dbtable = 'polls_response';

	function __construct($id = NULL) {
		$fields = array(
			'poll' => new Type\ForeignKey('FelixOnline\Core\Poll'),
			'option' => new Type\ForeignKey('FelixOnline\Core\PollOption'),
			'ip' => new Type\TextField(),
			'useragent' => new Type\TextField(),
		);

		parent::__construct($fields, $id, null, true);
	}
}

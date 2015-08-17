<?php
namespace FelixOnline\Core;
/*
 * Text class
 */
class Text extends BaseDB
{
	public $dbtable = 'text_story';

	/**
	 * Constructor for Text class
	 *
	 * $id - ID of text (optional)
	 */
	function __construct ($id = NULL) {

		$fields = array(
			'user' => new Type\ForeignKey('FelixOnline\Core\User'),
			'content' => new Type\TextField(),
			'timestamp' => new Type\DateTimeField(),
			'converted' => new Type\BooleanField(),
		);

		parent::__construct($fields, $id);
	}
}

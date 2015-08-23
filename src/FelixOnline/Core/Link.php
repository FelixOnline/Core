<?php
namespace FelixOnline\Core;
/*
 * Link class
 * For link redirections
 */
class Link extends BaseDb
{
	public $dbtable = 'Link';

	/**
	 * Constructor
	 *
	 */
	function __construct($link = NULL)
	{
		$fields = array(
			'link' => new Type\CharField(array('primary' => true)),
			'url' => new Type\CharField(),
			'active' => new Type\BooleanField()
		);

		parent::__construct($fields, $link);
	}
}


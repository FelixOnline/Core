<?php
namespace FelixOnline\Core;

class CategoryAuthor extends BaseDB
{
	public $dbtable = 'category_author';

	function __construct($id = NULL)
	{
		$fields = array(
			'category' => new Type\ForeignKey('FelixOnline\Core\Category'),
			'user' => new Type\ForeignKey('FelixOnline\Core\User')
		);

		parent::__construct($fields, $id);
	}
}
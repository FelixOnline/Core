<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../../constants.php';

class LinkTest extends AppTestCase
{
	public $fixtures = array(
		'links'
	);

	public function testGetActiveLink()
	{
		$link = new \FelixOnline\Core\Link('active');

		$this->assertEquals($link->getActive(), true);
	}

	public function testGetInctiveLink()
	{
		$link = new \FelixOnline\Core\Link('inactive');

		$this->assertEquals($link->getActive(), false);
	}
}

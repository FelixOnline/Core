<?php

require_once __DIR__ . '/../../AppTestCase.php';

class LinkTest extends AppTestCase
{
	public $fixtures = array(
		'links',
		'audit_log'
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

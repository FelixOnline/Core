<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class ImageTest extends DatabaseTestCase
{
	public $fixtures = array(
		'images',
	);

	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));
	}

	public function testGetName()
	{
		$image = new \FelixOnline\Core\Image(22);

		$this->assertEquals($image->getName(), '201001081608-felix-SimonSin.jpg');
	}

	public function testIsTall()
	{
		$image = new \FelixOnline\Core\Image(22);

		$this->assertTrue($image->isTall());

		$image = new \FelixOnline\Core\Image(23);

		$this->assertFalse($image->isTall());
	}
}

<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../../constants.php';

class ImageTest extends AppTestCase
{
	public $fixtures = array(
		'images',
	);

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

	public function testGetURL()
	{
		$image = new \FelixOnline\Core\Image(22);

		$this->assertEquals($image->getURL(), 'http://img.felixonline.co.uk/upload/201001081608-felix-SimonSin.jpg');
		$this->assertEquals($image->getURL(400), 'http://img.felixonline.co.uk/400/201001081608-felix-SimonSin.jpg');
		$this->assertEquals($image->getURL(400, 500), 'http://img.felixonline.co.uk/400/500/201001081608-felix-SimonSin.jpg');
	}
}

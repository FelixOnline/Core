<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class AdvertCategoryTest extends AppTestCase
{
	public $fixtures = array(
		'categories',
		'adverts',
		'advert_categories',
		'images'
	);

	public function testGetAdvert()
	{
		$advert = new \FelixOnline\Core\AdvertCategory(1);

		$this->assertEquals($advert->getAdvert()->getId(), 1);
	}

	public function testGetCategory()
	{
		$advert = new \FelixOnline\Core\AdvertCategory(2);

		$this->assertEquals($advert->getCategory()->getId(), 2);
	}
}

<?php

require_once __DIR__ . '/../../AppTestCase.php';

class AdvertTest extends AppTestCase
{
    public $fixtures = array(
        'categories',
        'adverts',
        'advert_categories',
        'images',
        'audit_log'
    );

    public function testGetDetails()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getDetails(), 'Test Advert One');
    }

    public function testGetImage()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getImage()->getId(), 22);
    }

    public function testGetUrl()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getUrl(), "http://www.google.com");
    }

    public function testGetStartDate()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getStartDate(), strtotime("2015-08-08 12:08:00"));
    }

    public function testGetEndDate()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getEndDate(), strtotime("2016-08-08 12:08:00"));
    }

    public function testGetMaxImpressions()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getMaxImpressions(), 100);
    }

    public function testGetViews()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getViews(), 10);
    }

    public function testGetClicks()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getClicks(), 1);
    }

    public function testGetFrontpage()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getFrontpage(), 1);
    }

    public function testGetCategories()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getCategories(), 0);
    }

    public function testGetArticles()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $this->assertEquals($advert->getArticles(), 1);
    }

    public function testGetAllocatedCategories()
    {
        $advert = new \FelixOnline\Core\Advert(1);

        $categories = $advert->getAllocatedCategories();

        $this->assertCount(2, $categories);
        $this->assertInstanceOf('FelixOnline\Core\Category', $categories[0]);
        $this->assertEquals($categories[0]->getLabel(), 'News');
    }

    public function testGetAllocatedCategoriesNull()
    {
        $advert = new \FelixOnline\Core\Advert(2);

        $categories = $advert->getAllocatedCategories();

        $this->assertNull($categories);
    }

    public function testViewAdvert()
    {
        $advert = new \FelixOnline\Core\Advert(2);

        $this->assertEquals($advert->getViews(), 20);

        $advert = $advert->viewAdvert();

        $this->assertEquals($advert->getViews(), 21);
    }

    public function testClickAdvert()
    {
        $advert = new \FelixOnline\Core\Advert(2);

        $this->assertEquals($advert->getClicks(), 5);

        $advert = $advert->clickAdvert();

        $this->assertEquals($advert->getClicks(), 6);
    }
}

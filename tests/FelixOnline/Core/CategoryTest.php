<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class CategoryTest extends DatabaseTestCase
{
	public $fixtures = array(
		'articles',
		'categories',
		'users',
		'category_authors',
	);

	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));
	}

	public function testGetURL()
	{
		$category = new \FelixOnline\Core\Category(1);

		$this->assertEquals($category->getURL(), 'http://localhost/news/');
	}

	public function testGetURLPage()
	{
		$category = new \FelixOnline\Core\Category(1);

		$this->assertEquals($category->getURL(2), 'http://localhost/news/2/');
	}

	public function testGetEditors()
	{
		$category = new \FelixOnline\Core\Category(1);

		$editors = $category->getEditors();

		$this->assertCount(2, $editors);
		$this->assertInstanceOf('FelixOnline\Core\User', $editors[0]);
		$this->assertEquals($editors[0]->getUser(), 'felix');
	}

	public function testGetEditorsNull()
	{
		$category = new \FelixOnline\Core\Category(2);

		$editors = $category->getEditors();
		$this->assertNull($editors);
	}

	public function testGetCategories()
	{
		$categories = \FelixOnline\Core\Category::getCategories();

		$this->assertCount(2, $categories);
		$this->assertInstanceOf('FelixOnline\Core\Category', $categories[0]);
		$this->assertEquals($categories[0]->getLabel(), 'News');
	}
}

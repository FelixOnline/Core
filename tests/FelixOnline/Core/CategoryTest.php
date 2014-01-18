<?php

require_once __DIR__ . '/../../AppTestCase.php';

class CategoryTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'categories',
		'users',
		'category_authors',
	);

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

	public function testGetTopStories()
	{
		$category = new \FelixOnline\Core\Category(1);

		$topStories = $category->getTopStories();
		$this->assertCount(4, $topStories);
		$this->assertInstanceOf('FelixOnline\Core\Article', $topStories[0]);
		$this->assertEquals($topStories[0]->getTitle(), 'Fighting for Libel Reform');
		$this->assertNull($topStories[3]);
	}

	public function testGetCategories()
	{
		$categories = \FelixOnline\Core\Category::getCategories();

		$this->assertCount(2, $categories);
		$this->assertInstanceOf('FelixOnline\Core\Category', $categories[0]);
		$this->assertEquals($categories[0]->getLabel(), 'News');
	}
}

<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';
require_once __DIR__ . '/../../../constants.php';

class UserTest extends DatabaseTestCase
{
	public $fixtures = array(
		'users',
		'articles',
		'article_authors',
		'comments',
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
		$user = new \FelixOnline\Core\User('felix');

		$this->assertEquals($user->getURL(), "http://localhost/user/felix/");

		$this->assertEquals($user->getURL(1), "http://localhost/user/felix/1/");
	}

	public function testGetArticles()
	{
		$user = new \FelixOnline\Core\User('felix');

		$articles = $user->getArticles();
		$this->assertCount(3, $articles);
		$this->assertInstanceOf('FelixOnline\Core\Article', $articles[0]);
	}

	public function testGetArticlesPage()
	{
		$user = new \FelixOnline\Core\User('felix');

		$articles = $user->getArticles(1);
		$this->assertCount(3, $articles);
		$this->assertInstanceOf('FelixOnline\Core\Article', $articles[0]);
	}

	public function testGetPopularArticles()
	{
		$user = new \FelixOnline\Core\User('felix');

		$articles = $user->getPopularArticles();
		$this->assertCount(3, $articles);
		$this->assertInstanceOf('FelixOnline\Core\Article', $articles[0]);
	}

	public function testGetComments()
	{
		$user = new \FelixOnline\Core\User('felix');

		$comments = $user->getComments();
		$this->assertCount(1, $comments);
		$this->assertInstanceOf('FelixOnline\Core\Comment', $comments[0]);
	}

	public function testGetLikes()
	{
		$user = new \FelixOnline\Core\User('jk708');

		$likes = $user->getLikes();
		$this->assertEquals(1, $likes);
	}

	public function testGetDislikes()
	{
		$user = new \FelixOnline\Core\User('jk708');

		$likes = $user->getDislikes();
		$this->assertEquals(0, $likes);
	}

	public function testGetCommentPopularity()
	{
		$user = new \FelixOnline\Core\User('jk708');

		$popularity = $user->getCommentPopularity();
		$this->assertEquals(100.0, $popularity);

		$user = new \FelixOnline\Core\User('felix');
		$popularity = $user->getCommentPopularity();
		$this->assertEquals(0, $popularity);
	}

	public function testGetNumPages()
	{
		$user = new \FelixOnline\Core\User('felix');

		$pages = $user->getNumPages();
		$this->assertEquals(1, $pages);
	}

	public function testFirstName()
	{
		$user = new \FelixOnline\Core\User('felix');

		$this->assertEquals('Joseph', $user->getFirstName());
	}

	public function testLastName()
	{
		$user = new \FelixOnline\Core\User('felix');

		$this->assertEquals('Letts', $user->getLastName());
	}

	public function testGetInfo()
	{
		$user = new \FelixOnline\Core\User('felix');

		$this->assertFalse($user->getInfo());

		$user = new \FelixOnline\Core\User('jk708');

		$this->assertEquals(['Alumni'], $user->getInfo());
	}

	public function testHasArticlesHiddenFromRobots()
	{
		$user = new \FelixOnline\Core\User('felix');
		$this->assertTrue($user->hasArticlesHiddenFromRobots());

		$user = new \FelixOnline\Core\User('jk708');
		$this->assertFalse($user->hasArticlesHiddenFromRobots());
	}
}

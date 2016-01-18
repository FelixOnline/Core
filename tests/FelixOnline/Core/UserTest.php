<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../../constants.php';

class UserTest extends AppTestCase
{
	public $fixtures = array(
		'users',
		'articles',
		'article_authors',
		'comments',
		'images',
		'audit_log'
	);

	public function testGetURL()
	{
		$user = new \FelixOnline\Core\User('felix');

		$this->assertEquals($user->getURL(), "http://localhost/user/felix/");

		$this->assertEquals($user->getURL(1), "http://localhost/user/felix/1/");
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

	public function testGetImage()
	{
		$user = new \FelixOnline\Core\User('jk708');
		$image = $user->getImage();

		$this->assertInstanceOf('FelixOnline\Core\Image', $image);
	}
}

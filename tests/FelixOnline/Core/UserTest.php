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
}

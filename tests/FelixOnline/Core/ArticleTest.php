<?php

require_once __DIR__ . '/../../../lib/SafeSQL.php';
require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class ArticleTest extends DatabaseTestCase
{
	public $fixtures = array(
		'articles'
	);

	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));
	}

	public function testGetTitle()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals($article->getTitle(), 'Fighting for Libel Reform');
	}
}

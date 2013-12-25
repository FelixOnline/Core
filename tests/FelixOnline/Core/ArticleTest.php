<?php

require_once __DIR__ . '/../../../lib/SafeSQL.php';
require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class ArticleTest extends DatabaseTestCase
{
	public $fixtures = array(
		'articles',
		'categories',
		'text_stories',
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

	public function testGetURL()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals($article->getURL(), 'http://localhost/news/1/fighting-for-libel-reform/');
	}

	public function testGetContent()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getContent(),
			"<p>As Imperial alumnus Simon Singh prepares to return to College to give a guest lecture on the libel laws in science of which he has fallen foul of, the Government have announced the commencement of a working group on libel reform.</p>"
		);
	}

	public function testGetTeaserFull()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getTeaserFull(),
			"In light of Simon Singh returning to Imperial to give a lecture on libel laws that he is personally embroiled in!"
		);
	}

	public function testGetTeaserFromContent()
	{
		$article = new \FelixOnline\Core\Article(2);
		$this->assertEquals(
			$article->getTeaserFull(),
			"All I bloody hear is the clock ticking. We all know what that sounds like, and we all know what the stabbing sounds of each second, &lsquo;tick&rsquo; or &lsquo;tock&rsquo;, means. Time is slipping..."
		);
	}
}

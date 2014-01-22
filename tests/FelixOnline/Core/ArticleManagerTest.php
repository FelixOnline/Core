<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class ArticleManagerTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'article_visits',
		'text_stories',
		'comments',
		'comments_ext',
	);

	public function testMostPopularQuery()
	{
		$manager = new \FelixOnline\Core\ArticleManager();

		// create a new article
		$article = new \FelixOnline\Core\Article();
		$article->setTitle('Hello');
		$article->setContent('Hello World!');
		$article->setTeaser('Hello!');
		$article->setCategory(1);
		$article->setPublished(date('Y-m-d H:i:s'));
		$article->save();

		$article->logVisit();

		$a2 = new \FelixOnline\Core\Article();
		$a2->setTitle('Hello2');
		$a2->setContent('Hello World!');
		$a2->setTeaser('Hello2!');
		$a2->setCategory(1);
		$a2->setPublished(date('Y-m-d H:i:s'));
		$a2->save();

		$a2->logVisit();
		$a2->logVisit();

		$articles = $manager->getMostPopular(5);

		$this->assertCount(2, $articles);
		$this->assertInstanceOf('FelixOnline\Core\Article', $articles[0]);
		$this->assertEquals($articles[0]->getTitle(), 'Hello2');
	}

	public function testMostPopularQueryNone()
	{
		$manager = new \FelixOnline\Core\ArticleManager();

		$articles = $manager->getMostPopular(5);

		$this->assertNull($articles);
	}

	public function testMostCommentedQueryNone()
	{
		$manager = new \FelixOnline\Core\ArticleManager();

		$articles = $manager->getMostCommented(5);

		$this->assertNull($articles);
	}
}

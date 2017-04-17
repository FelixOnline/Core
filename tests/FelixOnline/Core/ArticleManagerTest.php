<?php

require_once __DIR__ . '/../../AppTestCase.php';

class ArticleManagerTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'article_visits',
		'article_publication',
		'users',
		'categories',
		'text_stories',
		'comments',
		'audit_log'
	);

	public function testMostPopularQuery()
	{
		$manager = new \FelixOnline\Core\ArticleManager();

		$faker = Faker\Factory::create();

		$title1 = $faker->sentence();
		$title2 = $faker->sentence();

		$content1 = $faker->text();
		$content2 = $faker->text();

		// create a new article
		$article = new \FelixOnline\Core\Article();

		$article->setTitle($title1)
			->setContent($content1)
			->setTeaser($title1)
			->setCategory(1)
			->save();

		$articlevisit = new \FelixOnline\Core\ArticlePublication();

		$articlevisit->setArticle($article)
			->setPublicationDate(time())
			->setPublishedBy((new \FelixOnline\Core\User('felix')))
			->setRepublished(0)
			->save();

		$article->logVisit();

		$article2 = new \FelixOnline\Core\Article();

		$article2->setTitle($title2)
			->setContent($content2)
			->setTeaser($title2)
			->setCategory(1)
			->save();

		$articlevisit = new \FelixOnline\Core\ArticlePublication();

		$articlevisit->setArticle($article2)
			->setPublicationDate(time())
			->setPublishedBy((new \FelixOnline\Core\User('felix')))
			->setRepublished(0)
			->save();

		$article2->logVisit();
		$article2->logVisit();

		$articles = $manager->getMostPopular(5);

		$this->assertCount(2, $articles);
		$this->assertInstanceOf('FelixOnline\Core\Article', $articles[0]);
		$this->assertEquals($articles[0]->getTitle(), $title2);
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

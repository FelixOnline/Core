<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class BaseManagerTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
	);

	public function getManager()
	{
		$manager = $this->mock('FelixOnline\\Core\\BaseManager')
			->new();

		$manager->this()->table = 'article';
		$manager->this()->class = 'FelixOnline\\Core\\Article';

		return $manager;
	}

	public function testSQL()
	{
		$manager = $this->getManager();

		$manager->filter("title IS NOT NULL")
			->filter("category IS NOT NULL")
			->order('id', 'DESC')
			->limit(0, 10);

		$sql = $manager->getSQL();

		$this->assertEquals($sql, 'SELECT `id` FROM `article` WHERE title IS NOT NULL AND category IS NOT NULL ORDER BY `id` DESC LIMIT 0, 10');
	}

	public function testAll()
	{
		$manager = $this->getManager();

		$all = $manager->all();

		$this->assertCount(3, $all);
		$this->assertInstanceOf('FelixOnline\Core\Article', $all[0]);
	}

	public function testFilter()
	{
		$manager = $this->getManager();

		$filtered = $manager->filter('published IS NOT NULL')
			->filter('`id` IN (1, 2)')
			->values();

		$this->assertCount(2, $filtered);
		$this->assertInstanceOf('FelixOnline\Core\Article', $filtered[0]);
	}

	public function testOrder()
	{
		$manager = $this->getManager();

		$query = $manager->filter('published IS NOT NULL')
			->filter('`id` IN (1, 2)')
			->order('id', 'ASC');

		$results = $query->values();

		$this->assertCount(2, $results);
		$this->assertInstanceOf('FelixOnline\Core\Article', $results[0]);
		$this->assertEquals($results[0]->getId(), 1);
	}

	public function testOrderMultiple()
	{
		$manager = $this->getManager();

		$query = $manager->order(array('id', 'title'), 'DESC');

		$sql = $manager->getSQL();

		$this->assertEquals($sql, "SELECT `id` FROM `article` ORDER BY `id`,`title` DESC");
	}

	public function testLimit()
	{
		$manager = $this->getManager();

		$query = $manager->filter('published IS NOT NULL')
			->filter('published < NOW()')
			->order('id', 'ASC');

		$query->limit(0, 1);

		$results = $query->values();

		$this->assertCount(1, $results);
	}

	public function testCount()
	{
		$manager = $this->getManager();

		$query = $manager->filter('published IS NOT NULL')
			->filter('`id` IN (1, 2)')
			->order('id', 'ASC');

		$count = $query->count();

		$this->assertEquals($count, 2);
	}

	public function testQueryExceptionsBadQuery()
	{
		$manager = $this->getManager();

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException'
		);
		$manager->filter('not valid sql')->values();
	}

	public function testQueryExceptionsNoResults()
	{
		$manager = $this->getManager();

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'DB query returned no results'
		);

		$manager->filter('true = false')->values();
	}
}

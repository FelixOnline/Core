<?php
require __DIR__ . '/../../../lib/SafeSQL.php';
require __DIR__ . '/../../DatabaseTestCase.php';

class AppTest extends DatabaseTestCase
{
	public function createApp($config)
	{
		$db = new \ezSQL_mysqli();
		$db->quick_connect(
			'root',
			'',
			'test_media_felix',
			'localhost',
			3306,
			'utf8'
		);

		$safesql = new \SafeSQL_MySQLi($db->dbh);
		return new \FelixOnline\Core\App($config, $db, $safesql);
	}

	public function testApp()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo'	
		));
		$this->assertInstanceOf('FelixOnline\Core\App', $app);
	}

	public function testSingleton()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo'	
		));

		$this->assertEquals($app, \FelixOnline\Core\App::getInstance());	
	}

	public function testAccessBeforeInit()
	{
		\FelixOnline\Core\App::setInstance(null);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'App has not been initialised yet'
		);

		$app = \FelixOnline\Core\App::getInstance();
	}

	public function testRequiredOptions()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'"base_url" option has not been defined'
		);

		$app = $this->createApp(array());
	}

	public function testGetOption()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo'
		));

		$this->assertEquals($app->getOption('base_url'), 'foo');
	}

	public function testGetOptionDefault()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo'
		));
		$this->assertEquals($app->getOption('foo', 'bar'), 'bar');
	}

	public function testGetOptionException()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'Option "bar" has not been set'
		);

		$app = $this->createApp(array(
			'base_url' => 'foo',
		));

		$app->getOption('bar');
	}

	public function testQuery()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo',
		));

		$this->assertEquals(
			"SELECT id FROM foo",
			$app->query("SELECT id FROM %s", array("foo"))
		);
	}
}

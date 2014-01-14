<?php
require_once __DIR__ . '/../../../lib/SafeSQL.php';
require_once __DIR__ . '/../../DatabaseTestCase.php';

class AppTest extends DatabaseTestCase
{
	public function createApp($config)
	{
		$app = new \FelixOnline\Core\App($config);

		$db = new \ezSQL_mysqli();
		$db->quick_connect(
			'root',
			'',
			'test_media_felix',
			'localhost',
			3306,
			'utf8'
		);
		$app['db'] = $db;

		$app['safesql'] = new \SafeSQL_MySQLi($db->dbh);
		$app['env'] = \FelixOnline\Core\Environment::mock();

		$app->run();

		return $app;
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

	public function testRunNoDbException()
	{
		\FelixOnline\Core\App::setInstance(null);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'No db setup'
		);

		$app = new \FelixOnline\Core\App(array(
			'base_url' => 'foo'
		));
		$app->run();
	}

	public function testRunWrongDbTypeException()
	{
		\FelixOnline\Core\App::setInstance(null);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'No db setup'
		);

		$app = new \FelixOnline\Core\App(array(
			'base_url' => 'foo'
		));
		$app['db'] = 'foo';
		$app->run();
	}

	public function testRunNoSafesqlException()
	{
		\FelixOnline\Core\App::setInstance(null);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'No safesql setup'
		);

		$app = new \FelixOnline\Core\App(array(
			'base_url' => 'foo'
		));

		$db = new \ezSQL_mysqli();
		$app['db'] = $db;
		$app->run();
	}

	public function testRunNoWrongSafesqlException()
	{
		\FelixOnline\Core\App::setInstance(null);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'No safesql setup'
		);

		$app = new \FelixOnline\Core\App(array(
			'base_url' => 'foo'
		));

		$db = new \ezSQL_mysqli();
		$app['db'] = $db;
		$app['safesql'] = 'foo';
		$app->run();
	}

	public function testQuery()
	{
		$app = $this->createApp(array(
			'base_url' => 'foo',
		));

		$this->assertEquals(
			"SELECT id FROM foo",
			$app['safesql']->query("SELECT id FROM %s", array("foo"))
		);
	}
}

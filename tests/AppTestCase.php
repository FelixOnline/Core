<?php

require_once __DIR__ . '/DatabaseTestCase.php';
require_once __DIR__ . '/../lib/SafeSQL.php';

/**
 * App test case
 */
class AppTestCase extends DatabaseTestCase
{
	use \Xpmock\TestCaseTrait;

	protected $appConfig = array();
	protected $app;
	protected $setCurrentUser = true; // whether to initialise a current user or not

	public function setUp()
	{
		parent::setUp();

		// create app
		$config = $this->appConfig + array(
			'base_url' => 'http://localhost/'
		);

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

		$session = $this->mock('FelixOnline\\Core\\Session')
			->getId(1)
			->start(1)
			->reset()
			->new();

		$this->reflect($session)
			->__set('session', array());

		$app['env']['session'] = $session;

		$cookies = $this->mock('FelixOnline\\Core\\Cookies')
			->set(true)
			->delete(true)
			->new();

		$this->reflect($cookies)
			->__set('cookies', array());

		$app['env']['cookies'] = $cookies;

		if ($this->setCurrentUser) {
			$app['env']['currentuser'] = new \FelixOnline\Core\CurrentUser();
		}

		$app->run();

		$this->app = $app;
	}

	public function tearDown()
	{
		$app = \FelixOnline\Core\App::getInstance();

		$app['db']->dbh->close();

		\FelixOnline\Core\App::setInstance(null);
	}
}

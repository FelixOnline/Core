<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';
require_once __DIR__ . '/../../../constants.php';

class CurrentUserTest extends DatabaseTestCase
{
	use \Xpmock\TestCaseTrait;

	public $fixtures = array(
		'users',
		'logins',
		'cookies',
	);

	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));

		$app = \FelixOnline\Core\App::getInstance();

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
	}

	public function testNotLoggedIn()
	{
		$currentUser = new \FelixOnline\Core\CurrentUser();

		$this->assertFalse($currentUser->isLoggedIn());
	}

	public function testLoggedIn()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser();

		$this->assertFalse($currentUser->isLoggedIn());

		$env['session']['loggedin'] = true;
		$env['session']['uname'] = 'felix';

		$conn = $this->getConnection();
		$pdo = $conn->getConnection();
		$pdo->exec("INSERT INTO `login` 
			(`session_id`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`)
			VALUES 
			('1', '".$env['REMOTE_ADDR']."', '".$env['HTTP_USER_AGENT']."', 'felix', NOW(), 1, 1)");

		$this->assertTrue($currentUser->isLoggedIn());
	}

	public function testInvalidDatabaseSession()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser();

		$env['session']['loggedin'] = true;
		$env['session']['uname'] = 'felix';

		$conn = $this->getConnection();
		$pdo = $conn->getConnection();
		$pdo->exec("INSERT INTO `login` 
			(`session_id`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`)
			VALUES 
			('1', '".$env['REMOTE_ADDR']."', '".$env['HTTP_USER_AGENT']."', 'felix', NOW(), 1, 0)");

		$this->assertFalse($currentUser->isLoggedIn());
	}

	public function testLoginFromCookie()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser();

		$env['cookies']['felixonline'] = 'foo';

		$conn = $this->getConnection();
		$pdo = $conn->getConnection();
		$pdo->exec("INSERT INTO `cookies` 
			(`hash`, `user`, `expires`)
			VALUES 
			('foo', 'felix', DATE_ADD(NOW(), INTERVAL 1 DAY))");

		$this->assertTrue($currentUser->isLoggedIn());
		$this->assertTrue($env['session']['loggedin']);
		$this->assertEquals('felix', $env['session']['uname']);
		$this->assertEquals(2, $this->getConnection()->getRowCount('login'));
	}

	public function testExpiredCookie()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser();

		$env['cookies']['felixonline'] = 'foo';

		$conn = $this->getConnection();
		$pdo = $conn->getConnection();
		$pdo->exec("INSERT INTO `cookies` 
			(`hash`, `user`, `expires`)
			VALUES 
			('foo', 'felix', DATE_SUB(NOW(), INTERVAL 1 DAY))");

		$this->assertFalse($currentUser->isLoggedIn());
		$this->assertEquals(1, $this->getConnection()->getRowCount('login'));
	}
}

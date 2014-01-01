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

		$this->session = $this->mock('FelixOnline\\Core\\Session')
			->getId(1)
			->start(1)
			->reset()
			->new();

		$this->reflect($this->session)
			->__set('session', array());

		$this->cookies = $this->mock('FelixOnline\\Core\\Cookies')
			->set(true)
			->delete(true)
			->new();

		$this->reflect($this->cookies)
			->__set('cookies', array());
	}

	public function testNotLoggedIn()
	{
		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session,
			$this->cookies
		);

		$this->assertFalse($currentUser->isLoggedIn());
	}

	public function testLoggedIn()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session,
			$this->cookies
		);

		$this->assertFalse($currentUser->isLoggedIn());

		$this->session['loggedin'] = true;
		$this->session['uname'] = 'felix';

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

		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session,
			$this->cookies
		);

		$this->session['loggedin'] = true;
		$this->session['uname'] = 'felix';

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
		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session,
			$this->cookies
		);

		$this->cookies['felixonline'] = 'foo';

		$conn = $this->getConnection();
		$pdo = $conn->getConnection();
		$pdo->exec("INSERT INTO `cookies` 
			(`hash`, `user`, `expires`)
			VALUES 
			('foo', 'felix', DATE_ADD(NOW(), INTERVAL 1 DAY))");

		$this->assertTrue($currentUser->isLoggedIn());
		$this->assertTrue($this->session['loggedin']);
		$this->assertEquals('felix', $this->session['uname']);
		$this->assertEquals(2, $this->getConnection()->getRowCount('login'));
	}

	public function testExpiredCookie()
	{
		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session,
			$this->cookies
		);

		$this->cookies['felixonline'] = 'foo';

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

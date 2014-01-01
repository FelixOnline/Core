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
	}

	public function testNotLoggedIn()
	{
		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session
		);

		$this->assertFalse($currentUser->isLoggedIn());
	}

	public function testLoggedIn()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session
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

		$this->assertEquals('felix', $currentUser->isLoggedIn());
	}

	public function testInvalidDatabaseSession()
	{
		$env = \FelixOnline\Core\Environment::getInstance();

		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session
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
}

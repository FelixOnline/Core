<?php

require_once __DIR__ . '/../../AppTestCase.php';

class CurrentUserTest extends AppTestCase
{
    protected $setCurrentUser = false;

    public $fixtures = array(
        'users',
        'logins',
        'cookies',
        'images',
        'audit_log'
    );

    public function testNotLoggedIn()
    {
        $currentUser = new \FelixOnline\Core\CurrentUser();

        $this->assertFalse($currentUser->isLoggedIn());
    }

    public function testLoggedIn()
    {
        $this->fail('This test is broken due to Environment');
        $env = \FelixOnline\Core\Environment::getInstance();

        $currentUser = new \FelixOnline\Core\CurrentUser();

        $this->assertFalse($currentUser->isLoggedIn());

        $env['session']['loggedin'] = true;
        $env['session']['uname'] = 'felix';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `login`
			(`session_id`, `session_name`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`, `deleted`)
			VALUES
			('1', 'felix', '".$env['REMOTE_ADDR']."', '".$env['HTTP_USER_AGENT']."', 'felix', NOW(), 1, 1, 0)");

        $this->assertTrue($currentUser->isLoggedIn());
    }

    public function testInvalidDatabaseSession()
    {
        $this->fail('This test is broken due to Environment');
        $env = \FelixOnline\Core\Environment::getInstance();

        $currentUser = new \FelixOnline\Core\CurrentUser();

        $env['session']['loggedin'] = true;
        $env['session']['uname'] = 'felix';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `login`
			(`session_id`, `session_name`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`, `deleted`)
			VALUES
			('1', 'felix', '".$env['REMOTE_ADDR']."', '".$env['HTTP_USER_AGENT']."', 'felix', NOW(), 1, 0, 0)");

        $this->assertFalse($currentUser->isLoggedIn());
    }

    public function testLoginFromCookie()
    {
        $this->fail('This test is broken due to Environment');
        $env = \FelixOnline\Core\Environment::getInstance();

        $currentUser = new \FelixOnline\Core\CurrentUser();

        $env['cookies']['felixonline'] = 'foo';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `cookies`
			(`hash`, `user`, `expires`, `deleted`)
			VALUES
			('foo', 'felix', DATE_ADD(NOW(), INTERVAL 1 DAY), 0)");

        $this->assertTrue($currentUser->isLoggedIn());
        $this->assertTrue($env['session']['loggedin']);
        $this->assertEquals('felix', $env['session']['uname']);
        $this->assertEquals(2, $this->getConnection()->getRowCount('login'));
    }

    public function testExpiredCookie()
    {
        $this->fail('This test is broken due to Environment');
        $env = \FelixOnline\Core\Environment::getInstance();

        $currentUser = new \FelixOnline\Core\CurrentUser();

        $env['cookies']['felixonline'] = 'foo';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `cookies`
			(`hash`, `user`, `expires`, `deleted`)
			VALUES
			('foo', 'felix', DATE_SUB(NOW(), INTERVAL 1 DAY), 0)");

        $this->assertFalse($currentUser->isLoggedIn());
        $this->assertEquals(1, $this->getConnection()->getRowCount('login'));
    }
}

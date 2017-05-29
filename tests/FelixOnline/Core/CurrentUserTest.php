<?php

require_once __DIR__ . '/../../AppTestCase.php';
use \FelixOnline\Exceptions\InternalException;

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
        $app = \FelixOnline\Base\App::getInstance();
        $currentUser = new \FelixOnline\Core\CurrentUser();

        $this->assertFalse($currentUser->isLoggedIn());

        $app['env']['session']['loggedin'] = true;
        $app['env']['session']['uname'] = 'felix';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `login`
            (`session_id`, `session_name`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`, `deleted`)
            VALUES
            ('".$app['env']['session']->getId()."', 'felix', '".$app['env']['RemoteIP']."', '".$app['env']['RemoteUA']."', 'felix', NOW(), 1, 1, 0)");

        $this->assertTrue($currentUser->isLoggedIn());
    }

    public function testInvalidDatabaseSession()
    {
        $app = \FelixOnline\Base\App::getInstance();
        $currentUser = new \FelixOnline\Core\CurrentUser();

        $app['env']['session']['loggedin'] = true;
        $app['env']['session']['uname'] = 'felix';

        $conn = $this->getConnection();
        $pdo = $conn->getConnection();
        $pdo->exec("INSERT INTO `login`
            (`session_id`, `session_name`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`, `deleted`)
            VALUES
            ('".$app['env']['session']->getId()."', 'felix', '".$app['env']['RemoteIP']."', '".$app['env']['RemoteUA']."', 'felix', NOW(), 1, 0, 0)");

        $this->assertFalse($currentUser->isLoggedIn());
    }
}

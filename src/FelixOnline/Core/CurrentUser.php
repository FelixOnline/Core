<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Base\Session;
use FelixOnline\Base\AbstractCurrentUser;
use FelixOnline\Base\AbstractUser;
use FelixOnline\Exceptions\InternalException;

/*
 * Current User class
 */
class CurrentUser extends AbstractCurrentUser
{
    /*
     * Create current user object
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Public: Set user
     */
    public function setUser($username)
    {
        $app = App::getInstance();

        try {
            parent::__construct($username);
        } catch (\FelixOnline\Exceptions\ModelNotFoundException $e) {
            User::createUser($username);
            parent::__construct($username);
        }

        $user = new User($username);
        $this->logIn($user);
        $this->user = $user;
    }

    public function logIn(AbstractUser $user)
    {
        $app = App::getInstance();

        $user->setVisits($user->getVisits() + 1);
        $user->setIp($app['env']['RemoteIP']);
        $user->setTimestamp(time());
        $user->save();

        $this->user = $user;
    }

    public function logOut()
    {
        $this->destroySession();
        $this->destroyOldSessions();
        $this->unsetUser();
    }

    /************************************
     *
     * SESSIONS
     *
     ************************************/

    /**
     * Create session for user
     */
    public function createSession()
    {
        if (!$this->getUser()) {
            throw new \Exceptions\InternalException('Will not create session where no user is set');
        }

        $this->destroySessions(); // Delete old sessions for user

        $app = App::getInstance();

        $sql = $app['safesql']->query(
            "INSERT INTO `login`
            (
                session_id,
                session_name,
                ip,
                browser,
                user,
                logged_in,
                deleted
            ) VALUES (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                1,
                0
            )",
            array(
                $app['env']['session']->getId(),
                SESSION_NAME,
                $app['env']['RemoteIP'],
                $app['env']['RemoteUA'],
                $this->getUser(),
            ));
        $app['db']->query($sql);

        $app['env']['session']['uname'] = $this->getUser();
        $app['env']['session']['loggedin'] = true;
    }

    /*
     * Check if the session is recent (the last visited time is updated
     * on every visit, if this is greater than two hours then we need to log in
     * again, unless the cookie is valid) and valid for this user.
     */
    public function logInFromSession()
    {
        $app = App::getInstance();

        $sql = $app['safesql']->query(
            "SELECT
                TIMESTAMPDIFF(SECOND,timestamp,NOW()) AS timediff,
                ip,
                browser
            FROM `login`
            WHERE session_id = '%s'
            AND session_name = '%s'
            AND logged_in = 1
            AND valid = 1
            AND user = '%s'
            AND deleted = 0
            ORDER BY timediff ASC
            LIMIT 1",
            array(
                $app['env']['session']->getId(),
                SESSION_NAME,
                $app['env']['session']['uname'],
            ));

        $user = $app['db']->get_row($sql);

        if (
            $user
            && $user->timediff <= SESSION_LENGTH
            && $user->ip == $app['env']['RemoteIP']
            && $user->browser == $app['env']['RemoteUA']
        ) {
            return true;
        } else {
            $this->destroySession(); // Clear invalid session data
            return false;
        }
    }

    public function destroySession()
    {
        $app = App::getInstance();

        $sessionid = $app['env']['session']->getId();

        $app['env']['session']->reset();

        $sql = $app['safesql']->query("UPDATE `login`
                SET valid = 0,
                logged_in = 0
                WHERE user='%s'
                AND session_id='%s'
                AND session_name='%s'
                AND deleted = 0",
                array($this->getUser(), $sessionid, SESSION_NAME));

        return $app['db']->query($sql);
    }

    /*
     * Private: Destroy all user sessions
     */
    private function destroySessions()
    {
        $app = App::getInstance();

        $sql = $app['safesql']->query("UPDATE `login`
                SET valid = 0,
                logged_in = 0
                WHERE user='%s'
                AND session_name='%s'
                AND deleted = 0",
                array($this->getUser(), SESSION_NAME));

        return $app['db']->query($sql);
    }

    /*
     * Private: Destroy old sessions
     */
    private function destroyOldSessions()
    {
        $sql = $this->safesql->query("UPDATE `login`
                SET valid = 0
                WHERE user='%s'
                AND deleted = 0
                AND session_name='%s'
                AND logged_in=0
                OR TIMESTAMPDIFF(SECOND,timestamp,NOW()) > %i",
                array($this->getUser(),
                        SESSION_NAME,
                        SESSION_LENGTH));

        return $this->db->query($sql);
    }
}

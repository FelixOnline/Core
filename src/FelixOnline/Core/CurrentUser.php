<?php
namespace FelixOnline\Core;
/*
 * Current User class
 */
class CurrentUser extends User
{
	/*
	 * Create current user object
	 * Store session into object
	 */
	function __construct()
	{
		parent::__construct();

		$app = App::getInstance();

		if (!isset($app['env']['session'])) {
			$app['env']['session'] = new Session('felix');
		}
		$app['env']['session']->start();

		if (!isset($app['env']['cookies'])) {
			$app['env']['cookies'] = new Cookies();
		}

		if ($user = $this->isLoggedIn()) {
			$this->setUser($user);
		}
	}

	/*
	 * Public: Resets the session cookie, regenerating its ID, and ensures old session data is removed
	 */
	public function resetToGuest()
	{
		$app = App::getInstance();
		$app['env']['session']->reset();
	}

	/**
	 * Public: Check if user is currently logged in
	 *
	 * Returns boolean
	 */
	public function isLoggedIn()
	{
		$app = App::getInstance();

		if ($app['env']['session']['loggedin'] && $this->isSessionRecent()){
			return true;
		} else {
			// n.b. the session is cleared by isSessionRecent if invalid
			
			return $this->loginFromCookie();
		}
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
			// User does not yet exist in our database...
			// It'll be created later so carry on
		}

		// TODO Remove?
		$sql = $app['safesql']->query(
			"UPDATE login
				SET timestamp = NOW()
			WHERE session_id='%s'
			AND logged_in=1
			AND ip='%s'
			AND browser='%s'
			AND valid=1
			AND TIMESTAMPDIFF(SECOND,timestamp,NOW()) <= %i",
			array(
				$app['env']['session']->getId(),
				$app['env']['REMOTE_ADDR'],
				$app['env']['HTTP_USER_AGENT'],
				SESSION_LENGTH,
			));
		$app['db']->query($sql); // if this fails, it doesn't matter, we will
								// just be auto logged out after a while
	}

	/**
	 * Public: Removes the permanent cookie, and removes associated database entries
	 */
	protected function removeCookie()
	{
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"DELETE FROM cookies
			WHERE hash = '%s'",
			array(
				$app['env']['cookies']['felixonline']
			));

		$app['db']->query($sql);

		// also remove any expired cookies for anyone
		// TODO move to cron
		$sql = $app['safesql']->query(
			"DELETE FROM cookies
			WHERE expires < NOW()", array());

		$app['db']->query($sql);

		$app['env']['cookies']->delete('felixonline');
	}

	/**
	 * Protected: Check if there is a valid permanent cookie, if so log in with it
	 *
	 * Returns false if failed, username otherwise
	 * TODO make sure there isn't redundant code
	 */
	protected function loginFromCookie()
	{
		$app = App::getInstance();

		// is there a cookie?
		if (!isset($app['env']['cookies']['felixonline'])) {
			return false;
		}

		$sql = $app['safesql']->query(
			"SELECT user
			FROM `cookies`
			WHERE hash='%s'
			AND UNIX_TIMESTAMP(expires) > UNIX_TIMESTAMP()
			ORDER BY expires ASC
			LIMIT 1",
			array(
				$app['env']['cookies']['felixonline']
			));

		$cookie = $app['db']->get_row($sql);
		if (!$cookie) {
			$this->removeCookie();
			return false;
		}

		$username = $cookie->user;

		// Reset session ID
		$this->resetToGuest();

		// Populate user class
		parent::__construct($username);

		// Create session
		$this->createSession();

		return true;
	}

	/**
	 * Create session for user
	 */
	protected function createSession()
	{
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"INSERT INTO `login` 
			(
				session_id,
				ip,
				browser,
				user,
				logged_in
			) VALUES (
				'%s',
				'%s',
				'%s',
				'%s',
				1
			)",
			array(
				$app['env']['session']->getId(),
				$app['env']['REMOTE_ADDR'],
				$app['env']['HTTP_USER_AGENT'],
				$this->getUser(),
			));
		$app['db']->query($sql);

		$app['env']['session']['uname'] = $this->getUser();
		$app['env']['session']['loggedin'] = true;
	}

	/*
	 * Protected: Check if the session is recent (the last visited time is updated
	 * on every visit, if this is greater than two hours then we need to log in
	 * again, unless the cookie is valid
	 */
	protected function isSessionRecent()
	{
		$app = App::getInstance();

		$sql = $app['safesql']->query(
			"SELECT
				TIMESTAMPDIFF(SECOND,timestamp,NOW()) AS timediff,
				ip,
				browser
			FROM `login`
			WHERE session_id = '%s'
			AND logged_in = 1
			AND valid = 1
			AND user = '%s'
			ORDER BY timediff ASC
			LIMIT 1",
			array(
				$app['env']['session']->getId(),
				$app['env']['session']['uname'],
			));

		$user = $app['db']->get_row($sql);

		if (
			$user
			&& $user->timediff <= SESSION_LENGTH 
			&& $user->ip == $app['env']['REMOTE_ADDR']
			&& $user->browser == $app['env']['HTTP_USER_AGENT']
		) {
			return true;
		} else {
			$this->resetToGuest(); // Clear invalid session data
			// N.B. Do not delete cookies here!! If the session is invalid
			// it may have expired, but then we may be able to log in again
			// from the cookie
			return false;
		}
	}

	/*
	 * Public: Update user details
	 */
	public function updateDetails($username)
	{
		/* update user details */
		$name = $this->updateName($username);
		$info = $this->updateInfo($username);

		$sql = $this->safesql->query(
			"INSERT INTO `user` 
				(user,name,visits,ip,info) 
			VALUES (
				'%s',
				'%s',
				1,
				'%s',
				'%s'
			) 
			ON DUPLICATE KEY 
			UPDATE 
				name='%s',
				visits=visits+1,
				ip='%s',
				timestamp=NOW(),
				info='%s'",
			array(
				$username,
				$name,
				$_SERVER['REMOTE_ADDR'],
				$info,
				$name,
				$_SERVER['REMOTE_ADDR'],
				$info,
			));
			// note that this updated the last access time and the ip
			// of the last access for this user, this is separate from the
			// session
		return $this->db->query($sql);
	}

	/**
	 * Update user's name from ldap
	 */
	private function updateName($uname)
	{
		if(!LOCAL) {
			$ds = ldap_connect("addressbook.ic.ac.uk");
			$r = ldap_bind($ds);
			$justthese = array("gecos");
			$sr = ldap_search(
				$ds,
				"ou=People, ou=everyone, dc=ic, dc=ac, dc=uk",
				"uid=$uname",
				$justthese
			);
			$info = ldap_get_entries($ds, $sr);
			if ($info["count"] > 0) {
				$this->setName($info[0]['gecos'][0]);
				return ($info[0]['gecos'][0]);
			} else {
				return false;
			}
		} else {
			$name = $uname;
			try {
				$name = $this->getName();
			} catch (InternalException $e) {
				// User does not yet exist in our database...
				// It'll be created later so carry on
			}
			return $name;
		}
	}

	/*
	 * Update user's info from ldap
	 *
	 * Returns json encoded array
	 */
	private function updateInfo($uname)
	{
		$info = '';
		if(!LOCAL) { // if on union server
			$ds = ldap_connect("addressbook.ic.ac.uk");
			$r = ldap_bind($ds);
			$justthese = array("o");
			$sr = ldap_search(
				$ds,
				"ou=People, ou=shibboleth, dc=ic, dc=ac, dc=uk",
				"uid=$uname",
				$justthese
			);
			$info = ldap_get_entries($ds, $sr);
			if ($info["count"] > 0) {
				$info = json_encode(explode('|', $info[0]['o'][0]));
				$this->setInfo($info);
			} else {
				return false;
			}
		}
		return $info;
	}

	public function getRole()
	{
		if($this->fields['role']) {
			return $this->fields['role'];
		} else {
			return 0;
		}
	}
}

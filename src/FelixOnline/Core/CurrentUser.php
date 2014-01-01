<?php
namespace FelixOnline\Core;
/*
 * Current User class
 */
class CurrentUser extends User {
	protected $session; // current session id
	protected $cookies; // cookie class

	/*
	 * Create current user object
	 * Store session into object
	 */
	function __construct(Session $session = null, Cookies $cookies = null) {
		if (is_null($session)) {
			$this->session = new Session('felix');
		} else {
			$this->session = $session;
		}
		$this->session->start();

		if (is_null($cookies)) {
			$this->cookies = new Cookies();
		} else {
			$this->cookies = $cookies;
		}

		if ($user = $this->isLoggedIn()) {
			$this->setUser($user);
		}
	}

	/*
	 * Public: Resets the session cookie, regenerating its ID, and ensures old session data is removed
	 */
	public function resetToGuest() {
		$this->session->reset();
	}

	/**
	 * Public: Check if user is currently logged in
	 *
	 * Returns boolean
	 */
	public function isLoggedIn() {
		if($this->session['loggedin'] && $this->isSessionRecent()){
			return true;
		} else {
			// n.b. the session is cleared by isSessionRecent if invalid
			
			return $this->loginFromCookie();
		}
	}

	/**
	 * Public: Set user
	 */
	public function setUser($username) {
		$env = Environment::getInstance();

		try {
			parent::__construct($username);
		} catch (\FelixOnline\Exceptions\ModelNotFoundException $e) {
			// User does not yet exist in our database...
			// It'll be created later so carry on
		}

		// TODO Remove?
		$sql = App::query(
			"UPDATE login
				SET timestamp = NOW()
			WHERE session_id='%s'
			AND logged_in=1
			AND ip='%s'
			AND browser='%s'
			AND valid=1
			AND TIMESTAMPDIFF(SECOND,timestamp,NOW()) <= %i",
			array(
				$this->session->getId(),
				$env['REMOTE_ADDR'],
				$env['HTTP_USER_AGENT'],
				SESSION_LENGTH,
			));
		App::$db->query($sql); // if this fails, it doesn't matter, we will
								// just be auto logged out after a while
	}


	/**
	 * Public: Removes the permanent cookie, and removes associated database entries
	 */
	protected function removeCookie() {
		$sql = App::query(
			"DELETE FROM cookies
			WHERE hash = '%s'",
			array(
				$this->cookies['felixonline']
			));

		App::$db->query($sql);

		// also remove any expired cookies for anyone
		// TODO move to cron
		$sql = App::query(
			"DELETE FROM cookies
			WHERE expires < NOW()", array());

		App::$db->query($sql);

		$this->cookies->delete('felixonline');
	}

	/**
	 * Protected: Check if there is a valid permanent cookie, if so log in with it
	 *
	 * Returns false if failed, username otherwise
	 * TODO make sure there isn't redundant code
	 */
	protected function loginFromCookie() {
		// is there a cookie?
		if (!isset($this->cookies['felixonline'])) {
			return false;
		}

		$sql = App::query(
			"SELECT user
			FROM `cookies`
			WHERE hash='%s'
			AND UNIX_TIMESTAMP(expires) > UNIX_TIMESTAMP()
			ORDER BY expires ASC
			LIMIT 1",
			array(
				$this->cookies['felixonline']
			));

		$cookie = App::$db->get_row($sql);
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
		$env = Environment::getInstance();

		$sql = App::query(
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
				$this->session->getId(),
				$env['REMOTE_ADDR'],
				$env['HTTP_USER_AGENT'],
				$this->getUser(),
			));
		App::$db->query($sql);

		$this->session['uname'] = $this->getUser();
		$this->session['loggedin'] = true;
	}

	/*
	 * Protected: Check if the session is recent (the last visited time is updated
	 * on every visit, if this is greater than two hours then we need to log in
	 * again, unless the cookie is valid
	 */
	protected function isSessionRecent() {
		$env = Environment::getInstance();

		$sql = App::query(
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
				$this->session->getId(),
				$this->session['uname'],
			));

		$user = App::$db->get_row($sql);

		if (
			$user
			&& $user->timediff <= SESSION_LENGTH 
			&& $user->ip == $env['REMOTE_ADDR']
			&& $user->browser == $env['HTTP_USER_AGENT']
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

	public function getSession() {
		return $this->session;
	}

	/*
	 * Public: Update user details
	 */
	public function updateDetails($username) {
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

	/*
	 * Update user's name from ldap
	 */
	private function updateName($uname) {
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
	private function updateInfo($uname) {
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

	public function getRole() {
		if($this->fields['role']) {
			return $this->fields['role'];
		} else {
			return 0;
		}
	}
}

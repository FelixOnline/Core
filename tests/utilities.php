<?php

require_once __DIR__ . '/../constants.php';

/**
 * Test utilities
 */

function loginUser($user)
{
	$app = \FelixOnline\Core\App::getInstance();

	// Log in user
	$app['env']['session']['loggedin'] = true;
	$app['env']['session']['uname'] = $user;

	$app['db']->query("INSERT INTO `login` 
		(`session_id`, `ip`, `browser`, `user`, `timestamp`, `valid`, `logged_in`)
		VALUES 
		('1', '".$app['env']['REMOTE_ADDR']."', '".$app['env']['HTTP_USER_AGENT']."', '".$user."', NOW(), 1, 1)");

	$app['currentuser']->setUser($user);
}

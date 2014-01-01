<?php

require_once __DIR__ . '/../lib/SafeSQL.php';
/**
 * Test utilities
 */

function create_app($config = array('base_url' => 'foo')) {
	$db = new \ezSQL_mysqli();
	$db->quick_connect(
		'root',
		'',
		'test_media_felix',
		'localhost',
		3306,
		'utf8'
	);

	$safesql = new \SafeSQL_MySQLi($db->dbh);

	$env = \FelixOnline\Core\Environment::mock();

	return new \FelixOnline\Core\App($config, $db, $safesql, $env);
}

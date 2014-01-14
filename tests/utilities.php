<?php

require_once __DIR__ . '/../lib/SafeSQL.php';
/**
 * Test utilities
 */

function create_app($config = array('base_url' => 'foo')) {
	$app = new \FelixOnline\Core\App($config);

	$db = new \ezSQL_mysqli();
	$db->quick_connect(
		'root',
		'',
		'test_media_felix',
		'localhost',
		3306,
		'utf8'
	);
	$app['db'] = $db;

	$app['safesql'] = new \SafeSQL_MySQLi($db->dbh);

	$app['env'] = \FelixOnline\Core\Environment::mock();

	$app->run();

	return $app;;
}

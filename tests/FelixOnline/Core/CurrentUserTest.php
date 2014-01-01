<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';
require_once __DIR__ . '/../../../constants.php';

class CurrentUserTest extends DatabaseTestCase
{
	use \Xpmock\TestCaseTrait;

	public $fixtures = array(
		'users',
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
			->new();

		$this->reflect($this->session)
			->__set('session', array());
	}

	public function testNotLoggedIn()
	{
		$currentUser = new \FelixOnline\Core\CurrentUser(
			$this->session
		);
	}
}

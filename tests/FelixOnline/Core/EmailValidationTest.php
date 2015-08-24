<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../../constants.php';

class EmailValidationTest extends AppTestCase
{
	public $fixtures = array(
		'email_validations',
	);

	public function testCreate()
	{
		$manager = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation');
		$this->assertEquals($manager->count(), 2);

		$code = \FelixOnline\Core\EmailValidation::create('test3@test.com');
		$this->assertEquals(strlen($code), 13);

		$this->assertEquals($manager->count(), 3);
	}

	public function testCreateError()
	{
		$manager = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\EmailValidation', 'email_validation');
		$this->assertEquals($manager->count(), 2);

		$code = \FelixOnline\Core\EmailValidation::create('test2@test.com');
		$this->assertFalse($code);

		$this->assertEquals($manager->count(), 2);
	}

	public function testCheckValidated()
	{
		$status = \FelixOnline\Core\EmailValidation::isEmailValidated('test@test.com');
		$this->assertTrue($status);
	}

	public function testCheckNotValidated()
	{
		$status = \FelixOnline\Core\EmailValidation::isEmailValidated('test2@test.com');
		$this->assertFalse($status);
	}

	public function testCheckNotExistingValidated()
	{
		$status = \FelixOnline\Core\EmailValidation::isEmailValidated('test2@test.com');
		$this->assertFalse($status);
	}
}

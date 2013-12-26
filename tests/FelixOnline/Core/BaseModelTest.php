<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class BaseModelTest extends DatabaseTestCase
{
	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));
	}

	public function testNoModelFoundException()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\ModelNotFoundException',
			'No model in database'
		);
		$model = new \FelixOnline\Core\BaseModel(null);
	}

	public function testGetField()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'foo' => 'bar'
		));

		$this->assertEquals($model->getFoo(), 'bar');
	}

	public function testGetMissingField()
	{
		$model = new \FelixOnline\Core\BaseModel(array());

		$this->setExpectedException(
			'FelixOnline\Exceptions\ModelConfigurationException',
			'The requested field "foo" does not exist'
		);

		$model->getFoo();
	}

	public function testSetField()
	{
		$model = new \FelixOnline\Core\BaseModel(array());
		$model->setFoo('bar');

		$this->assertEquals($model->getFoo(), 'bar');
	}

	public function testHasField()
	{
		$model = new \FelixOnline\Core\BaseModel(array());

		$this->assertFalse($model->hasFoo());

		$model->setBar('fiz');
		$this->assertTrue($model->hasBar());
	}

	public function testWrongVerb()
	{
		$model = new \FelixOnline\Core\BaseModel(array());
		$this->setExpectedException(
			'FelixOnline\Exceptions\ModelConfigurationException',
			'The requested verb is not valid'
		);
		$model->geeFoo();
	}

	public function testGetFields()
	{
		$fields = array(
			'foo' => 'bar'
		);
		$model = new \FelixOnline\Core\BaseModel($fields);

		$this->assertEquals($fields, $model->getFields());
	}
}

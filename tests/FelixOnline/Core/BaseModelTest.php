<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class BaseModelTest extends DatabaseTestCase
{
	public $fixtures = array(
		'users',
	);

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

	public function testConstructInsertSQL()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'foo' => 'bar', // string
			'fizz' => 1, // number
			'buzz' => NULL, // null
			'empty' => '', // empty
		));
		$model->setDbtable('test');

		$this->assertEquals(
			$model->constructInsertSQL($model->getFields()),
			"INSERT INTO `test` (`foo`, `fizz`, `buzz`, `empty`) VALUES ('bar', 1, NULL, '')"
		);
	}

	public function testConstructUpdateSQL()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'id' => 1,
			'foo' => 'bar', // string
			'fizz' => 1, // number
			'buzz' => NULL, // null
			'empty' => '', // empty
		));
		$model->setDbtable('test');

		$this->assertEquals(
			$model->constructUpdateSQL(array(
				'foo' => 'bars',
			)),
			"UPDATE `test` SET `foo`='bars' WHERE `id`=1"
		);
	}

	public function testSave()
	{
		$this->assertEquals(3, $this->getConnection()->getRowCount('user'));

		$user = new \FelixOnline\Core\BaseModel(array(
			'user' => 'test',
			'name' => 'Joe Blogs',
			'role' => 10
		));

		$user->setDbtable('user');

		$user->save();

		$this->assertEquals(4, $this->getConnection()->getRowCount('user'));
	}

	public function testSaveUpdate()
	{
		$this->assertEquals(3, $this->getConnection()->getRowCount('user'));

		$user = new \FelixOnline\Core\BaseModel(array(
			'user' => 'felix',
			'name' => 'Joseph Letts - Felix Editor',
			'role' => 100,
			'info' => '',
		));

		$user->setPrimaryKey('user');
		$user->setDbtable('user');

		$user->setInfo('Foo bar');
		$user->save();

		$this->assertEquals(3, $this->getConnection()->getRowCount('user'));
	}

	public function testSaveNoFieldsException()
	{
		$model = new \FelixOnline\Core\BaseModel(array());

		$this->setExpectedException('FelixOnline\Exceptions\InternalException', 'No fields in object');

		$model->save();
	}

	public function testSaveNoDbtableException()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'foo' => 'bar'
		));

		$this->setExpectedException('FelixOnline\Exceptions\InternalException', 'No table specified');

		$model->save();
	}

	public function testFieldFilters()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'id' => 1,
			'foo' => 'bar',
			'xxx' => 'bbb',
		));

		$model->setFieldFilters(array(
			'foo' => 'fizz',
			'xxx' => 'yyy',
		));

		$model->setDbtable('test');

		$this->assertEquals(
			$model->constructInsertSQL($model->getFields()),
			"INSERT INTO `test` (`id`, `fizz`, `yyy`) VALUES (1, 'bar', 'bbb')"
		);

		$this->assertEquals(
			$model->constructUpdateSQL(array(
				'foo' => 'bars',
				'xxx' => 'aaa'
			)),
			"UPDATE `test` SET `fizz`='bars', `yyy`='aaa' WHERE `id`=1"
		);
	}
}

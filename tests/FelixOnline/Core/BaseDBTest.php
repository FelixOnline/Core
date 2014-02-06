<?php

require_once __DIR__ . '/../../AppTestCase.php';

use FelixOnline\Core\Type\IntegerField;
use FelixOnline\Core\Type\CharField;

class BaseModelTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'users',
	);

	public function testInit()
	{
		$model = new \FelixOnline\Core\BaseDB(array(
			'title' => new \FelixOnline\Core\Type\CharField()
		), 1, 'article');

		$this->assertEquals($model->getTitle(), 'Fighting for Libel Reform');
	}

	public function testAssignedPrimaryKey()
	{
		$model = new \FelixOnline\Core\BaseDB(array(
			'user' => new \FelixOnline\Core\Type\CharField(array(
				'primary' => true
			))
		), 'felix', 'user');

		$this->assertEquals($model->getUser(), 'felix');
	}

	public function testNoModelFoundException()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\ModelNotFoundException',
			'No model in database'
		);
		$model = new \FelixOnline\Core\BaseDB(array(
			'title' => new \FelixOnline\Core\Type\CharField()
		), 0, 'article');
	}

	public function testConstructSelectSQL()
	{
		$model = $this->mock('FelixOnline\\Core\\BaseDB')
			->new();

		$model->this()->dbtable = 'test';
		$model->this()->pk = 'id';

		$fields = array(
			'id' => (new IntegerField())->setValue(1),
			'title' => new CharField(),
		);

		$this->assertEquals(
			$model->constructSelectSQL($fields),
			"SELECT `id`, `title` FROM `test` WHERE `id` = 1"
		);
	}

	public function testConstructInsertSQL()
	{
		$model = $this->mock('FelixOnline\\Core\\BaseDB')
			->new();

		$model->this()->dbtable = 'test';
		$model->this()->pk = 'id';

		$fields = array(
			'foo' => (new CharField())->setValue('bar'), // string
			'fizz' => (new IntegerField())->setValue(1), // number
			'buzz' => (new CharField())->setValue(NULL), // null
			'empty' => (new CharField())->setValue(''), // empty
		);

		$this->assertEquals(
			$model->constructInsertSQL($fields),
			"INSERT INTO `test` ( `foo`, `fizz`, `buzz`, `empty` ) VALUES ( 'bar', 1, NULL, '' )"
		);
	}

	public function testConstructUpdateSQL()
	{
		$model = $this->mock('FelixOnline\\Core\\BaseDB')
			->new();

		$model->this()->dbtable = 'test';
		$model->this()->pk = 'id';

		$fields = array(
			'id' => (new IntegerField())->setValue(1),
			'foo' => (new CharField())->setValue('bar'), // string
			'fizz' => (new IntegerField())->setValue(1), // number
			'buzz' => (new CharField())->setValue(NULL), // null
			'empty' => (new CharField())->setValue(''), // empty
		);

		$this->assertEquals(
			$model->constructUpdateSQL($fields),
			"UPDATE `test` SET `foo`='bar', `fizz`=1, `buzz`=NULL, `empty`='' WHERE `id`=1"
		);
	}

	public function testSave()
	{
		$this->assertEquals(3, $this->getConnection()->getRowCount('user'));

		$user = new \FelixOnline\Core\BaseDB(array(
			'user' => (new CharField(array('primary' => true)))->setValue('test'),
			'name' => (new CharField())->setValue('Joe Blogs'),
			'role' => (new IntegerField())->setValue(10),
		), null, 'user');

		$user->save();

		$this->assertEquals(4, $this->getConnection()->getRowCount('user'));
	}

	public function testSaveArticle()
	{
		$this->assertEquals(3, $this->getConnection()->getRowCount('article'));

		$user = new \FelixOnline\Core\BaseDB(array(
			'title' => (new CharField())->setValue('test'),
			'teaser' => (new CharField())->setValue('test'),
			'category' => (new IntegerField())->setValue(1),
			'text1' => (new IntegerField())->setValue(1),
		), null, 'article');

		$user->save();

		$this->assertEquals(4, $this->getConnection()->getRowCount('article'));
	}

	public function xtestSaveUpdate()
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

	public function xtestSaveNoFieldsException()
	{
		$model = new \FelixOnline\Core\BaseModel(array());

		$this->setExpectedException('FelixOnline\Exceptions\InternalException', 'No fields in object');

		$model->save();
	}

	public function xtestSaveNoDbtableException()
	{
		$model = new \FelixOnline\Core\BaseModel(array(
			'foo' => 'bar'
		));

		$this->setExpectedException('FelixOnline\Exceptions\InternalException', 'No table specified');

		$model->save();
	}

}

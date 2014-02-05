<?php

require_once __DIR__ . '/../../AppTestCase.php';

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

}

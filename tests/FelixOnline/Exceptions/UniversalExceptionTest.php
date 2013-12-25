<?php
class UniversalExceptionTest extends PHPUnit_Framework_TestCase
{
	public function testException()
	{
		$this->setExpectedException('FelixOnline\Exceptions\UniversalException');
		throw new \FelixOnline\Exceptions\UniversalException('foo');
	}

	public function testExceptionMessage()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\UniversalException', 'foo'
		);
		throw new \FelixOnline\Exceptions\UniversalException('foo');
	}

	public function testExceptionCode()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\UniversalException',
			'foo',
			100
		);
		throw new \FelixOnline\Exceptions\UniversalException('foo');
	}
}

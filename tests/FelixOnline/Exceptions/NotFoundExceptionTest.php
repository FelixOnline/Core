<?php
class NotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
	public function testException()
	{
		$this->setExpectedException('FelixOnline\Exceptions\NotFoundException');
		throw new \FelixOnline\Exceptions\NotFoundException('foo');
	}

	public function testExceptionMessage()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\NotFoundException', 'foo'
		);
		throw new \FelixOnline\Exceptions\NotFoundException('foo');
	}

	public function testExceptionCode()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\NotFoundException',
			'foo',
			102
		);
		throw new \FelixOnline\Exceptions\NotFoundException('foo');
	}
}

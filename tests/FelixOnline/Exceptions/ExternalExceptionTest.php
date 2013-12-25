<?php
class ExternalExceptionTest extends PHPUnit_Framework_TestCase
{
	public function testException()
	{
		$this->setExpectedException('FelixOnline\Exceptions\ExternalException', 'foo');
		throw new \FelixOnline\Exceptions\ExternalException('foo');
	}

	public function testExceptionCode()
	{
		$this->setExpectedException(
			'FelixOnline\Exceptions\ExternalException',
			'foo',
			115
		);
		throw new \FelixOnline\Exceptions\ExternalException('foo');
	}
}

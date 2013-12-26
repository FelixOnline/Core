<?php
namespace FelixOnline\Exceptions;
/**
 * Generic - their fault
 */
class NotFoundException extends UniversalException {
	public function __construct(
		$message,
		$code = parent::EXCEPTION_NOTFOUND,
		\Exception $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}

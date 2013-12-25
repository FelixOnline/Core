<?php
namespace FelixOnline\Exceptions;
/**
 * Generic - not our fault
 */
class ExternalException extends UniversalException {
	public function __construct(
		$message,
		$code = parent::EXCEPTION_EXTERNAL,
		\Exception $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}

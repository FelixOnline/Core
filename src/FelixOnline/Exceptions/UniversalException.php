<?php
namespace FelixOnline\Exceptions;

/**
 * Base of all exceptions
 */
class UniversalException extends \Exception {
	const EXCEPTION_UNIVERSAL = 100;
	const EXCEPTION_INTERNAL = 101;
	const EXCEPTION_MODEL_NOTFOUND = 102;
	const EXCEPTION_MODEL = 103;
	const EXCEPTION_ERRORHANDLER = 104;

	protected $user;
	
	public function __construct(
		$message,
		$code = self::EXCEPTION_UNIVERSAL,
		\Exception $previous = null
	) {
		global $currentuser;
		$this->user = $currentuser;

		parent::__construct($message, $code, $previous);
	}
	
	public function getUser() {
		return $this->user;
	}
}

<?php
namespace FelixOnline\Exceptions;

/**
 * Base of all exceptions
 */
class UniversalException extends \Exception {
	const EXCEPTION_UNIVERSAL = 100;
	const EXCEPTION_INTERNAL = 101;
	const EXCEPTION_NOTFOUND = 102;
	const EXCEPTION_IMAGE_NOTFOUND = 103;
	const EXCEPTION_VIEW_NOTFOUND = 104;
	const EXCEPTION_MODEL_NOTFOUND = 105;
	const EXCEPTION_TIMTHUMB_NOTFOUND = 106;
	const EXCEPTION_MODEL = 107;
	const EXCEPTION_GLUE = 108;
	const EXCEPTION_GLUE_URL = 109;
	const EXCEPTION_GLUE_CLASS = 110;
	const EXCEPTION_GLUE_METHOD = 111;
	const EXCEPTION_ERRORHANDLER = 112;
	const EXCEPTION_VALIDATOR = 113;
	const EXCEPTION_LOGIN = 114;
	const EXCEPTION_EXTERNAL = 115;

	const LOGIN_EXCEPTION_CREDENTIALS = 50;
	const LOGIN_EXCEPTION_SESSION = 51;
	const LOGIN_EXCEPTION_OTHER = 52;

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

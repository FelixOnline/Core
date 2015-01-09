<?php

namespace FelixOnline\Exceptions;

class ErrorHandlerException extends UniversalException {
	protected $params;
	
	public function __construct($message, $params, $code = parent::EXCEPTION_ERRORHANDLER, \Exception $previous = null) {
		$this->params = $params;
		
		parent::__construct($message, $code, $previous);
	}
	
	public function getErrno() {
		return $this->params['errno'];
	}
	
	public function getErrorFile() {
		return $this->params['file'];
	}
	
	public function getErrorLine() {
		return $this->params['line'];
	}
	
	public function getContext() {
		return $this->params['context'];
	}
}

function errorhandler($errno, $errstr, $errfile, $errline, $errcontext) {
	throw new ErrorHandlerException($errstr, array('errno' => $errno, 'file' => $errfile, 'line' => $errline, 'context' => $errcontext));
}

set_error_handler('errorhandler', E_ALL & ~E_NOTICE);
<?php
namespace FelixOnline\Exceptions;
/**
 * Controller did not find data
 */
class NotFoundException extends UniversalException {
	protected $matches;
	protected $controller;

	public function __construct(
		$message,
		$code = parent::EXCEPTION_NOTFOUND,
		$matches,
		$controller,
		\Exception $previous = null
	) {
		$this->matches = $matches;
		$this->controller = $controller;
		parent::__construct($message, $code, $previous);
	}

	public function getMatches() {
		return $this->matches;
	}

	public function getController() {
		return $this->controller;
	}
}

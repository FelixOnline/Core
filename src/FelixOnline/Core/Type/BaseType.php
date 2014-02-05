<?php
namespace FelixOnline\Core\Type;

class BaseType
{
	public $config;
	protected $value;
	protected $placeholder = "'%s'";

	public function __construct($config = array())
	{
		$this->config = $config + array(
			'primary' => false	
		);
	}

	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getSQL()
	{
		$app = \FelixOnline\Core\App::getInstance();
		return $app['safesql']->query($this->placeholder, array($this->value));
	}
}

<?php
namespace FelixOnline\Core\Type;

class DateTimeField extends BaseType
{
	protected $placeholder = "'%s'";

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

		if (is_null($this->value) && $this->config['null'] == true) {
			return 'NULL';
		}

		return $app['safesql']->query($this->placeholder, array($this->value));
	}
}

<?php
namespace FelixOnline\Core\Type;

class DateTimeField extends BaseType
{
	protected $placeholder = "'%s'";

	public function setValue($value)
	{
		if (is_string($value) && strtotime($value) !== false) {
			$this->value = strtotime($value);
			$this->raw_value = $value;
		} else if (is_int($value)) {
			$this->value = $value;
			$this->raw_value = date('Y-m-d H:i:s', $value);
		} else if (is_null($value)) {
			$this->value = $value;
			$this->raw_value = $value;
		} else {
			throw new \FelixOnline\Exceptions\InternalException('Invalid date');
		}

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getRawValue()
	{
		return $this->raw_value;
	}

	public function getSQL()
	{
		$app = \FelixOnline\Core\App::getInstance();

		if (is_null($this->value) && $this->config['null'] == true) {
			return 'NULL';
		}

		$datetime = date('Y-m-d H:i:s', $this->value);

		return $app['safesql']->query($this->placeholder, array($datetime));
	}
}

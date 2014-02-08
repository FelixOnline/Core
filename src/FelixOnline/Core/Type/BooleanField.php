<?php
namespace FelixOnline\Core\Type;

class BooleanField extends BaseType
{
	protected $placeholder = "%i";

	public function setValue($value)
	{
		$this->value = (boolean) $value;
		return $this;
	}

	public function getValue()
	{
		return (boolean) $this->value;
	}
}

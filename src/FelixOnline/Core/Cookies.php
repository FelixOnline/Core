<?php
namespace FelixOnline\Core;
/**
 * Cookies
 */
class Cookies implements \ArrayAccess
{
	protected $cookies = array();

	public function __construct()
	{
		$this->cookies = &$_COOKIE;
	}

	/**
	 * Set cookie
	 */
	public function set($name, $value, $expire, $path = "/")
	{
		return setcookie($name, $value, $expire, $path);
	}

	/**
	 * Delete cookie
	 */
	public function delete($name)
	{
		return setcookie($name, '', time() - 3600);
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
            $this->cookies[] = $value;
        } else {
            $this->cookies[$offset] = $value;
        }
    }

	public function offsetExists($offset)
	{
		return isset($this->cookies[$offset]);
	}

	public function offsetUnset($offset)
	{
        unset($this->cookies[$offset]);
    }

	public function offsetGet($offset)
	{
        return isset($this->cookies[$offset]) ? $this->cookies[$offset] : null;
    }
}

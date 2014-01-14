<?php
namespace FelixOnline\Core;
/**
 * App class
 */
class App implements \ArrayAccess
{
	protected static $instance = null;
	protected static $options = array();
	protected $container;

	/**
	 * Required options
	 */
	protected $required = array(
		'base_url'
	);

	/**
	 * Constructor
	 *
	 * @param array $options - options array
	 * @param \ezSQL_mysqli $db - database object
	 * @param \SafeSQL_MySQLi $db - safesql object
	 */
	public function __construct(
		$options = array()
	) {
		$this->checkOptions($options);
		self::$options = $options;

		unset($this->container);

		self::$instance = $this;
	}

	/**
	 * Initialize app
	 */
	public function run()
	{
		if (!isset($this->container['env']) || is_null($this->container['env'])) {
			$this->container['env'] = Environment::getInstance();
		}

		if (!isset($this->container['db']) || !($this->container['db'] instanceof \ezSQL_mysqli)) {
			throw new \FelixOnline\Exceptions\InternalException('No db setup');
		}

		if (!isset($this->container['safesql']) || !($this->container['safesql'] instanceof \SafeSQL_MySQLi)) {
			throw new \FelixOnline\Exceptions\InternalException('No safesql setup');
		}
	}

	/**
	 * Check that all required options are defined
	 *
	 * Throws InternalException if option is not defined
	 */
	private function checkOptions($options)
	{
		foreach($this->required as $req) {
			if (!array_key_exists($req, $options)) {
				throw new \FelixOnline\Exceptions\InternalException('"' . $req . '" option has not been defined');
			}
		}
	}

	/**
	 * Get instance
	 *
	 * @return instance
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			throw new \FelixOnline\Exceptions\InternalException('App has not been initialised yet');
		}
		return self::$instance;
	}

	/**
	 * Set instance
	 *
	 * @param object $instance - instance object to set
	 *
	 * @return void
	 */
	public static function setInstance($instance)
	{
		self::$instance = $instance;
	}

	/**
	 * Get option
	 *
	 * @param string $key - option key
	 * @param mixed $default - value to return if not defined [optional]
	 *
	 * @return mixed option
	 */
	public function getOption($key)
	{
		if (!array_key_exists($key, self::$options)) {
			// if a default has been defined 
			if (func_num_args() > 1) {
				return func_get_arg(1);
			} else {
				throw new \FelixOnline\Exceptions\InternalException('Option "'.$key.'" has not been set');
			}
		}
		return self::$options[$key];
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

	public function offsetExists($offset)
	{
		return isset($this->container[$offset]);
	}

	public function offsetUnset($offset)
	{
        unset($this->container[$offset]);
    }

	public function offsetGet($offset)
	{
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

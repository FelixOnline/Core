<?php
namespace FelixOnline\Core;
/**
 * App class
 */
class App
{
	protected static $instance = null;
	protected static $options = array();

	/**
	 * Required options
	 */
	protected $required = array(
		'db_name',
		'db_user',
		'db_pass'
	);

	public static $db = null;
	protected static $safesql = null;

	/**
	 * Constructor
	 *
	 * @param array $options - options array
	 */
	public function __construct($options = array())
	{
		$this->checkOptions($options);
		self::$options = $options;

		self::$db = new \ezSQL_mysqli();
		self::$db->quick_connect(
			$this->getOption('db_user'),
			$this->getOption('db_pass'),
			$this->getOption('db_name'),
			$this->getOption('db_host', 'localhost'),
			$this->getOption('db_port', 3306),
			'utf8'
		);

		self::$safesql = new \SafeSQL_MySQLi(self::$db->dbh);

		self::$instance = $this;
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
				throw new \FelixOnline\Exceptions\InternalException('"' . $req . '" option is not defined');
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
			self::$instance = new App();
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
			if (func_get_arg(1)) {
				return func_get_arg(1);
			} else {
				throw new \FelixOnline\Exceptions\InternalException('Key "'.$key.'" has not been set');
			}
		}
		return self::$options[$key];
	}

	/**
	 * Get safesql query
	 *
	 * @param string $sql - sql string
	 * $param array $values - array of values for query
	 */
	public static function query($sql, $values)
	{
		return self::$safesql->query($sql, $values);
	}
}

<?php
namespace FelixOnline\Core;
/*
 * Base model class
 *
 * Creates dynamic getter functions for model fields
 */
class BaseModel {
	protected $fields = array(); // array that holds all the database fields
	protected $dbtable; // name of database table
	protected $class;
	protected $item;
	protected $db;
	protected $safesql;
	private $imported = array();
	private $importedFunctions = array();
	protected $filters = array();
	protected $transformers = array();
	protected $initialFields; // Initial fields, used to determine whether the model has been modified
	protected $primaryKey = 'id';

	const TRANSFORMER_NONE = 1;
	const TRANSFORMER_NO_HTML = 2;

	function __construct($fields, $item = NULL) {
		$this->class = get_class($this);
		$this->item = $item;

		if (!is_null($fields)) {
			foreach($fields as $key => $value) {
				if(!empty($this->filters) && array_key_exists($key, $this->filters)) {
					$key = $this->filters[$key];
				}
				$this->fields[$key] = $value;
			}
		} else {
			throw new \FelixOnline\Exceptions\ModelNotFoundException('No model in database', $this->class, $item);
		}

		$this->initialFields = $this->fields;
		return $this->fields;
	}

	/*
	 * Create dynamic functions
	 */
	function __call($method,$arguments) {
		$meth = $this->from_camel_case(substr($method,3,strlen($method)-3));
		$verb = substr($method, 0, 3);
		switch($verb) {
			case 'get':
				if(array_key_exists($meth, $this->fields)) {
					return $this->fields[$meth];
				}
				throw new \FelixOnline\Exceptions\ModelConfigurationException(
					'The requested field "'.$meth.'" does not exist',
					$verb,
					$meth,
					$this->class,
					$this->item
				);
				break;
			case 'set':
				if(array_key_exists($meth, $this->transformers)) {
					switch($this->transformers[$meth]) {
						case self::TRANSFORMER_NO_HTML:
							$this->fields[$meth] = strip_tags($arguments[0]);
							break;
						case self::TRANSFORMER_NONE:
						default:
							$this->fields[$meth] = $arguments[0];
							break;
					}
				} else {
					$this->fields[$meth] = $arguments[0];
				}

				return $this;
				break;
			case 'has':
				if (array_key_exists($meth, $this->fields)) {
					return true;
				}
				return false;
				break;
			default:
				throw new \FelixOnline\Exceptions\ModelConfigurationException(
					'The requested verb is not valid',
					$verb,
					$meth,
					$this->class,
					$this->item
				);
				break;
		}
	}

	/*
	 * Public: Set dbtable
	 */
	public function setDbtable($table) {
		$this->dbtable = $table;
		return $this->dbtable;
	}

	/*
	 * Public: Save all fields to database TODO
	 *
	 * Example:
	 *	  $obj = new Obj();
	 *	  $obj->setTable('comment');
	 *	  $obj->setUser('k.onions');
	 *	  $obj->setContent('hello');
	 *	  $obj->save();
	 */
	public function save() {
		$app = App::getInstance();

		$arrayLength = count($this->fields);
		if (!$arrayLength) {
			throw new \FelixOnline\Exceptions\InternalException('No fields in object');
		}

		if (!$this->dbtable) {
			throw new \FelixOnline\Exceptions\InternalException('No table specified');
		}

		// update model
		if (array_key_exists($this->primaryKey, $this->fields) && $this->fields[$this->primaryKey]) {
			// Determine what has been modified
			$changed = array();
			foreach ($this->initialFields as $field => $value) {
				if ($this->fields[$field] !== $value) {
					$changed[$field] = $this->fields[$field];
				}
			}

			if (!empty($changed)) {
				$sql = $this->constructUpdateSQL($changed);

				$app['db']->query($sql);
				if ($app['db']->last_error) {
					throw new \FelixOnline\Exceptions\InternalException($app['db']->last_error);
				}
			}
		} else { // insert model
			$sql = $this->constructInsertSQL($this->fields);

			$app['db']->query($sql);
			if ($app['db']->last_error) {
				throw new \FelixOnline\Exceptions\InternalException($app['db']->last_error);
			}

			$this->fields[$this->primaryKey] = $app['db']->insert_id;
		}

		return $this->fields[$this->primaryKey]; // return new id
	}

	/**
	 * Private: Construct SQL
	 */
	public function constructInsertSQL($fields) {
		$app = App::getInstance();

		$arrayLength = count($fields);
		$values = array();

		$sql = "INSERT INTO `";

		$sql .= $this->dbtable;

		$sql .= "` (";
		$i = 1; // counter
		foreach($fields as $key => $value) {
			if(array_key_exists($key, $this->filters)) {
				$key = $this->filters[$key];
			}

			$sql .= '`';
			$sql .= $key;
			$sql .= '`';

			if($i !== $arrayLength) {
				$sql .= ', ';
			}
			$i++;
		}

		$sql .= ") VALUES (";

		$i = 1;
		foreach($fields as $key => $value) {
			$sql .= $this->getFieldValue($value, $values);

			if($i != $arrayLength) {
				$sql .= ", ";
			}
			$i++;
		}

		$sql .= ")";

		return $app['safesql']->query($sql, $values);
	}

	/**
	 * Public: Construct update SQL
	 */
	public function constructUpdateSQL($fields) {
		$app = App::getInstance();

		$arrayLength = count($fields);
		$values = array();

		$sql = "UPDATE `";

		$sql .= $this->dbtable;

		$sql .= "` SET ";

		$i = 1;
		foreach($fields as $key => $value) {
			if(array_key_exists($key, $this->filters)) {
				$key = $this->filters[$key];
			}
			$sql .= '`'.$key.'`'."=";

			$sql .= $this->getFieldValue($value, $values);

			if($i != $arrayLength) {
				$sql .= ", ";
			}
			$i++;
		}

		$sql .= " WHERE `" . $this->primaryKey . "`=";
		$primaryKey = $this->fields[$this->primaryKey];
		if (is_numeric($primaryKey)) {
			$sql .= "%i";
		} else {
			$sql .= "'%s'";
		}
		$values[] = $this->fields[$this->primaryKey];

		return $app['safesql']->query($sql, $values);
	}

	/**
	 * Private: Get field value
	 */
	private function getFieldValue($value, &$values) {
		if (is_null($value)) {
			return 'NULL';
		}

		$values[] = $value;

		if (is_numeric($value)) {
			return "%i";
		}

		return "'%s'";
	}

	/*
	 * Public: Set field filters
	 *
	 * $filters - array
	 *
	 * Returns filters
	 */
	public function setFieldFilters($filters) {
		$this->filters = $filters;
		return $this->filters;
	}

	/*
	 * Public: Set field transformers
	 *
	 $ $transformers - array
	 *
	 * Returns transformers
	 */
	public function setTransformers($transformers) {
		$this->transformers = $transformers;
		return $this->transformers;
	}

	/*
	 * Public: Get all fields
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Public: Set field
	 */
	public function setField($field, $value) {
		$this->fields[$field] = $value;
		return $this->fields[$field];
	}

	/**
	 * Public: Set fields
	 */
	public function setFields($fields) {
		$this->fields = $fields;
		return $this->fields;
	}

	/**
	 * Public: Set primary key
	 */
	public function setPrimaryKey($key) {
		$this->primaryKey = $key;
		return $this->primaryKey;
	}

	/*
	 * Convert camel case to underscore
	 * http://www.paulferrett.com/2009/php-camel-case-functions/
	 */
	function from_camel_case($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}
}

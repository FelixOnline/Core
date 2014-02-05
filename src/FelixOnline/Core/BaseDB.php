<?php
namespace FelixOnline\Core;

use FelixOnline\Exceptions\InternalException;
use FelixOnline\Exceptions\ModelNotFoundException;

/**
 * Base DB class
 */
class BaseDB extends BaseModel
{
	public $fields = array(); // array that holds all the database fields
	public $dbtable; // name of database table
	protected $pk;
	protected $initialFields;

	function __construct($fields, $id, $dbtable = null)
	{
		$app = \FelixOnline\Core\App::getInstance();

		if (!is_null($dbtable)) {
			$this->dbtable = $dbtable;
		}

		$this->pk = $this->findPk($fields);

		if (is_null($this->pk)) {
			$fields['id'] = new \FelixOnline\Core\Type\IntegerField(array('primary' => true));
			$this->pk = 'id';
		}

		$fields[$this->pk]->setValue($id);

		$sql = $this->constructSelectSQL($fields);

		// TODO Cache here
		$results = $app['db']->get_row($sql);

		if ($app['db']->last_error) {
			throw new InternalException($app['db']->last_error);
		}

		if (is_null($results)) {
			throw new ModelNotFoundException('No model in database', $this->class);
		}

		foreach ($results as $column => $value) {
			$fields[$column]->setValue($value);
		}

		$this->initialFields = $this->fields;

		parent::__construct($fields);

		/*
		foreach($fields as $key => $value) {
			if(!empty($this->filters) && array_key_exists($key, $this->filters)) {
				$key = $this->filters[$key];
			}
			$this->fields[$key] = $value;
		}
		*/
	}

	/**
	 * Public: Save all fields to database TODO
	 *
	 * Example:
	 *	  $obj = new Obj();
	 *	  $obj->setTable('comment');
	 *	  $obj->setUser('k.onions');
	 *	  $obj->setContent('hello');
	 *	  $obj->save();
	 */
	public function save()
	{
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
	 * Construct the select sql to retrive model from db
	 */
	public function constructSelectSQL($fields)
	{
		$sql = array();

		$sql[] = "SELECT";

		$sql[] = "`" . implode("`, `", array_keys($fields)) . "`";

		$sql[] = "FROM `" . $this->dbtable . "`";
		$sql[] = "WHERE `" . $this->pk . "` = " . $fields[$this->pk]->getSQL();

		return implode(" ", $sql);
	}

	/**
	 * Public: Construct SQL
	 */
	public function constructInsertSQL($fields) {
		$sql = array();

		$sql[] = "INSERT INTO";
		$sql[] = "`" . $this->dbtable . "`";
		$sql[] = "(";

		$columns = array();
		foreach($fields as $key => $value) {
			/* TODO
			if(array_key_exists($key, $this->filters)) {
				$key = $this->filters[$key];
			}
			 */

			$columns[] = "`" . $key . "`";
		}
		$sql[] = implode(", ", $columns);

		$sql[] = ") VALUES (";

		$values = [];
		foreach($fields as $key => $value) {
			$values[] = $value->getSQL();

		}
		$sql[] = implode(", ", $values);
		$sql[] = ")";

		return implode(" ", $sql);
	}

	/**
	 * Public: Construct update SQL
	 */
	public function constructUpdateSQL($fields)
	{
		$sql = array();

		$sql[] = "UPDATE";
		$sql[] = "`" . $this->dbtable . "`";
		$sql[] = "SET";

		$values = array();
		foreach($fields as $key => $value) {
			/* TODO118.97
			if(array_key_exists($key, $this->filters)) {
				$key = $this->filters[$key];
			}
			 */

			// Don't include the primary key
			if ($key == $this->pk) {
				continue;
			}

			$values[] = "`" . $key . "`=" . $value->getSQL();
		}
		$sql[] = implode(", ", $values);

		$sql[] = "WHERE `" . $this->pk . "`=" . $fields[$this->pk]->getSQL();

		return implode(" ", $sql);
	}

	/**
	 * Find pk
	 */
	private function findPk($fields)
	{
		$pk = null;
		foreach ($fields as $column => $field) {
			if ($field->config['primary'] == true) {
				$pk = $column;
				break;
			}
		}
		return $pk;
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
}

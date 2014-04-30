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
	public $pk;
	protected $initialFields;

	function __construct($fields, $id = null, $dbtable = null)
	{
		$app = \FelixOnline\Core\App::getInstance();

		if (!is_null($dbtable)) {
			$this->dbtable = $dbtable;
		}

		if (!is_array($fields) || empty($fields)) {
			throw new \FelixOnline\Exceptions\InternalException('No fields defined');
		}

		if (!$this->dbtable) {
			throw new \FelixOnline\Exceptions\InternalException('No table specified');
		}

		if (!is_null($id)) {
			$this->pk = $this->findPk($fields);

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
		}

		// PHP passes all objects by refernce so we need to clone the fields 
		// so that the initial fields don't get updated when the fields change
		$_fields = array();
		foreach ($fields as $k => $f) {
			$_fields[$k] = clone $f;
		}
		$this->initialFields = $_fields;
		parent::__construct($fields);
	}

	/**
	 * Public: Save all fields to database
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

		// update model
		if ($this->pk && $this->fields[$this->pk]->getValue()) {
			// Determine what has been modified
			$changed = array();
			foreach ($this->initialFields as $column => $field) {
				if ($this->fields[$column]->getValue() !== $field->getValue()) {
					$changed[$column] = $this->fields[$column];
				}
			}

			if (!empty($changed)) {
				$sql = $this->constructUpdateSQL($changed, $this->fields);

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

			$this->pk = $this->findPk($this->fields);

			if ($app['db']->insert_id) {
				$this->fields[$this->pk]->setValue($app['db']->insert_id);
			}
		}

		return $this->fields[$this->pk]->getValue(); // return new id
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
	public function constructInsertSQL($fields)
	{
		$sql = array();

		$sql[] = "INSERT INTO";
		$sql[] = "`" . $this->dbtable . "`";
		$sql[] = "(";

		$columns = array();
		foreach($fields as $key => $value) {
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
	public function constructUpdateSQL($changed, $fields)
	{
		$sql = array();

		$sql[] = "UPDATE";
		$sql[] = "`" . $this->dbtable . "`";
		$sql[] = "SET";

		$values = array();
		foreach($changed as $key => $value) {
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
	private function findPk(&$fields)
	{
		$pk = null;
		foreach ($fields as $column => $field) {
			if ($field->config['primary'] == true) {
				$pk = $column;
				break;
			}
		}

		// If there isn't a primary key defined then add a default one
		if (is_null($pk)) {
			$pk = 'id';
			$fields[$pk] = new \FelixOnline\Core\Type\IntegerField(array('primary' => true));
		}

		return $pk;
	}

	/**
	 * Get data
	 */
	public function getData()
	{
		$data = array();
		foreach($this->fields as $key => $field) {
			if ($field instanceof Type\ForeignKey) { // foreign key exception
				$data[$key] = $field->getRawValue();
			} else {
				$data[$key] = $field->getValue();
			}
		}

		return $data;
	}
}

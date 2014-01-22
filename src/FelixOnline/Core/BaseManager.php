<?php
namespace FelixOnline\Core;

use \FelixOnline\Exceptions\InternalException;

/**
 * Base manager
 */
class BaseManager
{
	/**
	 * database table
	 */
	protected $table;

	/**
	 * object class name
	 */
	protected $class;

	/**
	 * primary key
	 */
	protected $pk = 'id';

	/**
	 * Array of query filters
	 */
	protected $filters = array();

	/**
	 * Order statement
	 */
	protected $order;

	/**
	 * Limit
	 */
	protected $limit;

	/**
	 * Get all objects
	 */
	public function all()
	{
		$_filters = $this->filters; // store filters
		$this->filters = array(); // reset them

		$values = $this->values();

		$this->filters = $_filters; // restore filters

		return $values;
	}

	/**
	 * Filter objects
	 */
	public function filter($filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	/**
	 * Order objects
	 */
	public function order($columns, $order)
	{
		$this->order = array($columns, $order);
		return $this;
	}

	/**
	 * Add limit to query
	 */
	public function limit($offset, $number)
	{
		$this->limit = array($offset, $number);
		return $this;
	}

	/**
	 * Get count
	 */
	public function count()
	{
		$statement = [];

		$statement[] = "SELECT COUNT(`" . $this->pk . "`) AS count";
		$statement[] = $this->getFrom();
		$statement[] = $this->getWhere();
		$statement[] = $this->getOrder();
		$statement[] = $this->getLimit();

		// Remove null values
		$statement = array_filter($statement);

		$sql = $this->safe(implode(" ", $statement));

		$results = $this->query($sql);

		return (int) $results[0]->count;
	}

	/**
	 * Get values
	 */
	public function values()
	{
		$sql = $this->getSQL();

		$results = $this->query($sql);

		$models = $this->resultToModels($results);

		return $models;
	}

	/**
	 * Get sql
	 */
	public function getSQL()
	{
		$statement = [];

		$statement[] = "SELECT `" . $this->pk . "`";
		$statement[] = $this->getFrom();
		$statement[] = $this->getWhere();
		$statement[] = $this->getOrder();
		$statement[] = $this->getLimit();

		// Remove null values
		$statement = array_filter($statement);

		return $this->safe(implode(" ", $statement));
	}

	/**
	 * From
	 */
	protected function getFrom()
	{
		return "FROM `" . $this->table . "`";
	}

	/**
	 * Where
	 */
	protected function getWhere()
	{
		if (!empty($this->filters)) {
			return "WHERE " . implode(" AND ", $this->filters);
		}
		return null;
	}

	/**
	 * Order
	 */
	protected function getOrder()
	{
		if ($this->order) {
			$order = "ORDER BY ";

			if (is_array($this->order[0])) {
				$columns = array();
				foreach ($this->order[0] as $column) {
					$columns[] = "`" . $column . "`";
				}
				$order .= implode(",", $columns);
			} else {
				$order .= "`" . $this->order[0] . "`";
			}

			$order .= " ";

			$order .= $this->order[1];

			return $order;
		}
		return null;
	}

	/**
	 * Limit
	 */
	protected function getLimit()
	{
		if ($this->limit) {
			return "LIMIT " . implode(", ", $this->limit);
		}
		return null;
	}

	/**
	 * Safe
	 */
	protected function safe($sql)
	{
		$app = \FelixOnline\Core\App::getInstance();

		return $app['safesql']->query($sql, array());
	}

	/**
	 * Query sql
	 */
	protected function query($sql)
	{
		$app = \FelixOnline\Core\App::getInstance();

		// TODO cache here

		$results = $app['db']->get_results($sql);

		if ($app['db']->last_error) {
			throw new InternalException($app['db']->last_error);
		}

		if (is_null($results)) {
			throw new InternalException('DB query returned no values');
		}

		return $results;
	}

	/**
	 * Map result to models
	 */
	protected function resultToModels($result)
	{
		$models = array();
		foreach ($result as $r) {
			$pk = $r->{$this->pk};
			$models[] = new $this->class($pk);
		}
		return $models;
	}

	/**
	 * Get object based on id
	 *
	 * $id - array or single id
	 */
	public function get($id)
	{
		if (is_array($id)) {
			$objects = [];
			foreach($id as $i) {
				$objects[] = new $this->class($i);
			}
			return $objects;
		} else {
			$object = new $this->class($id);
			return $object;
		}
	}

}

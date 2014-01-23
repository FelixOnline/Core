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
	public function filter($filter, $values = array())
	{
		$app = \FelixOnline\Core\App::getInstance();

		if (!is_array($values)) {
			throw new InternalException('Values is not an array');
		}

		$this->filters[] = $app['safesql']->query($filter, $values);

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

		$sql = implode(" ", $statement);

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

		if (is_null($results)) {
			return null;
		}

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

		return implode(" ", $statement);
	}

	/**
	 * Get one
	 */
	public function one()
	{
		$_limit = $this->limit;
		$this->limit = null;

		$values = $this->values();

		if (is_null($values)) {
			throw new InternalException('No results');
		}

		if (count($values) > 1) {
			throw new InternalException('More than one result');
		}

		$this->limit = $_limit;
		return $values[0];
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
	 * Query sql
	 */
	protected function query($sql)
	{
		$app = \FelixOnline\Core\App::getInstance();

		// TODO cache here

		set_error_handler(function($errno, $errstr) {
			throw new InternalException($errstr);
		});
		$results = $app['db']->get_results($sql);
		restore_error_handler(); // restore old error handler

		if ($app['db']->last_error) {
			throw new InternalException($app['db']->last_error);
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
}

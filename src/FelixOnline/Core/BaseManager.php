<?php
namespace FelixOnline\Core;

use \FelixOnline\Exceptions\InternalException;
use \FelixOnline\Exceptions\SQLException;

/**
 * Base manager
 */
class BaseManager
{
	/**
	 * database table
	 */
	public $table;

	/**
	 * object class name
	 */
	public $class;

	/**
	 * primary key
	 */
	public $pk = 'id';

	/**
	 * Array of query filters
	 */
	public $filters = array();

	/**
	 * Order statement
	 */
	protected $order;

	/**
	 * Limit
	 */
	protected $limit;

	/**
	 * Joins
	 */
	protected $joins = array();

	/**
	 * Cache flag
	 */
	protected $cache = false;

	/**
	 * Cache expiry
	 */
	protected $cacheExpiry = null;

	public static function build($class, $table, $pk = null)
	{
		$manager = new self();
		$manager->class = $class;
		$manager->table = $table;

		if (!is_null($pk)) {
			$manager->pk = $pk;
		}

		return $manager;
	}

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

		$filter = trim($filter);

		$this->filters[] = "`" . $this->table . "`." . $app['safesql']->query($filter, $values);

		return $this;
	}

	/**
	 * Order objects
	 */
	public function order($columns, $order)
	{
		$colArray = array();

		if(is_array($columns)) {
			foreach($columns as $column) {
				$colArray[] = array($column, $order);
			}
		} else {
			$colArray[] = array($columns, $order);
		}

		$this->multiOrder($colArray);
		return $this;
	}

	/**
	 * Order objects - multiple columns with different sort orders
	 */
	public function multiOrder($columns)
	{
		$this->order = $columns;
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

		$statement[] = "SELECT COUNT(`" . $this->table . "`.`" . $this->pk . "`) AS count";
		$statement[] = $this->getFrom();
		$statement[] = $this->getJoin();
		$statement[] = $this->getWhere();
		$statement[] = $this->getOrder();

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

		$statement[] = "SELECT `" . $this->table . "`.`" . $this->pk . "`";
		$statement[] = $this->getFrom();
		$statement[] = $this->getJoin();
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
	 * Join managers together
	 */
	public function join(BaseManager $manager, $type = null, $column = null)
	{
		$this->joins[$manager->table] = array(
			'manager' => $manager,
			'type' => $type,
			'column' => $column
		);
		return $this;
	}

	/**
	 * Set cache status
	 */
	public function cache($flag, $expiry = null)
	{
		$this->cache = (boolean) $flag;

		if (!is_null($expiry)) {
			$this->cacheExpiry = $expiry;
		}
		return $this;
	}

	/**
	 * From
	 */
	protected function getFrom()
	{
		return "FROM `" . $this->table . "`";
	}

	/**
	 * Get Join
	 */
	protected function getJoin()
	{
		if (!empty($this->joins)) {
			$joins = array();
			foreach ($this->joins as $join) {
				$manager = $join['manager'];
				$st = array();

				if ($join['type']) {
					$st[] = $join['type'];
				}

				if ($join['column']) {
					$column = $join['column'];
				} else {
					$column = $this->pk;
				}
				   
				$st[] = "JOIN `" . $manager->table . "`";

				$st[] = "ON (";
				$st[] = "`" . $this->table . "`.`" . $column . "`";
				$st[] = "=";
				$st[] = "`" . $manager->table . "`.`" . $manager->pk . "`";
				$st[] = ")";
				$joins[] = implode(' ', $st);
			}
			return implode(" ", $joins);
		}
		return null;
	}

	/**
	 * Where
	 */
	protected function getWhere()
	{
		$filters = [];

		if (!empty($this->filters)) {
			$filters = $this->filters;
		}

		if (!empty($this->joins)) {
			foreach ($this->joins as $join) {
				$manager = $join['manager'];
				$filters = array_merge($filters, $manager->filters);
			}
		}

		if (!empty($filters)) {
			return "WHERE " . implode(" AND ", $filters);
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

			$first = true;

			foreach($this->order as $orderItem) {
				if(!$first) {
					$order .= ", ";
				}

				$order .= $this->getColumnReference($orderItem[0]);
				$order .= " ";
				$order .= $orderItem[1];

				$first = false;
			}

			return $order;
		}
		return null;
	}
	
	/**
	 * Get column reference
	 */
	protected function getColumnReference($column)
	{
		// check if table is already defined
		if (count(explode(".", $column)) > 1) {
			return $column;
		}

		return "`" . $this->table . "`.`" . $column . "`";
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

		$item = null;
		if ($this->cache == true) {
			$item = $app['cache']->getItem($this->table, md5($sql));
			$results = $item->get(\Stash\Item::SP_PRECOMPUTE, 300);
		}

		if ($item && !$item->isMiss()) {
			return $results;
		}

		set_error_handler(function($errno, $errstr) {
			throw new InternalException($errstr);
		});
		$results = $app['db']->get_results($sql);
		restore_error_handler(); // restore old error handler

		if ($app['db']->last_error) {
			throw new SQLException($app['db']->last_error, $sql);
		}

		if ($item) {
			if ($this->cacheExpiry) {
				$item->set($results, $this->cacheExpiry);
			} else {
				$item->set($results);
			}
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

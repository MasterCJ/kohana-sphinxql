<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This file is part of SphinxQL for Kohana.
 *
 * Copyright (c) 2010, Deoxxa Development
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package kohana-sphinxql
 */

/**
 * Class for communicating with a sphinx server
 *
 * @package kohana-sphinxql
 * @author MasterCJ <mastercj@mastercj.net>
 * @version 0.1
 * @license http://mastercj.net/license.txt
 */
class Kohana_SphinxQL_Client {
	/**
	 * @var string The address and port of the server this client is to connect to
	 */
	protected $_server = false;
	/**
	 * @var resource A reference to the mysql link that this client will be using
	 */
	protected $_handle = false;
	/**
	 * @var boolean A flag to denote whether or not this client has tried to connect and failed
	 */
	protected $_failed = false;
	/**
	 * @var resource A reference to the mysql result returned by a query that this client has performed
	 */
	protected $_result = false;

	/**
	 * Constructor
	 *
	 * @param string The address and port of a sphinx server
	 */
	public function __construct($server) {
		if (!is_string($server)) { return false; }
		$this->_server = $server;
	}

	/**
	 * Used to attempt connection to the sphinx server, keeps a record of whether it failed to connect or not
	 *
	 * @return boolean Status of the connection attempt
	 */
	protected function connect() {
		if ($this->_handle) { return true; }
		if ($this->_failed) { return false; }
		if ($this->_server === false) { return false; }
		try {
			$this->_handle = mysql_connect($this->_server);
		} catch (Exception $e) {
			$this->_failed = true;
			return false;
		}
		return true;
	}

	/**
	 * Perform a query
	 *
	 * @param string The query to perform
	 * @return SphinxQL_Client This client object
	 */
	public function query($query) {
		$this->_result = false;
		if (is_string($query) && $this->connect()) { $this->_result = mysql_query($query, $this->_handle); }
		return $this;
	}

	/**
	 * Fetch one row of the result set
	 *
	 * @return array|false The row or an error
	 */
	public function fetch_row() {
		if ($this->_result === false) { return false; }
		if ($arr = mysql_fetch_assoc($this->_result)) { return $arr; }
		return false;
	}

	/**
	 * Fetch the whole result set
	 *
	 * @return array|false The results or an error
	 */
	public function fetch_all() {
		if ($this->_result === false) { return false; }
		$ret = array();
		while ($arr = mysql_fetch_assoc($this->_result)) { $ret[] = $arr; }
		return $ret;
	}
}

?>
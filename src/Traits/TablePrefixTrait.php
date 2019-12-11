<?php
namespace App\Traits;

use Cake\Core\Configure;


/**
 * Database Prefix Trait for CakePHP 3.0
 *
 * @author Florian Krämer
 * @license MIT
 */
trait TablePrefixTrait {
	/**
	 * The table prefix to use.
	 *
	 * @var string
	 */
	protected $_tablePrefix = '';
	/**
	 * Getter and setter for the DB prefix
	 *
	 * @param string $prefix
	 * @return string
	 */
	public function tablePrefix($prefix = '') {
		if (empty($prefix)) {
			$connectionConfig = $this->connection()->config();
			if (isset($connectionConfig['prefix'])) {
				$prefix = $connectionConfig['prefix'];
			}
		}
		if (!empty($prefix)) {
			$this->_tablePrefix = $prefix;
		}
		return $this->_tablePrefix;
	}
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		parent::init();
		$this->_tablePrefix();
		if (!empty($this->_tablePrefix)) {
			$this->table = $this->_tablePrefix . $this->table;
		}
		debug($this->table);exit;
	}
	
	public function table($table = null) {
		if (!empty($table)) {
			$table = $this->_tablePrefix . $table;
		}
		return parent::table($table);
	}	
}
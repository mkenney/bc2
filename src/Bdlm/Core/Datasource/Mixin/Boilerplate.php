<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for datasource instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Core\Datasource\Datasource
	 */
	protected $_datasource = null;

	/**
	 * Get the current datasource instance
	 *
	 * @return Core\Datasource\Datasource\Iface\Base|false The current datasource instance, else false
	 */
	public function getDatasource() {
		return $this->_datasource;
	}

	/**
	 * Set a datasource instance for this object to reference
	 *
	 * @param Core\Datasource\Datasource $datasource A datasource instance to reference
	 * @return Core\Datasource\Iface\Base            The current instance
	 */
	public function setDatasource(Core\Datasource\Iface\Base $datasource) {
		$this->_datasource = $datasource;
		return $this;
	}
}

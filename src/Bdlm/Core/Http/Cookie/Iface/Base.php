<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http\Cookie\Iface;

use \Bdlm\Core;

/**
 * Cookie object interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.1
 */
interface Base extends Core\Object\Iface\Base {

	/**
	 * Path get/set wrapper
	 * @param string $var The name of the data object to return
	 */
	public function path($path = null);

	/**
	 * timeout get/set wrapper
	 */
	public function timeout($timeout = null);

	/**
	 * domain get/set wrapper
	 */
	public function domain($domain = null);

	/**
	 * secure get/set wrapper
	 */
	public function secure($secure = null);
}
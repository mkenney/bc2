<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility;

/**
 * Simple data encryption
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.27
 */
class Encryption implements Encryption\Iface\Base {

	use Encryption\Mixin\Aes256;

	public function __construct($key = '', $iv = null) {
		$this->setKey($key);
		if (!is_null($iv)) {
			$this->setIv($iv);
		}
	}
}
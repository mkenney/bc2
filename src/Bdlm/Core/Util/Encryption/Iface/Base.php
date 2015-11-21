<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Util\Encryption\Iface;

use \Bdlm\Core\Util;

/**
 * Simple data encryption
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base {

	/**
	 * Decrypt the information provided
	 *
	 * @param  string $data The encrypted data
	 * @return string The decrypted data
	 */
	public function decrypt($data);

	/**
	 * Encrypt the information provided
	 *
	 * @param  string $data The decrypted data
	 * @return string The encrypted data
	 */
	public function encrypt($data);

	/**
	 * Get an encryption resource
	 *
	 * Generate a new resource if one doesn't exist
	 *
	 * @return resource Encryption descriptor
	 */
	public function getCrypt();

	/**
	 * Get an initialization vector
	 *
	 * Generate a new IV string if one doesn't exist or the current one is invalid
	 *
	 * @see    http://php.net/manual/en/function.mcrypt-generic-init.php
	 * @return string Unique IV string
	 */
	public function getIv();

	/**
	 * Get an initialization vector
	 *
	 * Requires a mcrypt_enc_get_iv_size() length key
	 *
	 * @see    http://php.net/manual/en/function.mcrypt-generic-init.php
	 * @return mixed The current instance
	 */
	public function setIv($iv);

	/**
	 * Get the current encryption key
	 *
	 * @return string The current encryption key
	 */
	public function getKey();

	/**
	 * Set the encryption key
	 *
	 * @return mixed The current instance
	 * @throws \InvalidArgumentException If the given key doesn't conform to minimum requirements
	 */
	public function setKey($key);
}

<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility\Encryption\Mixin;

/**
 * Implementation for \Bdlm\Core\Utility\Encryption\Iface
 *
 * Requires Mcrypt, implements AES-256 encryption.  Returns and expects UTF-8
 * encoded data
 *
 * @see     http://php.net/manual/en/ref.mcrypt.php
 * @author  Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Aes256 {

	/**
	 * Encryption descriptor resource created by mcrypt_module_open()
	 *
	 * @var resource
	 */
	protected $_crypt = null;

	/**
	 * Cookie encryption initialization vector
	 *
	 * @var string
	 */
	protected $_iv = null;

	/**
	 * Encryption key
	 *
	 * @var string
	 */
	protected $_key = '';

	/**
	 * Decrypt the information provided
	 *
	 * @param  string $data The encrypted data
	 * @return string The decrypted data
	 */
	public function decrypt($data) {
		$data = (string) $data;
		$decrypted = '';

		if ('' !== $data) {
			mcrypt_generic_init($this->getCrypt(), $this->getKey(), $this->getIv());

			$decrypted = mdecrypt_generic($this->getCrypt(), utf8_decode($data));

			mcrypt_generic_deinit($this->getCrypt());
			mcrypt_module_close($this->getCrypt());
		}

		return $decrypted;
	}

	/**
	 * Encrypt the information provided
	 *
	 * @param  string $data The decrypted data
	 * @return string The encrypted data
	 */
	public function encrypt($data) {
		$data = (string) $data;
		$encrypted = '';

		if ('' !== $data) {
			mcrypt_generic_init($this->getCrypt(), $this->getKey(), $this->getIv());
			$encrypted = utf8_encode(mcrypt_generic($this->getCrypt(), $data));

			mcrypt_generic_deinit($this->getCrypt());
			mcrypt_module_close($this->getCrypt());
		}

		return $encrypted;
	}

	/**
	 * Get an encryption resource, uses AES-256 encryption
	 *
	 * Generates a new resource if one doesn't exist
	 *
	 * @return resource Encryption descriptor
	 */
	public function getCrypt() {
		if (!is_resource($this->_crypt)) {
			$this->_crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '');
		}
		return $this->_crypt;
	}

	/**
	 * Get an initialization vector
	 *
	 * Generates a new IV string if one doesn't exist or the current one is invalid
	 *
	 * @see    http://php.net/manual/en/function.mcrypt-generic-init.php
	 * @return string Unique IV string
	 */
	public function getIv() {
		$iv_len = mcrypt_enc_get_iv_size($this->getCrypt());
		if (strlen($this->_iv) != $iv_len) {
			$this->_iv = mcrypt_create_iv($iv_len, MCRYPT_RAND);
		}
		return $this->_iv;
	}

	/**
	 * Get an initialization vector
	 *
	 * Requires a mcrypt_enc_get_iv_size() length key
	 *
	 * @see    http://php.net/manual/en/function.mcrypt-generic-init.php
	 * @return mixed The current instance
	 */
	public function setIv($iv) {
		$iv = (string) $iv;
		$iv_len = mcrypt_enc_get_iv_size($this->getCrypt());
		if (strlen($iv) != $iv_len) {
			throw new \InvalidArgumentException("Invalid initialization vector, {$iv_len} characters required");
		}
		$this->_iv = $iv;
		return $this;
	}

	/**
	 * Get the current encryption key
	 *
	 * @return string The current encryption key
	 */
	public function getKey() {
		return (string) $this->_key;
	}

	/**
	 * Set the encryption key
	 *
	 * @return mixed The current instance
	 * @throws \InvalidArgumentException If the encryption key is too long, system dependent
	 */
	public function setKey($key) {
		$key = (string) $key;
		$max_len = mcrypt_enc_get_key_size($this->getCrypt());
		if (strlen($key) > $max_len) {
			throw new \InvalidArgumentException("Key too large, {$max_len} characters allowed");
		}
		$this->_key = (string) $key;
		return $this;
	}
}

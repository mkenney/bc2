<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core;

/**
 * Base view class
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class View extends Object implements View\Iface\Base {

	/**
	 * Array of named paths
	 * @var array
	 */
	protected $_templates = [];
	/**
	 * Array of named template outputs
	 * @var array
	 */
	protected $_templates_rendered = [];

	/**
	 * Initialize this controller
	 *
	 * @param \Bdlm\Core\Object\Iface\Base|array $data Data for this view
	 */
	final public function __construct($data = null) {

		// Convert object instances to arrays
		if ($data instanceof \Bdlm\Core\Object\Iface\Base) {$data = $data->getData();}

		// Save
		if (!is_null($data)) {$this->setData($data);}
	}

	/**
	 * Set a path to a template by name
	 * @param string $name The name a this template
	 * @param string $path The path to the template file
	 */
	public function setTemplate($name, $path) {
		$path = (string) $path;
		$name = (string) $name;

		// sanitize
		while(false !== strpos(DIRECTORY_SEPARATOR.'.', $path)) {$path = str_replace(DIRECTORY_SEPARATOR.'.', DIRECTORY_SEPARATOR, $path);}
		while(false !== strpos('.'.DIRECTORY_SEPARATOR, $path)) {$path = str_replace('.'.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);}
		while(false !== strpos(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $path)) {$path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);}

		// Exception if file does not exist
		if (!is_file($path)) {throw new \RuntimeException("'{$original_path}' is not a valid template name.");}

		$this->_templates[$name] = $path;
		unset($this->_templates_rendered[$name]);

		return $this;
	}

	/**
	 * Get the path of a named template
	 * @param string $name The name a this template
	 * @param string $path The path to the template file
	 */
	public function getTemplate($name) {
		$name = (string) $name;
		$ret_val = null;
		if (isset($this->_templates[(string) $name])) {
			$ret_val = (string) $this->_templates[$name];
		}
		return $ret_val;
	}

	/**
	 * Remove a named template path and any rendered output from the stack
	 * @param string $name The name a this template
	 */
	public function deleteTemplate($name) {
		unset($this->_templates[$name]);
		unset($this->_templates_rendered[$name]);
		return $this;
	}

	/**
	 * Execute a template file and return the output string
	 * @param  string  $name  The name of the template to render
	 * @param  boolean $force If true, force re-rendering a previously rendered
	 *                        template file
	 * @return string
	 */
	public function renderTemplate($name, $force = false) {
		$rendered_template = '';

		if (!isset($this->_templates[$name])) {throw new \RuntimeException("'{$name}' is not a defined template name");}

		if (
			!isset($this->_templates_rendered[$name])
			|| $force
		) {
			$rendered_template = call_user_func(function($path) {
				$ret_val = '';
				if (is_file($path)) {
					ob_start();
					require($path);
					$ret_val = ob_get_contents();
					ob_end_clean();
				}
				return $ret_val;
			}, $this->getTemplate($name));

			$this->_templates_rendered[$name] = $rendered_template;
		}

		return $this->_templates_rendered[$name];
	}

	/**
	 * Render all templates in the order they are defined
	 * @return string
	 */
	public function renderTemplates() {
		$templates_rendered = '';
		foreach (array_keys($this->_templates) as $name) {
			$templates_rendered .= $this->renderTemplate($name);
		}
		return $templates_rendered;
	}
}

<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\View;

/**
 * Html controller class
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Html implements Html\Iface\Base {

	/**
	 * List of CSS strings with an optional media attribute
	 * 	[css string] => media attribute
	 * @var array
	 */
	protected $_css_strings = [];
	/**
	 * List of CSS URLs with an optional media attribute
	 * 	[css url] => media attribute
	 * @var array
	 */
	protected $_css_urls = [];
	/**
	 * List of Javascript strings
	 * 	[js string] => null
	 * @var array
	 */
	protected $_js_strings = [];
	/**
	 * List of Javascript strings
	 * 	[js url] => null
	 * @var array
	 */
	protected $_js_urls = [];

	/**
	 * Add a section of CSS code to the stack
	 * @param  string $css_string CSS code
	 * @return Html\Iface\Base
	 */
	public function addCssString($css_string, $media = null) {
		$css_string = (string) $css_string;
		if (!is_null($media)) {
			$media = (string) $media;
		}

		$this->_css_strings[$css_string] = $media;
		return $this;
	}

	/**
	 * Add a CSS URL to the stack
	 * @param  [type] $css_url [description]
	 * @return Html\Iface\Base
	 */
	public function addCssUrl($css_url, $media = null) {
		$css_url = (string) $css_url;
		if (!is_null($media)) {
			$media = (string) $media;
		}
		$this->_css_urls[$css_url] = $media;
		return $this;
	}

	/**
	 * Add a section of javascript code to the stack
	 * @param [type] $js_string [description]
	 * @return Html\Iface\Base
	 */
	public function addJsString($js_string) {
		$js_string = (string) $js_string;
		$this->_js_strings[$js_string] = null;
		return $this;
	}

	/**
	 * Add a script URL to the stack
	 * @param  [type] $js_url [description]
	 * @return Html\Iface\Base
	 */
	public function addJsUrl($js_url) {
		$js_url = (string) $js_url;
		$this->_js_urls[$js_url] = null;
		return $this;
	}

	/**
	 * Output all defined css in stack order.  URLs are output first, followed
	 * by raw CSS definitions.
	 * @return string [description]
	 */
	public function renderCss() {
		$css = [];
		foreach ($this->_css_urls as $url => $media) {
			$media_attr = '';
			if (!is_null($media)) {
				$media_attr = "media=\"{$media}\"";
			}
			$css[] =
<<<HTML
<link rel="stylesheet" type="text/css" href="{$url}" {$media}/>
HTML;
		}
		foreach ($this->_css_strings as $string => $media) {
			$media_attr = '';
			if (!is_null($media)) {
				$media_attr = "media=\"{$media}\"";
			}
			$css[] =
<<<HTML
<style type="text/css" {$media_attr}/>
{$string}
</style>
HTML;
		}
		return implode("\n", $css);
	}

	/**
	 * Output all defined javascript in stack order.  URLs are output first, followed
	 * by raw javascript code.
	 * @return string [description]
	 */
	public function renderJs() {
		$js = [];
		foreach (array_keys($this->_js_urls) as $url) {
			$js[] =
<<<HTML
<script type="text/javascript" src="{$url}"></script>
HTML;
		}
		foreach (array_keys($this->_js_strings) as $string) {
			$js[] =
<<<HTML
<script type="text/javascript">
{$string}
</script>
HTML;
		}
		return implode("\n", $js);
	}
}

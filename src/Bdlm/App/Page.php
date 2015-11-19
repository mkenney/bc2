<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\App;

use \Bdlm\Core;

/**
 * Basic page controller
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.27
 */
class Page extends \Bdlm\Core\Controller {

	use Core\Utility\Encryption\Mixin\Boilerplate;

	use Core\Http\Cookie\Mixin\Boilerplate;

	public function __construct(
		Core\Model\Iface\Base $model,
		Core\View\Iface\Base $view,
		Core\Utility\Config\Iface\Base $config
	) {
		parent::__construct($model, $view, $config);

		$this->setName('page');
		$this->setCookie(new Core\Http\Cookie($this->getName()));
	}

	public function getTitle() {
		return $this['title'];
	}

	public function setTitle($title) {
		$this['title'] = (string) $title;
		return $this;
	}


}

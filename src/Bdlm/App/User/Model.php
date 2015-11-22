<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\App\User;

/**
 * User model
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Model extends \Bdlm\Core\Model {
    public function __construct(\Bdlm\Core\Datasource\Iface\Base $datasource = null) {
        parent::__construct($datasource);

    }
}

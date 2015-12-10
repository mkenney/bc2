<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Util\Log\Iface;

/**
 *
 * @author  Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base {
    /**
     * Trace logging
     * debug-level message used for production support. This is logged as LOG_DEBUG
     */
    const TRACE = 0;
    /**
     * Debug-level messages
     */
    const DEBUG = 1;
    /**
     * Informational messages
     */
    const INFO  = 2;
    /**
     * Normal, but significant, condition
     */
    const NOTICE  = 3;
    /**
     * Warning conditions
     *
     * Use in all environments to log information about minor errors (bad user input,
     * bad external data, etc.) that can be recovered from gracefully. Warnings
     * should not halt system execution in development environments.
     *
     * This should be considered a low-priority tech-debt support issue
     */
    const WARN  = 4;
    /**
     * Error condition exists
     *
     * Use in all environments to log information about serious errors that do not
     * halt system execution. Errors _should_ halt system execution in development
     * environments.
     *
     * This should be considered a high-priority tech-debt support issue
     */
    const ERR = 5;
    /**
     * Crititical condition exists
     *
     * Use in all environments for unhandled exceptions. Halt system execution
     *
     * This should be considered a tier-3 support issue
     */
    const CRIT = 6;
    /**
     * Action must be taken
     *
     * Use in all environments to signify a support issue. Halt system  execution.
     *
     * This should be considered a tier-2 support issue
     */
    const ALERT = 7;
    /**
     * System is unusable
     *
     * Use in all environments to signify an emergency support issue. Halt system
     * execution.
     *
     * This should be considered a tier-1 support issue
     */
    const EMERG = 8;
    /**
     * Custom DB connection error code
     * 10 has no real significance, just not already taken by another error code
     */
    const E_DB_CONNECT = 10;
   /**
     * Initialize the logger object - sets the environment (dev/test/prod) and the site name to use in logging
     *
     * @param string $env       environment server is runing in (dev, test, prod, etc...)
     * @param string $site_name name of site being run
     * @param page $page
     * @param skin $skin
     */
    public static function init($env);
    /**
     * Default exception handler
     *
     * @param \Exception $e
     */
    public static function exceptionHandler($e);
    /**
     * Default error handler
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @throws ErrorException
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline);
    /**
     * format and output to syslog
     *
     * @param unknown_type $level
     * @param unknown_type $msg
     * @param unknown_type $vals
     * @return boolean
     */
    public static function message($level, $msg, $vals = null);
}

<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Util\Log\Mixin;

/**
 * Implementation for \Bdlm\Core\Util\Log\Iface
 *
 * @author  Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Base {
    /**
     * 'dev' / 'test' / 'prod'
     * @var string
     */
    protected static $_env;

    /**
     * initialize the logger object - sets the environment (dev/test/prod) and the site name to use in logging
     *
     * @param string $env       environment server is runing in (dev, test, prod, etc...)
     * @param page $page
     * @param skin $skin
     */
    public static function init($env) {
        self::$_env = $env;
    }

    /**
     * Default error handler
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @throws ErrorException
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {

        // ignore errors suppressed by '@'
        $error_level = error_reporting();
        if ($error_level == 0) {return;}
        $error = [];
        $error['error'] = strip_tags($errstr);
        $error['level'] = self::_getSeverityLevel($errno, $errstr);

        // if fatal or error, throw an exception.
        if (
            $error['level'] == self::ERR
            || $error['level'] == self::CRIT
            || $error['level'] == self::ALERT
            || $error['level'] == self::EMERG
        ) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);

        } else {
            $error['type'] = self::_getErrorType($errno);
            $error['info'] = self::_getDebugValStr();
            $error['file'] = $errfile;
            $error['line'] = $errline;

            $backtrace = debug_backtrace();

            // find true error from backtrace, replace error if it's different
            $origin_key = self::_getErrorOrigin($backtrace);
            if (isset($backtrace[$origin_key]['file']) && isset($backtrace[$origin_key]['line']) && $backtrace[$origin_key]['file'] != $error['file'] && $backtrace[$origin_key]['line'] != $error['line']) {
                $origin_file = $backtrace[$origin_key]['file'];
                $origin_line = $backtrace[$origin_key]['line'];
            }

            // if this is a warning (or below), just log it and return, don't stop script execution
            $log_vals['file'] = $error['file'];
            $log_vals['line'] = $error['line'];
            if (isset($origin_file)) {
                $log_vals['origin'] = "$origin_file($origin_line)";
            }
            $log_vals['info'] = self::_getDebugValStr();
            self::message($error['level'], $errstr, $log_vals);

            if ('cli' === PHP_SAPI) {
                fwrite(STDERR, "<error>" . print_r($backtrace, true) . "</error>\n");

            // if this is dev, throw the debug page, otherwise, return and continue script execution
            } elseif (self::$_env == 'dev') {

                // clear the output buffer
                while (ob_get_level()) {ob_end_clean();}

                self::_renderDebugErrorPage($error, $backtrace, $origin_key);
            }

            return;
        }
    }

    /**
     * Default exception handler
     *
     * @param \Exception $e
     */
    public static function exceptionHandler($e) {
        if (!method_exists($e, 'getSeverity')) {$severity = 1;}
        else {$severity = $e->getSeverity();}

        $error = [];
        $error['error'] = strip_tags($e->getMessage());
        $error['level'] = self::_getSeverityLevel($severity, $error['error']);
        $error['type']  = self::_getErrorType($severity);
        $error['info']  = self::_getDebugValStr();
        $error['file']  = $e->getFile();
        $error['line']  = $e->getLine();

        $backtrace = $e->getTrace();

        // find true error from backtrace, replace error if it's different
        $origin_key = self::_getErrorOrigin($backtrace);
        if (
            isset($backtrace[$origin_key]['file'])
            && isset($backtrace[$origin_key]['line'])
            && $backtrace[$origin_key]['file'] != $error['file']
            && $backtrace[$origin_key]['line'] != $error['line']
        ) {
            $origin_file = $backtrace[$origin_key]['file'];
            $origin_line = $backtrace[$origin_key]['line'];
        }

        // set values for logging
        $log_vals = [];
        $log_vals['file'] = $error['file'];
        $log_vals['line'] = $error['line'];
        if (isset($origin_file)) {
            $log_vals['origin'] = "{$origin_file} ({$origin_line})";
        }
        $log_vals['info'] = $error['info'];

        self::message($error['level'], $error['error'], $log_vals);

        // clear the output buffer
        while (ob_get_level()) {ob_end_clean();}

        // Output to stderr for the CLI sapi
        if ('cli' === PHP_SAPI) {
            fwrite(STDERR, "<exception>" . print_r($backtrace, true) . "</exception>\n");

        // Display a full page of error information in development environments
        } elseif (self::$_env == 'dev') {
            self::_renderDebugErrorPage($error, $backtrace, $origin_key);

        // Display a simple error page in non-development environments
        } else {
            self::_renderErrorPage();
        }
    }

    /**
     * format and output to syslog
     *
     * @param int $level
     * @param string $msg
     * @param array $vals
     * @return boolean
     */
    public static function message($level, $msg, $vals = null) {

        // associate these logging levels with syslog levels
        $log_levels = [
            self::TRACE  => LOG_DEBUG,
            self::DEBUG  => LOG_DEBUG,
            self::INFO   => LOG_INFO,
            self::NOTICE => LOG_NOTICE,
            self::WARN   => LOG_WARNING,
            self::ERR    => LOG_ERR,
            self::CRIT   => LOG_CRIT,
            self::ALERT  => LOG_ALERT,
            self::EMERG  => LOG_EMERG,
        ];

        if (!isset($log_levels[$level])) {
            $level = self::ERR;
        }

        $output = "[{$level}] {$msg}";

        $output .= ' File: ';
        if (isset($vals['file'])) {
            $output .= $vals['file'];
            unset($vals['file']);
        } else {
            $output .= $_SERVER['SCRIPT_FILENAME'];
        }
        if (isset($vals['line'])) {
            $output .= "({$vals['line']})";
            unset($vals['line']);
        }

        if (! empty($vals)) {
            foreach ($vals as $key => $value) {
                if (strlen($value) > 0) {
                    $output .= " {$key}:{$value}";
                }
            }
        }

        $output .= ' Site:' . self::$_env;
        if(isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
            $output .= " URL: {$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
        }

        openlog('bdlm', LOG_ODELAY | LOG_PID, LOG_LOCAL4);
        $ret_val = syslog($log_levels[$level], $output);;
        closelog();

        // In development environments halt execution and throw an exception for
        // all errors higher than warning
        if ('dev' === self::$_env && $level > self::WARN) {
            throw new \Exception($msg, $level);
        }

        return $ret_val;
    }

    /**
     * Try to determine the correct root cause of error in backtrace, and returns
     * the key to that error. Skips over require and include statements.
     *
     * @param array $backtrace A backtrace array
     * @return int  The array key corresponding
     */
    protected static function _getErrorOrigin($backtrace) {
        $origin_key = (count($backtrace) - 1);
        // skip over include/require statements
        $skip = array('require', 'require_once', 'include', 'include_once');
        for($i = $origin_key; $i >= 0; $i --) {
            if (
                isset($backtrace[$i]['function'])
                && ! in_array($backtrace[$i]['function'], $skip)
                && isset($backtrace[$i]['file'])
                && isset($backtrace[$i]['line'])
            ) {
                $origin_key = $i;
                break;
            }
        }

        return $origin_key;
    }

    /**
     * get display name for type of error
     *
     * @param int $errno
     * @return string
     */
    protected static function _getErrorType($errno) {
        $error_types = array(
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parsing Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice',
            self::E_DB_CONNECT => 'DB Connect Error',
        );

        // get error type
        $error_type_name = 'Unknown Error';
        if (isset($error_types[$errno])) {
            $error_type_name = $error_types[$errno];
        }
        return $error_type_name;
    }

    /**
     * figure out what severity level based on the type of PHP error being thrown
     *
     * @param int $errno
     * @param string $errstr
     * @return string
     */
    protected static function _getSeverityLevel($errno, $errstr) {
        $ret_val = self::ERR;
        switch ($errno) {
            case E_ERROR :
            case E_PARSE :
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
            case self::E_DB_CONNECT :
                $ret_val = self::CRIT;
            case E_USER_ERROR :
                $ret_val = self::ERR;
            case E_WARNING :
                if (preg_match('/ORA-/', $errstr)) {
                    $ret_val = self::ERR;
                }
            case E_CORE_WARNING :
            case E_COMPILE_WARNING :
            case E_NOTICE :
            case E_USER_WARNING :
            case E_USER_NOTICE :
            case E_STRICT :
            case E_DEPRECATED :
                $ret_val = self::WARN;
        }
        return $ret_val;
    }

    /**
     * Formats defined debug vals into a string
     * @return string
     */
    protected static function _getDebugValStr() {
        $vals = '';

        if (! empty(self::$debug_vals)) {
            foreach (self::$debug_vals as $key => $value) {
                $vals .= "$key=";
                if (is_array($value)) {
                    $vals .= var_export($value, true);
                } else {
                    $vals .= $value;
                }
                $vals .= ', ';
            }
            $vals = substr($vals, 0, (strlen($vals) - 2));
        }

        return $vals;
    }

    /**
     * this will display a nice error page for test/prod environments
     * it's important that we don't make database calls (without calling $db->check_connection()
     * first), otherwise, we'll get stuck in an error loop this can be customized
     * for each website, depending on how pages are created.
     *
     * @todo This is dependent on too many external classes.  It should be moved or rewritten.
     * @param string $message
     * @param string $page_title
     */
    public static function _renderErrorPage($message, $page_title) {
        echo
<<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
</head>
<body>
    <p>There was an error, we apologize for the inconvenience.</p>
</body>
</html>
HTML;
        exit;
    }

    /**
     * this will display a debug error page for development purposes
     * this should not be called in test or prod environments
     *
     * @param array $error
     * @param array $backtrace
     * @param int $origin_key
     */
    protected static function _renderDebugErrorPage($error, $backtrace, $origin_key = 0) {
        $error_page =
<<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Error!</title>
    <style type="text/css">
        body {
            font-family: Verdana, Geneva, sans-serif;
            font-size: 11px;
            margin: 15px;
        }
        .error_table {
            border: solid 1px #999999;
            border-collapse: collapse;
        }
        .error_table td {
            border: solid 1px #999999;
        }
        .error_table th {
            border: solid 1px #999999;
            background-color: #EAEAEA;
            font-weight: bold;
            text-align: left;
        }
        .error_table th.vert {
            text-align: right;
        }
        .backtrace {
            font-family: "Courier New", Courier, monospace;
            font-size: 13px;
            background-color: #EAEAEA;
            padding: 8px;
            margin-top: 0;
            border: solid 1px #CCCCCC;
        }
        .backtrace_highlight {
            color: red;
            font-weight: bold;
        }
        h1 {
            font-size: 24px;
        }
        h2 {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>[{$error['level']}] {$error['error']}</h1>
    <table border="0" cellpadding="4" cellspacing="0" class="error_table">
HTML;

        foreach ($error as $error_title => $error_value) {
            if (strlen($error_value) > 0) {
                $error_page .=
<<<HTML
    <tr>
        <th class="vert">{$error_title}:</th>
        <td>{$error_value}</td>
    </tr>
HTML;
            }
        }
        $error_page .=
<<<HTML
</table>
<h2>Backtrace</h2>
<pre class="backtrace">
HTML;
        // create backtrace output
        // copied logic from the Exception::getTraceAsString method
        foreach ($backtrace as $key => $item) {
            $str = "#$key ";
            if (! empty($item['file'])) {
                $line = (! empty($item['line'])) ? $item['line'] : 0;
                $str .= "{$item['file']}($line): ";
            } else {
                $str .= '[internal function]: ';
            }
            if (! empty($item['class'])) {
                $str .= $item['class'];
            }
            if (! empty($item['type'])) {
                $str .= $item['type'];
            }
            if (! empty($item['function'])) {
                $str .= $item['function'] . '(';
                if (! empty($item['args'])) {
                    $arg_count = count($item['args']);
                    foreach ($item['args'] as $arg_key => $arg) {
                        if (is_null($arg)) {
                            $str .= 'NULL';
                        } elseif (is_string($arg)) {
                            $str .= "'";
                            if (strlen($arg) > 15) {
                                $str .= substr($arg, 0, 15) . '...';
                            } else {
                                $str .= $arg;
                            }
                            $str .= "'";
                        } elseif (is_bool($arg)) {
                            $str .= ($arg) ? 'true' : 'false';
                        } elseif (is_resource($arg)) {
                            $str .= 'Resource id #' . (int)$arg;
                        } elseif (is_int($arg) || is_float($arg)) {
                            $str .= $arg;
                        } elseif (is_array($arg)) {
                            $str .= 'Array';
                        } elseif (is_object($arg)) {
                            $str .= 'Object(' . get_class($arg) . ')';
                        }
                        if (($arg_key + 1) < $arg_count) {
                            $str .= ', ';
                        }
                    }
                }
                $str .= ')';
            }
            if ($key == $origin_key) {
                $error_page .=
<<<HTML
    <span class="backtrace_highlight">{$str}</span>

HTML;
            }
            $error_page .= "\n";
        }
        $backtrace_count = count($backtrace);
        $error_page .=
<<<HTML
#{$backtrace_count} {main}
</pre>

<h2>Super Globals</h2>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="error_table">
HTML;
        foreach ($GLOBALS as $global_key => $global_var) {
            if ($global_key[0] == '_' && is_array($global_var)) {
                $error_page .=
<<<HTML
    <tr><th colspan="2">\${$global_key}</th></tr>
HTML;
                foreach ($global_var as $key => $value) {
//                    $key = RP_Utility::escapeHtml($key);
                    $error_page .=
<<<HTML
    <tr><td>{$key}</td><td>
HTML;
                    if (is_array($value)) {
//                        $error_page .= RP_Utility::escapeHtml(var_export($value, true));
                        $error_page .= var_export($value);
                    } else {
//                        $error_page .= RP_Utility::escapeHtml($value);
                        $error_page .= $value;
                    }
                    $error_page .= '</td></tr>';
                }
            }
        }
        $error_page .=
<<<HTML
</table>
</body>
</html>
HTML;

        echo $error_page;
        exit;
    }

}

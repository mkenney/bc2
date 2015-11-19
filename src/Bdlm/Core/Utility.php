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
 * Common utilities
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
class Utility {

	/**
	 * Convert &lt;br /&gt; tags to newlines (reverse of the PHP nl2br() function)
	 *
	 * @param string $string
	 * @return string The converted string
	 * @version 0.17
	 */
	public static function br2nl($string) {

		//
		// Strict typing
		//
		$string = (string) $string;

		//
		// Always reformat line endings, do this first
		//
		$string = str_replace("\r\n", "\n", $string);
		$string = str_replace("\r", "\n", $string);

		//
		// Replace break tags ignoring letter case
		//
		$string = str_ireplace("<br />\n", "\n", $string);
		$string = str_ireplace("<br>\n", "\n", $string);
		$string = str_ireplace("<br />", "\n", $string);
		$string = str_ireplace("<br>", "\n", $string);

		return $string;
	}

	/**
	 * Debug assistance
	 *
	 * @return void
	 * @version 0.03
	 */
	public static function debug() {
		$backtrace = array_reverse(debug_backtrace());
		$arguments = func_get_args();
		$total_arguments = count($arguments);
		echo
<<<HTML
<style type="text/css">
fieldset.bc2-debug               {background: #bbb; border: 3px solid green; border-radius: 10px; padding: 5px; margin: 5px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; max-width: 1000px; min-width: 1000px; width: 1000px;}
fieldset.bc2-debug div.pre       {white-space: pre; border-radius: 3px; background: #000; color: #aaa; margin-top: 0.25em; padding: 0 0 1em 1em; overflow: scroll; font-family: monospace;}
fieldset.bc2-debug div.backtrace {white-space: pre; border-radius: 3px; background: #000; color: #aaa; padding: 0.5em; margin: 1px 0; overflow: scroll; font-family: monospace;}
fieldset.bc2-debug .title        {font-style: italic;}
fieldset.bc2-debug hr.section    {padding: 0; margin-bottom: 2em; border: none; border-top: medium double #333; color: #333; text-align: center;}
</style>
<fieldset class="bc2-debug">
	<strong><code><i>\Bdlm\Core\Utility::debug({{$total_arguments} args}])</i></code></strong>
	<hr class="section" />
HTML;
		$a = 0;
		while (list($b, $argument) = each($arguments)) {
			$print_r_output = trim(print_r($argument, true));

			ob_start();
			var_dump($argument);
			$var_dump_output = trim(ob_get_contents());
			ob_end_clean();

			$a++;

			echo
<<<HTML
<strong>Argument #{$a} of {$total_arguments}</strong>:
<br />
<span class="title">
<code>print_r(\$arg{$a})</code> output:
</span>
<div class="pre">
{$print_r_output}
</div>

<br />
<span class="title">
<code>var_dump(\$arg{$a})</code> output:
</span>
<div class="pre">
{$var_dump_output}
</div>
<hr class="section" />
HTML;
		}

		$_debug_backtrace = debug_backtrace();
			echo
<<<HTML
<strong>Backtrace:</strong>
<br />
HTML;
		while (list($b, $callee) = each($_debug_backtrace)) {

			if (isset($callee['file']) && isset($callee['line'])) {
				echo
<<<HTML
<div class="backtrace">
{$b}: {$callee['file']} @ line: {$callee['line']}
</div>
HTML;
			} elseif (isset($callee['class']) && isset($callee['type']) && isset($callee['function'])) {
				$args = count($callee['args']);
				echo
<<<HTML
<div class="backtrace">
{$b}: {$callee['class']}{$callee['type']}{$callee['function']}({{$args} args})
</div>
HTML;

			} elseif (isset($callee['function']) && isset($callee['args'])) {
				$callee['function'] = str_replace(array('{', '}'), '', $callee['function']);
				$closure_args = count($callee['args']);
				echo
<<<HTML
<div class="backtrace">
{$b}: <i>{$callee['function']}({{$closure_args} args})</i>
</div>
HTML;

			} else {
				print_r($callee);
			}
			$a = 0;
		}

		echo
<<<HTML
</fieldset>
HTML;
	}

	/**
	 * Convert a case-sensitive base-69 value into an integer.
	 * 14 digits maximum.  Decoding is 54% faster than encoding.
	 *
	 * @param integer $num The integer to encode.  If true, return the digits array instead.
	 * @return string The encoded value, if $num is true then return the digits array
	 * @version 0.06
	 */
	public static function decodeId($encoded_num) {

		//
		// Define our available "digits"
		//
		$digits = array_flip([
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
			'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
			'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd',
			'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
			'y', 'z',

			//
			// Only URL-safe special characters should be used
			// Underscore removed because it's hard to see in an underlined link
			//
			'-', '~', "'", '!', '*', '(', ')',
		]);

		//
		// Hack to get the digits array in case we need it.
		//
		if (true === $encoded_num) {
			$ret_val = $digits;
		} else {
			$ret_val = 0.0;
			for ($i = strlen($encoded_num) - 1; $i >= 0; $i--) {
				$ret_val += $digits[substr($encoded_num, $i - 1, 1)] * pow(count($digits), (strlen($encoded_num) - ($i + 1)));
			}
		}
		return $ret_val;
	}

	/**
	 * Convert an integer into a case-sensitive base-69 value.
	 * 14 digits maximum.  Decoding is 54% faster than encoding.
	 *
	 * @param integer|true $num The integer to encode.  If true, return the digits array instead.
	 * @return string The encoded value, if $num is true then return the digits array
	 * @version 0.06
	 */
	public static function encodeId($num) {

		//
		// Define our available "digits"
		//
		$digits = [
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
			'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
			'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd',
			'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
			'y', 'z',

			//
			// Only URL-safe special characters should be used
			// Underscore removed because it's hard to see in an underlined link
			//
			'-', '~', "'", '!', '*', '(', ')',
		];

		//
		// Hack to get the digits array in case we need it.
		//
		if (true === $num) {
			$ret_val = $digits;
		} else {

			$cur = floatval($num);
			$chars = [];

			do {
				$mod = fmod($cur, count($digits));
				$cur = floor($cur / count($digits));
				$chars[] = $digits[$mod];
			} while ($cur > 0);
			$ret_val = implode('', array_reverse($chars));
		}

		return $ret_val;
	}

	/**
	 * Legacy support
	 *
	 * @param string $string
	 * @return string The converted string
	 * @version 0.01
	 * @deprecated
	 */
	public static function event($level, $message) {
		debug("$level: $message");
		logevent('warning', "Deprecated method 'event()' called");
	}

	/**
	 * Get a users IP address bypassing proxies if possible
	 *
	 * @return string|false The users IP address or false on failure
	 * @version 0.31
	 */
	public static function getIp() {

		// Defalut false in case of failure
		$ip_addr = false;

		// Check the $_SERVER global first
		if (count($_SERVER) > 1) {

			// Check originating address if forwarded by a proxy
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];

			// Check for the client ip
			} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$ip_addr = $_SERVER["HTTP_CLIENT_IP"];

			// Default to the remote address
			} elseif (isset($_SERVER["REMOTE_ADDR"])) {
				$ip_addr = $_SERVER["REMOTE_ADDR"];
			}

		// Check environment variables if the $_SERVER global isn't available for some reason
		} else {

			// Check originating address if forwarded by a proxy
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$ip_addr = getenv('HTTP_X_FORWARDED_FOR');

			// Check for the client ip
			} elseif (getenv('HTTP_CLIENT_IP')) {
				$ip_addr = getenv('HTTP_CLIENT_IP');

			// Default to the remote address
			} elseif (getenv('REMOTE_ADDR')) {
				$ip_addr = getenv('REMOTE_ADDR');
			}
		}

		// IP address on success, false on failure
		return $ip_addr;
	}

	/**
	 * Get an HTML variable from this page load.
	 * We are changing the way GET and POST data is handled, POST data will no longer be used
	 * for page navigation and GET data will no longer be used to store form data, therefore
	 * you must now specify if you want to pull a variable from the POST data, by default all
	 * variables will be pulled from the GET data.
	 *
	 * @param string $name The variable name.
	 * @param string $default A default value to use if a value for $name cannot be found, if any.
	 * @param string $type Either "get" or "post", default "get"
	 * @return string The value of the HTTP variable.
	 * @version 0.02
	 */
	public static function httpVar($name, $type = "get", $default = "") {
		$name = (string) $name;
		$default = (is_array($default) ? (array) $default : (string) $default);
		$type = strtolower($type);

		$ret_val = null;

		switch ($type) {
			case "get":
				if (isset($_GET[$name])) {
					$ret_val = $_GET[$name];
				}
			break;

			case "post":
				if (isset($_POST[$name])) {
					$ret_val = $_POST[$name];
				}
			break;

			default:
				event("error", "Invalid request type '$type', must be either 'get' or 'post'.");
			break;
		}
		if (is_null($ret_val)) {
			$ret_val = $default;
		}

		return $ret_val;
	}

	/**
	 * Validate an email address
	 * Thanks to http://www.ilovejackdaniels.com/
	 *
	 * @param string $email The email address to validate
	 * @return bool
	 * @version 0.22
	 * @deprecated  Use filter_var($email, FILTER_VALIDATE_EMAIL)
	 */
	public static function isValidEmail($email) {
		$ret_val = true;

		//Check that there's one @ symbol, and that the length is right
		if (0 === preg_match(">^[^@]{1,64}@[^@]{1,255}$>", $email)) {
			$ret_val = false;
		}

		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);

		// Check the username portion of the address... why is it exploded by '.'?
		for ($i = 0; $i < count($local_array); $i++) {
			if (0 === preg_match(">^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\\.-]{0,63})|(\\\"[^(\\|\\\")]{0,62}\\\"))$>", $local_array[$i])) {
				$ret_val = false;
			}
		}

		// Check if domain is an IP address, otherwise it should be valid domain name
		if (0 === preg_match(">^[0-9]\\.[0-9]\\.[0-9]\\.[0-9]$>", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);

			// Ends in a period
			if ('' === trim($domain_array[count($domain_array) - 1])) {
				$ret_val = false;
			}

			// Must have at least two parts.  Private hostnames are not supported
			if (count($domain_array) < 2) {
				$ret_val = false;
			}

			// Each domain part must have some letters or numbers... whatever
			for ($i = 0; $i < count($domain_array); $i++) {
				if (0 === preg_match(">^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$>", $domain_array[$i])) {
					$ret_val = false;
				}
			}
		}

		return $ret_val;
	}

	/**
	 * Validate numeric values using either is_numeric or ctype_digit PHP functions.
	 *
	 * @param mixed $num The value to test
	 * @param bool $force_int If true, do not return matches for floats
	 * @return bool True if $num is numeric, else false
	 * @version 0.13
	 */
	public static function isNum($num, $force_int = false) {
		if (false === $force_int) {
			$ret_val = is_numeric($num);
		} else {
			$ret_val = ctype_digit((string) $num);
		}

		return $ret_val;
	}

	/**
	 * Log an event to the syslog table in the database.
	 * If the event type is 'error' or 'fatal' an error notification will be
	 * sent out to the address defined as SYSTEM_ADMINISTRATOR_EMAIL. If the
	 * event type is 'fatal' an Bdlm_Exception will be thrown.
	 *
	 * @param string $type 'info', 'warning', 'error', 'fatal' or 'debug'
	 * @param string $message A description of the event or error
	 * @param string $module The system module where the event occoured, generally auto-detected.
	 * @return bool
	 * @throws Bdlm_Exception
	 * @version 0.07
	 */
	public static function logEvent($type, $message, $module = null) {

		switch ($type) {
			case 'info':
			case 'warning':
			case 'error':
			case 'fatal':
			case 'debug':
			break;

			default:
				throw new Bdlm_Exception("Invalid event type '$type'");
			break;
		}

		if (is_null($module)) {
			$module = APP_MODULE_NAME;
		}

		$_SERVER['SCRIPT_FILENAME'] = (isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '');
		$_SERVER['HTTP_REFERER']    = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		$_SERVER['HTTP_USER_AGENT'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');

		$syslog = new Bdlm_Db_Row(new Bdlm_Db_Table('syslog', $GLOBALS['db']));
		$syslog['type'] = $type;
		$syslog['module'] = (string) $module;
		$syslog['url'] = (IS_HTTP_REQUEST ? $_SERVER['REQUEST_URI'] : '');
		$syslog['ip'] = (IS_HTTP_REQUEST ? get_ip() : '0.0.0.0');
		$syslog['message'] = (string) $message;
		$ret_val = $syslog->save(); // $ret_val will be set based on success of sending a notification email for error and fatal events.

		switch ($type) {
			case 'error':
			case 'fatal':

				// Wipe out the current buffer
				ob_end_clean();

				// Get a debugging backtrace
				ob_start();
				debug_print_backtrace();
				$backtrace = strip_tags(ob_get_clean());

				// Get printouts of the various data arrays
				$userdata = print_r(($GLOBALS['user'] instanceof Bdlm_User ? $GLOBALS['user']->getData() : []), true);
				$getdata = print_r($_GET, true);
				$postdata = print_r($_POST, true);
				$cookiedata = print_r($_COOKIE, true);
				$filedata = print_r($_FILES, true);

				// Send an error notification
				$mailer = new Bdlm_Mailer();
				$mailer->AddAddress(SYSTEM_ADMINISTRATOR_EMAIL);
				$mailer->Subject = HOSTNAME.": An error has occoured on a ".(IS_PRODUCTION ? 'production' : 'development')." server!";
				$mailer->ContentType = 'text/plain';
				$mailer->Body = "
An error has occoured on a ".(IS_PRODUCTION ? '**PRODUCTION**' : 'development')." server!

Server: ".HOSTNAME."
Script: {$_SERVER['SCRIPT_FILENAME']}
URL: {$syslog['url']}
Referrer: {$_SERVER['HTTP_REFERER']}
Remote IP: {$syslog['ip']}
Browser: {$_SERVER['HTTP_USER_AGENT']}

Error reported by script:
	Message:    {$syslog['message']}
	Date/time:  {$syslog['created_on']}
	Error type: {$syslog['type']}
	Module:     {$syslog['module']}

Backtrace:
$backtrace

User Data:
$userdata

\$_GET:
$getdata

\$_POST
$postdata

\$_COOKIE
$cookiedata

\$_FILES
$filedata
";
				$mailer->enable_mail = true;
				$ret_val = $mailer->Send();
				if ('fatal' === $type) {
					throw new Bdlm_Exception("A $type error has occoured in the '$module' module: $message");
				}
			break;
		}
		return $ret_val;
	}

	/**
	 * Translate a number of bytes into a kb, mb, gb, tb or pb string ('52.08 Gb')
	 *
	 * @param int $num_bytes The number of bytes
	 * @param int $precision The number of decimal places to show
	 * @return string A formatted string
	 * @version 0.09
	 */
	public static function parseByteSize($num_bytes, $precision = 2) {

		// Enforce integer types
		$num_bytes = (float) $num_bytes;
		$precision = (int) $precision;

		// Define each size
		$kb = 1024;
		$mb = 1024 * $kb;
		$gb = 1024 * $mb;
		$tb = 1024 * $gb;
		$pb = 1024 * $tb;

		// Parse the string based on the number of bytes
		if (0 === $num_bytes) {
			$parsed_size = "0 B";
		} elseif ($num_bytes < $kb) {
			$parsed_size =  number_format($num_bytes, $precision)." B";
		} elseif ($num_bytes < $mb) {
			$parsed_size = number_format($num_bytes / $kb, $precision).' Kb';
		} elseif ($num_bytes < $gb) {
			$parsed_size = number_format($num_bytes / $mb, $precision).' Mb';
		} elseif ($num_bytes < $tb) {
			$parsed_size = number_format($num_bytes / $gb, $precision).' Gb';
		} elseif ($num_bytes < $pb) {
			$parsed_size = number_format($num_bytes / $tb, $precision).' Gb';
		} else {
			$parsed_size = number_format($num_bytes / $pb, $precision).' Pb';
		}
		return $parsed_size;
	}

	/**
	 * print_r wrapped in <pre></pre> tags
	 * @param mixed $val
	 * @param bool $return
	 * @return mixed Matches print_r return values
	 * @version 0.03
	 */
	public static function prePrintR($val, $return = false) {
		$ret_val = '<pre>'.print_r($val, true).'</pre>';
		if ($return === false) {
			echo $ret_val;
			return true;
		} else {
			return $ret_val;
		}
	}

	/**
	 * Redirect browsers using various methods
	 *
	 * The redirection method ($type) may be one of:<br />
	 * 'html'/void - The default action is to use HTML META refresh redirection<br />
	 * 'header' - header() redirection<br />
	 * 'javascript' - document.location redirection<br />
	 *
	 * @param string $url Target URL
	 * @param string $type Redirection method
	 * If "html" then a META refresh is used
	 * If "javascript" then the document.location value is updated
	 * If "post" then post data is included using a form.
	 * @param string $status The HTTP/1.1 redirection status code to use
	 * @return void
	 * @version 0.29
	 */
	public static function redirect($url = '', $type = null, $status = '') {

		// Close open sessions
		if (session_id() != '') {
			session_write_close();
		}

		// Check redirect URI, default to current location
		$url = ($url == '') ? 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : $url;

		switch (strtolower($type)) {

			// HTML redirection
			case 'html':
				echo '<meta http-equiv="Refresh" content="0; url='.$url.'">';
			break;

			// JavaScript redirection
			case 'javascript':
				echo '<script type="text/javascript">document.location.href="'.$url.'";</script>\n';
			break;

			// Include POST data,
			case 'post':
				$form_id = uniqid('redirect_');
				$form = '<form id="'.$form_id.'" action="'.$url.'" method="post">';
				while (list($k, $v) = each($_POST)) {
					$form .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
				}
				$form .= '</form><script type="text/javascript">document.'.$form_id.'.submit();</script>';

				echo $form;
			break;

			// Header redirection (default)
			case 'header': // break; omitted
			default:

				// Redirection status headers
				switch ($status) {
					case '301':
						header('HTTP/1.1 301 Moved Permanently');
					break;

					case '302':
						header('HTTP/1.1 302 Found');
					break;

					case '303':
						header('HTTP/1.1 303 See Other');
					break;

					case '304':
						header('HTTP/1.1 304 Not Modified');
					break;

					// Most browsers don't play nicely with this one
					//case '305':
					//	header('HTTP/1.1 305 Use Proxy');
					//break;

					// No longer used
					//case '306':
					//	header('HTTP/1.1 306 Switch Proxy');
					//break;

					case '307':
						header('HTTP/1.1 307 Temporary Redirect');
					break;
				}
				header("Location: ".$url);
			break;
		}

		exit;
	}

	/**
	 * Format a string to use sentence case.  There are several rules:
	 * 	<ul>
	 * 		<li>The first letter in the word or sentence is capitalied.</li>
	 * 		<li>The first letter following a period, followed by at least one whitespace character, followed by no more than one single (') or double (") quote is capitalized.</li>
	 * 		<li>If a period is followed by a quote (double or single) and then followed by a whitespace character, the following letter is NOT capitalized.</li>
	 * 		<li>Language specific constructs (I.E. capitalizing English pronouns) are beyond the scope of this function.</li>
	 * 	</ul>
	 *
	 * This is pretty basic but it works well for most cases.
	 *
	 * @param string $sentence The word or sentences to be modified
	 * @return string The modified word or sentence
	 *
	 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
	 * @author Michael Kenney <mkenney@webbedlam.com>
	 * @package Bedlam_CORE
	 * @subpackage Functions
	 * @version 0.4
	 */
	public static function sentenceCase($sentence = '') {

		//
		// Make sure it's a string
		//
		$sentence = (string) $sentence;

		//
		// If it's empty, don't bother
		//
		if ('' !== trim($sentence)) {

			//
			// This is for the first letter
			//
			$sentence = preg_replace(
				// Any ammount of whitespace followed by a lowercase letter at
				// the beginning of the string
				'/^([\W]*[a-z]{1})/e'
				, "strtoupper('\\1')"
				, $sentence
			);

			//
			// This is for every other letter in the string that follows a period
			//
			$sentence = preg_replace(
				// A period followed by at least one whitespace character followed
				// by a lower-case letter anywhere in the string
				'/([\.]{1}[\W]{1}[\\\'\\"]{0,1}[a-z]{1})/e'
				, "strtoupper('\\1')"
				, $sentence
			);
		}
		return $sentence;
	}

	/**
	 * Run a whois query against whois.geektools.com
	 *
	 * @param string $domain
	 * @return string The resulting output
	 *
	 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
	 * @author Michael Kenney <mkenney@webbedlam.com>
	 * @package Bedlam_CORE
	 * @subpackage Functions
	 * @version 0.17
	 */
	public static function whois($domain) {
		$whois_server = "whois.geektools.com";
		$output = '';

		$connection = fsockopen($whois_server, 43);
		if (!$connection) {
			$output = 'Sorry, we could not connect to the server. Please try again later.';
		} else {
			fwrite($connection, "$domain\n");
			while (!feof($connection)) {
				$output .= fgets($connection);
			}
			fclose($connection);
		}

		return trim($output);
	}
}

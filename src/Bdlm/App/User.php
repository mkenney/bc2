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
 * Represents a system user
 * Contains all methods required for authentication and related functions
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.27
 */
class User extends Core\Controller {

    use Core\Util\Encryption\Mixin\Boilerplate;

    use Core\Http\Cookie\Mixin\Boilerplate;

    protected $_user_id = null;
    protected $_session_id = null;
    protected $_cookie_user_id = null;

    /**
     * Authenticated flag, only valid for the current web user.
     * @var bool $_is_authenticated
     */
    protected $_is_authenticated = false;

    public function __construct(
        Core\Model\Iface\Base $model
        , Core\View\Iface\Base $view
        , Core\Util\Config\Iface\Base $config
    ) {
        parent::__construct($model, $view, $config);

        $this->setName('user');
        $this->setCookie(new Core\Http\Cookie($this->getName()));
        $this->setEncryption(new Core\Util\Encryption());
    }

//  /**
//   * Define the tables in this object and load if an Id is passed.
//   * @param int $id
//   * @return Bdlm_App_User
//   */
//  public function __construct($id = 0, $authenticate = true) {
//      $this->setModel(new \Bdlm\App\User\Model($this));
//      $this->setView(new \Bdlm\App\User\View($this));
//
//      //$this->getModel()->setFetchMode(Model\ModelAbstract::FETCH_BY_ID);
//      //$this->getModel()->setFetchValue($id);
//      //$this->getModel()->setFetchColumn('id');
//
//      if (true === $authenticate) {
//          $this->authenticate();
//      }
//  }

    /**
     * Authenticate a web user
     * @return bool
     */
    final public function authenticateHttpSession() {
        $this->_is_authenticated = false;

        $user_id = 0;
        $session_id = '';
        $cookie_user_id = $this->decryptCookieData($this->getCookie()->get('user_id'));
//*
// Debug
echo "user_id value in cookie: ".$this->getCookie()->get('user_id');
Core\Util::prePrintR($this->getCookie());
var_dump($cookie_user_id > 0);
var_dump($this->getCookie()->getData());
var_dump($this->getCookie()->has('pwd'));
var_dump(!$this->getCookie()->isEmpty('pwd'));
var_dump($this->getCookie()->has('session_id'));
var_dump(!$this->getCookie()->isEmpty('session_id'));
//*/

        //
        // Check for minimum required information
        //
        if (
            $cookie_user_id > 0
            && $this->getCookie()->has('pwd')
            && !$this->getCookie()->isEmpty('pwd')
            && $this->getCookie()->has('session_id')
            && !$this->getCookie()->isEmpty('session_id')
        ) {

            //
            // Select a matching user and session Id
            //
            $sql = new Bdlm_Db_Statement("
                SELECT user.id, user.session_id, user.last_activity
                FROM user
                WHERE
                    user.id = :id
                    AND user.password = ':password'
            ");
            $sql->data(array(
                'id' => $cookie_user_id,
                'password' => $this->decryptCookieData($this->getCookie()->get('pwd')),
            ));

            list($user_id, $session_id, $last_activity) = $this->db()->query($sql)->nextRow();

            $user_id = (int) $user_id;
            if ($user_id < 1 || $user_id != $cookie_user_id) {
                event('info', 'User authentication failed: User Id not found or does not match authentication information.');
                $user_id = 0;
            }

            //
            // Just in case we run ->authenticate() on a user object that was loaded manually.  If the Id's match it
            // doesn't matter anyway do don't bother breaking.
            //
            if ($this->id() > 0 && $user_id != $this->id()) {
                event('error', 'User authentication failed: The authenticated user Id does not match the user Id loaded into this object.');
                $user_id = 0;
            }

            //
            // If the session Id in the cookie doesn't match the session Id in the database then there is probably a
            // browser collision.
            //
            if ($session_id !== strip_tags($this->getCookie()->get('session_id'))) {
//              event('warning', "App_User::authenticate() - Possible browser collision or session expired.  Refreshing session for user #".(int) mysql_escape_string(strip_tags($this->decryptCookieData($this->getCookie()->get('user_id')))));
                $this->getCookie()->delete('session_id');
                $user_id = 0;
            }

            if (strtotime($last_activity) < strtotime('now - '.USER_INACTIVITY_TIMEOUT)) {
                event('info', 'User authentication failed: User Id not found or does not match authentication information.');
                $user_id = 0;
            }
        }

        //
        // No matching user Id found, authentication failed
        //
        if (!$user_id) {
            $this->isAuthenticated(false);

        //
        // Authenticated using cookies, set the user Id to match
        //
        } else {
            $this->id($user_id);
            $this->refreshAuthenticationCookies();
            $this->isAuthenticated(true);
            $this->load();
            $this->set('last_activity', 'NOW()');
            $this->save(false, false);
        }

        return $this->isAuthenticated();
    }

    /**
     * Check to make sure the user has all the right authentication cookies
     */
    final public function checkLoginCookies() {

        $ret_val = true;

        //
        // Validate user cookies.
        //
        if ($this->get('password') !== $this->decryptCookieData($this->getCookie()->get('pwd'))) {
            $ret_val = false;
        }

        //
        // Validate all the session cookies
        //
        if (
            $this->get('password') !== $this->decryptCookieData($this->getCookie()->get('pwd'))
            || (int) $this->id() !== (int) $this->decryptCookieData($this->getCookie()->get('user_id'))
            || $this->get('session_id') != $this->getCookie()->get('session_id')
        ) {
            $ret_val = false;
        }

        //
        // Login collision check
        //
        if ($this->get('session_id') != $this->getCookie()->get('session_id')) {
            $this->loginCollision();
        }

        return $ret_val;
    }

    /**
     *
     * Check the user's plain-text password against the encrypted database version.
     * Override to use alternate authentication mechanisms like LDAP.
     *
     * @param void
     * @return bool True if the password is valid for this user, else false.
     */
    final public function checkPassword($password) {

        if ($this->id() < 1) {
            event('error', 'A user must be loaded before a password can be validated.');
        }

        $ret_val = false;
        if ($this->hashPassword($password) === $this->get('password')) {
            $ret_val = true;
        }
        return $ret_val;
    }

    /**
     * Create a new user record
     * @param string $login The user's login in the form of an email address.  Must be a valid address for email confirmation.
     * @param string $fname Optional, the user's first name
     * @param string $lname Optional, the user's last name
     * @return Bdlm_App_User The new user object
     */
    static public function createUser($login, $fname = null, $lname = null, $send_notification = true) {
        if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {throw new Bdlm_Exception("'login' must be in valid email address format, '$login' given");}

        $user = new App\User(new App\User\Model(), new App\User\View());
        $user->getModel()->set('login', $login);
        $user->getModel()->set('email',  $login);
        $user->getModel()->set('created_on',  'NOW()');
        $user->getModel()->set('created_by',  $GLOBALS['user']->id());
        $user->getModel()->set('status',  'inactive');
        if (!is_null($fname)) {$user->getModel()->set('fname', (string) $fname);}
        if (!is_null($lname)) {$user->getModel()->set('lname', (string) $lname);}

        $user->save(true); // Save here because salt() and managementKey() use the user id
        $user->salt();
        $user->managementKey();
        $user->save(false, false);

        if (true === $send_notification) {
            $mailer = new Bdlm_Mailer();
            $mailer->AddAddress($user['email']);
            $mailer->Subject = "Your ".APP_NAME." account has been created";
            $mailer->Body = "
Hi {$user['fname']},

Your new ".APP_NAME." account has been created but activation is required.  Click <a href=\"".HTTP_PROTOCOL.HOSTNAME."/register/activate/?key={$user['management_key']}\">here</a> to activate your account and set a password.";

            $mailer->enable_mail = true;

            if (!$mailer->Send()) {
                logevent('error', "Could not send new user confirmation email for user {$user['email']}");
            }
        }
        return $user;
    }

    /**
     * Get a crypt
     * @return resource Mcrypt resource
     * @todo deprecated?  it wasn't working for some reason but it might have been unrelated.  retest or remove.
     */
    final public function crypt() {
        if (!is_resource($this->_crypt)) {
            $this->_crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '');
        }
        return $this->_crypt;
    }

    /**
     * Decrypt the information provided.
     * @param string $data
     * @return string
     */
    final public function decryptCookieData($data = '') {

        $decrypted = '';

        //
        // Check to see if the user has the iv in the cookie and that they are providing
        // data to be decrypted.
        //
        if (32 === strlen($this->getCookie()->get('iv')) && '' !== $data && !is_null($data)) {


            $iv = $this->getCookie()->get('iv');
            $crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '');
            mcrypt_generic_init($crypt, USER_MCRYPT_SALT, $iv);

            $decrypted = mdecrypt_generic($crypt, utf8_decode($data));

            mcrypt_generic_deinit($crypt);
            mcrypt_module_close($crypt);
        }
//      if ($data != $this->encryptCookieData($decrypted)) {
//          debug("$data != ".$this->encryptCookieData($decrypted)." ($decrypted)");
//      }
        return $decrypted;
    }

    /**
     * Encrypt information for storage in the cookies
     * @param string $data
     * @return string
     */
    final public function encryptCookieData($data = '') {

        $encrypted = '';

        //
        // If there is a user id then check to see if the user has the second session cookie
        // that stores the iv for mcrypt
        //
        if ($this->id() > 0 && $data != '') {


            $crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '');

            //
            // Check the cookie for the iv value.  If it doesn't exist then check _iv.
            //
            if (!$this->getCookie()->has('iv') || 32 !== strlen($this->getCookie()->get('iv'))) {
                $this->getCookie()->set('iv', $this->iv(), COOKIE_TIMEOUT_LONGTERM);
            }
            $iv = $this->getCookie()->get('iv') ? $this->getCookie()->get('iv') : $this->iv();
            if (!$iv) {
                $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($crypt), MCRYPT_RAND);
                $this->getCookie()->set('iv', $iv, COOKIE_TIMEOUT_LONGTERM);
            }
            $this->iv($iv);

            mcrypt_generic_init($crypt, USER_MCRYPT_SALT, $this->iv());
            $encrypted = utf8_encode(mcrypt_generic($crypt, $data));
            mcrypt_generic_deinit($crypt);
            mcrypt_module_close($crypt);
        }
//      if ($data != $this->decryptCookieData($encrypted)) {
//          debug("$data != ".$this->decryptCookieData($encrypted)." ($encrypted)");
//      }

        return $encrypted;
    }

    /**
     * Hash the users password.
     * Warning - Changing the way this method works in a child class will invalidate every password
     * currently in the database.  Override with care.
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function hashPassword($password = '', $salt = null) {
        if (is_null($salt)) {
            $salt = $this->get('salt');
        }
        return md5($salt.$password);
    }

    /**
     * Get/set the authentication flag
     * @param bool $is_authenticated
     * @return bool
     */
    final public function isAuthenticated($is_authenticated = null) {
        if (!is_null($is_authenticated)) {
            $this->_is_authenticated = (bool) $is_authenticated;
        }
        return $this->_is_authenticated;
    }

    /**
     * Used to generate this user's encryption initializaion vector
     * @return string
     */
    final public function iv($iv = null) {
        if (!is_null($iv)) {
            $this->_iv = $iv;
        }
        if (32 !== strlen($this->_iv) && $this->getCookie()->has('iv')) {
            $this->_iv = $this->getCookie()->get('iv');
        }
        if (32 !== strlen($this->_iv)) {
            $crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '');
            $this->_iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($crypt), MCRYPT_RAND);
        }
        return $this->_iv;
    }

    /**
     * Generate a random session id for the session cookie
     * A new one is generated on every login.
     * @return string
     */
    public function generateSessionId() {
        return md5(uniqid('', true).mt_rand());
    }

    /**
     * Log in a browser session using a login/password combination
     * This also modifies the session_id field in the database.
     *
     * @param string $login The login name for this user
     * @param string $password The plain-text password for this user
     * @return bool True on success, false on failure
     */
    final public function login($login, $password) {

        $is_authenticated = false;

        //
        // Find a matching user Id
        //
        $sql = new Bdlm_Db_Statement("
            SELECT id
            FROM `user`
            WHERE login = ':login'
        ");
        $sql->data(array(
            'login' => $login,
        ));
        $id = (int) $this->db()->query($sql)->toWord();

        //
        // If an Id was found, load the data for that user and set their login cookies
        //
        if ($id > 0) {
            $this->id($id);
            $this->load();

            if ('active' !== $this->get('status')) {
                $this->errorMessages("Login not valid for user ".htmlentities($login).": ue02"); // user account isn't acive
            } elseif (!$this->checkPassword($password)) {
                $this->errorMessages("Login not valid for user ".htmlentities($login).": ue03"); // password didn't validate
            } else {
                $this->setLoginCookies();
            }

            if (!$this->checkLoginCookies()) {
                $this->errorMessages("Login not valid for user ".htmlentities($login).": ue04"); // cookies didn't get set properly
            } else {
                $is_authenticated = true;
                $this->set('last_activity', 'NOW()');
                $this->set('login_count', ((int) $this->get('login_count') + 1));
                $this->save();
            }
        } else {
            $this->errorMessages("Login not valid for user ".htmlentities($login).": ue01"); // user not found
        }

        return $this->isAuthenticated((bool) $is_authenticated);
    }

    /**
     * Log out a browser session by destroying the key cookies
     * This also modifies the last_logout field, you can override this behavior.
     * @param bool $update_last_logout
     * @return void Just assume it worked
     */
    final public function logout($update_last_logout = true) {
        if ($this->isAuthenticated()) {
            if ($this->getCookie()->has('pwd')) {
                $this->getCookie()->delete('pwd');
            }
            if ($this->getCookie()->has('session_id')) {
                $this->getCookie()->delete('session_id');
            }
            if (true === $update_last_logout) {
                $this->set('last_logout', 'NOW()');
            }
            $this->save();
            $this->isAuthenticated(false);
        }
    }

    /**
     * This will repair a users session by forcing the reauthentication of the users login credentials
     * This method should be called in case of a session_id mis-match.  This
     * dis-allows simultaneous sessions in different browsers.  Sorry ;)
     *
     * @param bool $all_cookies Delete all cookies
     * @return void
     */
    final public function loginCollision($all_cookies = false) {

        // Set forced refresh cookie, 30 minute timeout
        $this->getCookie()->set('login_collision', 'true', 1800);

        // Logout
        $this->logout(false);

        // Delete all remaining user cookies.  This will cause the
        // loss of ALL cookies (id, iv, etc.)
        if ($all_cookies) {
            $this->getCookie()->reset();
        }

        // Log forced session refresh
        $arg = ($all_cookies ? 'true' : 'false');
        logevent('warning', "Bdlm_App_User::loginCollision($arg) - Session reloaded for user {$this['login']} (#{$this['id']})");

        // Redirect to the login page
        if ('login' !== APP_MODULE_NAME) {
            redirect('/login/');
        }
    }

    /**
     * Get/generate+set the 128 character salt for the password or the token for retrieving a password.
     * @return string
     */
    final public function managementKey() {
        if (128 !== strlen($this->get('management_key'))) {
            $this->set('management_key', md5($this->get('login').$this->id()).md5(microtime(true)).md5(uniqid(mt_rand(), true)).md5(uniqid(mt_rand(), true)));
        }
        return $this->get('management_key');
    }

    /**
     * Refresh the expiration of the authentication cookies every time there is activity
     * @return void
     */
    public function refreshAuthenticationCookies() {
        $this->getCookie()->set('user_id', $this->getCookie()->get('user_id'), COOKIE_TIMEOUT_LONGTERM);
        $this->getCookie()->set('iv', $this->getCookie()->get('iv'), COOKIE_TIMEOUT_LONGTERM); // Long term so we can decrypt the user_id after an extended absense.  Regenerated on each login so it's ok.
        $this->getCookie()->set('session_id', $this->getCookie()->get('session_id'), USER_INACTIVITY_TIMEOUT);
        $this->getCookie()->set('pwd', $this->getCookie()->get('pwd'), USER_INACTIVITY_TIMEOUT);
    }

    /**
     * Reset a user's password and notify them
     * @return bool
     */
    public function resetPassword() {
        if ('' === trim($this['login'])) {
            throw new Bdlm_Exception("Could not reset the user password, login is empty: {$this['id']}");
        }
        if (!is_valid_email($this['email'])) {
            throw new Bdlm_Exception("Could not reset the user password, email is not a valid email address: {$this['id']}|{$this['email']}");
        }
        $this->managementKey();
        $this->save(false, false);

        $mailer = new Bdlm_Mailer();
        $mailer->AddAddress($this['email'], "{$this['fname']} {$this['lname']}");
        $mailer->Body = "
{$this['fname']} {$this['lname']}

    You or someone has requested a password reset on your account.  Click <a href=\"".HTTP_PROTOCOL.HOSTNAME."/register/?key={$this['management_key']}\">here</a> to set
a new password.  If you did not request a password reset or have remembered your password, please delete this email.
        ";
        $mailer->Send();
    }

    /**
     * Get/generate+set the 128 character salt for the password or the token for retrieving a password.
     * @return string
     */
    final public function salt($extra_salt = null) {
        if (128 !== strlen($this->get('salt'))) {
            $this->set('salt', md5($extra_salt.$this->id()).md5(microtime(true)).md5(uniqid(mt_rand(), true)).md5(uniqid(mt_rand(), true)));
        }
        return $this->get('salt');
    }

    /**
     * Set the authentication cookies.
     * This also updates the session_id field on the user table and saves the user object.
     *
     * @return void
     */
    public function setLoginCookies() {

        //
        // User does not have an id or is not active then log the user out.
        //
        if (!$this->id() || $this->get('status') != 'active') {
            event('error', "This user cannot be logged in.  Id: {$this->id()}, Login: {$this->getLogin()}, Status: {$this->getStatus()}");
        }

        $this->set('session_id', $this->generateSessionId());
        $this->save();

        $this->getCookie()->set('user_id', $this->encryptCookieData($this->id()), COOKIE_TIMEOUT_LONGTERM);
        $this->getCookie()->set('session_id', $this->get('session_id'), USER_INACTIVITY_TIMEOUT);

        //
        // Check the cookie for the iv value.  If it doesn't exist then check _iv.
        //
        $_iv = ($this->getCookie()->get('iv') ? $this->getCookie()->get('iv') : $this->iv());

        if (!$_iv) {
            $this->iv(mcrypt_create_iv(mcrypt_enc_get_iv_size($this->crypt()), MCRYPT_RAND));
            $this->getCookie()->set('iv', $this->iv(), COOKIE_TIMEOUT_LONGTERM);
        }

        $this->getCookie()->set('pwd', $this->encryptCookieData($this->get('password')), USER_INACTIVITY_TIMEOUT);
    }

    /**
     * Set a user's password.  Just a convenience function.
     * @param string $password
     * @return bool
     */
    public function setPassword($password) {
        if (!User::validatePassword($password)) {
            throw new Bdlm_Exception("The given password is invalid");
        }
        $this['password'] = $this->hashPassword($password);
    }

    /**
     * Check a password to make sure it meets the minimum criteria.
     *  1. Passwords must be at least USER_PASSWORD_MINIMUM_LENGTH characters
     *  2. Passwords must consist of at least one letter, one number and 1 "special" character
     *      - Special characters: * ~ ! # $ % ^ & * ( ) _ - + = ? . , < > / \ ; : [ ] { } | or ' ' (space)
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password) {
        $ret_val = false;

        //
        // ~ ! # $ % ^ & * ( ) _ - + = ? . , < > / \ ; : [ ] { } | and whitespace
        //
        if (
            strlen($password) >= USER_PASSWORD_MINIMUM_LENGTH
            && preg_match('/[a-zA-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[~!@#\\$%^&\\*()_\\-\\+=\\?\\.,<>\\/\\\\;:\\[\\]{}\\|\\s]/', $password)
        ) {
            $ret_val = true;
        }
        return $ret_val;
    }

    /**
     * Bdlm_Db_Row::save() wrapper
     * If a user saves their own record, automatically update the last_activity field.
     * @param bool $as_new Save as a new database record
     * @param bool $update_lastmod 'modified_on' field update override flag
     * @return bool
     */
    public function save($as_new = false, $update_lastmod = true) {
        $as_new = (bool) $as_new;
        if (true === $as_new || 0 === $this->id()) {
            $this->set('created_on', 'NOW()');
            $this->set('created_by', ($GLOBALS['user'] instanceof Bdlm_App_User ? $GLOBALS['user']->id() : 0));
        }
        // If I'm saving my own record, update my last_activity field
        if (
            $this->id() > 0
            && isset($GLOBALS['user'])
            && $GLOBALS['user'] instanceof Bdlm_App_User
            && $this->id() === $GLOBALS['user']->id()
        ) {
            $this->set('last_activity', 'NOW()');
        }
        return parent::save($as_new, $update_lastmod);
    }

}
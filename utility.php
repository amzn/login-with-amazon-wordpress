<?php
/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
/**
 * Utilities for Login With Amazon.
 */
class LoginWithAmazonUtility {
    public static $CSRF_AUTHENTICATOR_KEY = '_amazon_login_csrf_authenticator';
    public static $I18N_DOMAIN = 'loginwithamazon';

    private static $META_KEY_AMAZON = '_login_with_amazon';
    private static $META_KEY_BOTH = '_login_with_amazon_and_native';
    private static $login_error_msg = "";
    private static $login_error_add = "";


    /**
     * @return bool
     */
    public static function shouldProcessAmazonLogin() {
        $get = isset($_GET['amazonLogin']) && $_GET['amazonLogin'] === '1';
        $post = isset($_POST['amazonLogin']) && $_POST['amazonLogin'] === '1';

        return $get || $post;
    }

    /**
     * @return bool
     */
    public static function shouldReregister() {
        $get = isset($_GET['loginwithamazon_reregister']) && $_GET['loginwithamazon_reregister'] === '1';
        $post = isset($_POST['loginwithamazon_reregister']) && $_POST['loginwithamazon_reregister'] === '1';

        return $get || $post;
    }

    /**
     * @return null|WP_Post
     */
    public static function getAcessToken() {
        $get = (isset($_GET['access_token'])) ? $_GET['access_token'] : null;
        $post = (isset($_POST['access_token'])) ? $_POST['access_token'] : null;

        return ($get) ?: $post;
    }

    /**
     * @return null|WP_Post
     */
    public static function getCsrfToken() {
        $get = (isset($_GET['state'])) ? $_GET['state'] : null;
        $post = (isset($_POST['state'])) ? $_POST['state'] : null;

        return ($get) ?: $post;
    }


    /**
     * Generates an HMAC key for the CSRF tokens
     *
     * @param string $authenticator The authenticator retrieved from the current session
     * @return string
     */
    public static function hmac($authenticator) {
        return hash_hmac('sha256', $authenticator, wp_salt('NONCE_SALT'));
    }


    /**
     * Checks an existing HMAC token against the authenticator in the current session
     *
     * @param string $token The token that needs validated
     * @return bool
     */
    public static function verifyCsrfToken($token) {
        $true_token = self::hmac( self::getAuthenticatorKey() );
        return strcmp($token, $true_token) === 0;
    }

    public static function getAuthenticatorKey() {
        static $auth;

        if ( !isset( $auth ) ) {
            if ( isset( $_COOKIE[LoginWithAmazonUtility::$CSRF_AUTHENTICATOR_KEY] ) ) {
                $auth = $_COOKIE[LoginWithAmazonUtility::$CSRF_AUTHENTICATOR_KEY];
            } else {
                $auth = self::setAuthenticatorKey();
            }
        }

        return $auth;
    }

    public static function setAuthenticatorKey() {
        $auth = wp_generate_password(64);
        setcookie( LoginWithAmazonUtility::$CSRF_AUTHENTICATOR_KEY, $auth, 0, COOKIEPATH, COOKIE_DOMAIN, true );
        return $auth;
    }

    public static function createCsrfToken() {
	return self::hmac( self::getAuthenticatorKey() );
    }

    /**
     * Get user email address from Amazon access token.
     *
     * @param $accessToken string Access Token from Amazon Login widget
     * @return string|bool Email Address or False
     */
    public static function getEmailFromAccessToken($accessToken) {
        $result = self::json_curl_wrapper('https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($accessToken));
        if (!isset($result['aud']) || $result['aud'] != get_option('loginwithamazon_client_id')) {
            return false;
        }

        $result = self::json_curl_wrapper('https://api.amazon.com/user/profile', array('Authorization: bearer ' . $accessToken));
        if (isset($result['email'])) {
            return $result['email'];
        }

        return false;
    }


    /**
     * Wrapper for JSON CURL requests.
     *
     * @param $url string URL to request.
     * @param $headerArray array CURL request header contents.
     *
     * @return array JSON Decoded Results
     */
    public static function json_curl_wrapper($url, $headerArray = array()) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }


    /**
     * Determine if user can login with Amazon
     *
     * @param WP_User $user WordPress user object
     * @return boolean
     */
    public static function isAmazonUser($user) {
        if (!$user) {
            return false;
        }

        $amazon_user_meta = get_user_meta($user->ID, self::$META_KEY_AMAZON, true);

        return filter_var($amazon_user_meta, FILTER_VALIDATE_BOOLEAN);
    }


    /**
     * Determine if user can login with either Amazon or natively
     *
     * @param WP_User $user WordPress user object
     * @return boolean
     */
    public static function isAmazonAndNativeUser($user) {
        if (!$user) {
            return false;
        }

        $amazon_and_native_user_meta = get_user_meta($user->ID, self::$META_KEY_BOTH, true);

        return filter_var($amazon_and_native_user_meta, FILTER_VALIDATE_BOOLEAN);
    }


    /**
     * Determine if user can ONLY login with Amazon
     *
     * @param WP_User $user WordPress user object
     * @return boolean
     */
    public static function isAmazonOnlyUser($user) {
        return self::isAmazonUser($user) && !self::isAmazonAndNativeUser($user);
    }


    /**
     * Login by email address.
     *
     * @param WP_User $user A WordPress user object
     * @return void
     */
    public static function createSessionFromUser($user) {
        wp_set_auth_cookie( $user->ID, false, true );
        wp_set_auth_cookie( $user->ID, false, false );
        wp_set_current_user( $user->ID, $user->user_login );
        do_action( 'wp_login', $user->user_login, $user );

        wp_redirect( admin_url() );
        exit;
    }


    /**
     * Find a WordPress user via their email address
     *
     * @param $email
     * @return false|WP_User
     */
    public static function findUserByEmail($email) {
        return get_user_by( 'email', $email );
    }


    /**
     * Login or create account by email address.
     *
     * @param string $email Email Address
     * @return WP_User|WP_Error
     */
    public static function findOrCreateUserByEmail($email) {
        $user = self::findUserByEmail($email);

        if ($user) {
            if (self::isAmazonUser($user)) {

                return $user;
            } else {
                // Add fields to the form and manipulate the UI
                add_action( 'login_form', array('LoginWithAmazonUtility', 'addReregisterFieldsToForm') );
                wp_enqueue_script('loginwithamazon_reregister', LOGINWITHAMAZON__PLUGIN_URL . 'reregister.js', array('jquery'));

                if (self::shouldReregister() ) {
                    $pwd = ( isset($_POST['pwd']) ) ? $_POST['pwd'] : null;
                    $auth_user = wp_authenticate($user->user_login, $pwd);

                    if ( is_wp_error($auth_user) ) {
                        // Could not authenticate with the given password
                        self::changeLoginError(null);
                        $msg = "Sorry, that password is not correct. Please re-enter your password to " .
                            "enable <em>Login with Amazon</em>";

                        return new WP_Error( 'login', __( $msg, self::$I18N_DOMAIN ) );
                    } else {
                        // Success. Set db reference fields and return the authorized user
                        add_user_meta($user->ID, self::$META_KEY_AMAZON, true);
                        add_user_meta($user->ID, self::$META_KEY_BOTH, true);

                        return $auth_user;
                    }
                } else {
                    // User is native, tell ask for password to connect their account to Amazon
                    $msg = "It looks you're already registered directly with this website. Enter your password below " .
                        "to enable <em>Login with Amazon.</em>";

                    return new WP_Error( 'login', __( $msg, self::$I18N_DOMAIN ) );
                }
            }
        }

        $password = wp_generate_password();
        $user_id = wp_create_user($email, $password, $email);
        $user = get_user_by( 'id', $user_id );

        // Set metadata to mark it as an Amazon account
        add_user_meta($user->ID, self::$META_KEY_AMAZON, true);

        // Generate a random, invalid password to prevent native logins
        self::setInvalidPasswordForUser($user);

        return $user;
    }


    /**
     * Change the login form error and call the filter so that it will display
     *
     * @param string $message
     */
    public static function changeLoginError($message) {
        self::$login_error_msg = $message;
        add_filter( 'login_errors', array('LoginWithAmazonUtility', 'changeErrorOnLoginForm'), 10, 0 );
    }

    /**
     * Used by the `login_errors` filter to change the error
     *
     * @return string
     */
    public static function changeErrorOnLoginForm() {
        return self::$login_error_msg;
    }


    /**
     * Set a login form message and call the filter so that it will display
     *
     * @param string $message
     */
    public static function addLoginError($message) {
        self::$login_error_add = $message;
        add_filter( 'login_message', array('LoginWithAmazonUtility', 'displayErrorOnLoginForm'), 10, 0 );
    }

    /**
     * Used by the `login_message` filter to display a message on the login form
     *
     * @return string
     */
    public static function displayErrorOnLoginForm() {
        $msg = self::$login_error_add;

        return "<p id=\"login_error\">$msg</p>";
    }


    /**
     * Used by the `registration_errors` filter. Adds an error when a user tries to
     * natively register an account that was already created with Amazon.
     *
     * @param $errors
     * @param $sanitized_user_login
     * @param $user_email
     * @return mixed
     */
    public static function registrationErrors($errors, $sanitized_user_login, $user_email) {
        $user = self::findUserByEmail($user_email);

        if ( self::isAmazonOnlyUser($user) ) {
            $url = "https://www.amazon.com/gp/css/account/forgot-password/email.html";
            $msg = "<br><h2>Login with Amazon</h2>" .
                "<br>It appears you've already registered with your Amazon account. " .
                "<a href=\"".wp_lostpassword_url()."\">Reset your WordPress pasword</a> to gain access." .
                "<br><br>You may also continue using the <em>Login with Amazon</em> button below to gain access. " .
                "If you are having trouble logging in with your Amazon account, you may " .
                "<a href=\"$url\" target=\"_blank\">recover your Amazon account here.</a>";

            $errors->add( "login", __($msg, self::$I18N_DOMAIN) );
        }

        return $errors;
    }

    /**
     * Set a custom password after registration.
     *
     * @param WP_User  $user  WordPress user object
     * @return WP_User
     */
    public static function setInvalidPasswordForUser($user) {
        if ( self::isAmazonOnlyUser($user) ) {
            global $wpdb;
            $pass = 'LOGINWITHAMAZON00000000000000000';

            $wpdb->update(
                'wp_users',
                array('user_pass' => $pass),
                array('ID' => $user->ID),
                array('%s'),
                array('%d')
            );

            clean_user_cache($user);

            return self::findUserByEmail($user->user_email);
        }

        return $user;
    }


    /**
     * Add hidden fields to login form for the re-register process
     *
     * @return void
     */
    public static function addReregisterFieldsToForm() {
        ?>
        <input type="hidden" name="amazonLogin" value="1">
        <input type="hidden" name="access_token" value="<?php echo self::getAcessToken(); ?>">
        <input type="hidden" name="state" value="<?php echo self::getCsrfToken(); ?>">
        <input type="hidden" name="loginwithamazon_reregister" value="1">
        <?php
    }


}

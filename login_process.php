<?php
/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
defined('ABSPATH') or die('Access denied');

add_action('login_init', 'amazon_login_process');

function amazon_login_process() {
    if ( LoginWithAmazonUtility::shouldProcessAmazonLogin() || LoginWithAmazonUtility::shouldReregister() ) {
        $access_token = LoginWithAmazonUtility::getAcessToken();
        if ( $access_token ) {
            // Ensure CSRF token present and valid
            $csrf_token = LoginWithAmazonUtility::getCsrfToken();
            if ( $csrf_token && LoginWithAmazonUtility::verifyCsrfToken($csrf_token) ) {

                $email = LoginWithAmazonUtility::getEmailFromAccessToken($access_token);

                if ($email) {
                    // Find or create an Amazon user
                    $user = LoginWithAmazonUtility::findOrCreateUserByEmail($email);

                    if (is_wp_error($user)) {
                        // Display error on the login form.
                        LoginWithAmazonUtility::addLoginError($user->get_error_message());
                    } else {
                        // Log in the Amazon user and redirect to the homepage.
                        LoginWithAmazonUtility::createSessionFromUser($user);
                    }

                } else {
                    // Could not retrieve email with the token. Provide a general login error to the user.
                    $error_msg = "There was an error when attempting to log you in.";
                    LoginWithAmazonUtility::addLoginError( __($error_msg, LoginWithAmazonUtility::$I18N_DOMAIN) );
                }

            }
        } else {
            add_action('login_enqueue_scripts', 'loginwithamazon_enqueue_nonsecure_script');
        }
    }
}

function loginwithamazon_enqueue_nonsecure_script() {
    wp_enqueue_script('loginwithamazon_nonsecure', LOGINWITHAMAZON__PLUGIN_URL . 'nonsecure.js');
}

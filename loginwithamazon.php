<?php
/**
 * Plugin Name: Login With Amazon
 * Plugin URI: https://github.com/amzn/login-with-amazon-wordpress
 * Description: Login With Amazon support for WordPress.
 * Version: 1.0.0
 * Author: Login with Amazon
 * Author URI: https://login.amazon.com/
 * License: Apache 2.0
 * Amazon Login - Amazon Login for WordPress
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
defined('ABSPATH') or die('Access denied');
define('LOGINWITHAMAZON__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('LOGINWITHAMAZON__PLUGIN_URL', plugin_dir_url( __FILE__ ));

add_option('loginwithamazon_client_id', '', '', 'yes');

require_once LOGINWITHAMAZON__PLUGIN_DIR . 'options.php';
require_once LOGINWITHAMAZON__PLUGIN_DIR . 'utility.php';
require_once LOGINWITHAMAZON__PLUGIN_DIR . 'login_process.php';

add_filter( 'registration_errors', array('LoginWithAmazonUtility', 'registrationErrors'), 1, 3 );

LoginWithAmazon::setup();

class LoginWithAmazon {
    static function setup() {
        add_action( 'login_init',                            array( __CLASS__, 'load_login_button' ) );
        add_action( 'wp_ajax_loginwithamazon_config',        array( __CLASS__, 'ajax_config' ) );
        add_action( 'wp_ajax_nopriv_loginwithamazon_config', array( __CLASS__, 'ajax_config' ) );
    }

    static function load_login_button() {
        if ( ! get_option('loginwithamazon_client_id') ) {
            return;
        }

        require_once LOGINWITHAMAZON__PLUGIN_DIR . 'login_button.php';

        // For future shortcode and skinned login page support
        add_action('wp_enqueue_scripts', 'loginwithamazon_enqueue_script');
        add_action('wp_footer', 'loginwithamazon_add_footer_script');

        // For login page
        add_action('login_enqueue_scripts', 'loginwithamazon_enqueue_script');
        add_action('login_footer', 'loginwithamazon_add_footer_script');
    }

    static function ajax_config() {
        $config = array(
            'ssl' => is_ssl(),
            'client_id' => get_option('loginwithamazon_client_id'),
            'logout' => !empty( $_GET['logout'] ),
            'options' => array(
                'scope' => 'profile',
                'state' => LoginWithAmazonUtility::createCsrfToken(),
                'popup' => is_ssl()
            ),
            'redirect' => site_url( 'wp-login.php?amazonLogin=1', 'https' )
        );

        if ( isset( $_GET['callback'] ) ) {
            // JSON-P response
            $callback = wp_unslash( $_GET['callback'] );
            $arg = wp_json_encode( $config );
            header( 'Content-type: application/javascript; charset=' . get_option( 'blog_charset' ) );
            echo "$callback( $arg );";
            exit();
        } else {
            // JSON response
            wp_send_json( $config );
        }
    }

}

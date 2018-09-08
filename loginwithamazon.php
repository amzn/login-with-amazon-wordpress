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

if ( is_admin() ) {
    require_once LOGINWITHAMAZON__PLUGIN_DIR . 'options.php';
}

require_once LOGINWITHAMAZON__PLUGIN_DIR . 'utility.php';
require_once LOGINWITHAMAZON__PLUGIN_DIR . 'login_button.php';
require_once LOGINWITHAMAZON__PLUGIN_DIR . 'login_process.php';

add_filter( 'registration_errors', array('LoginWithAmazonUtility', 'registrationErrors'), 1, 3 );

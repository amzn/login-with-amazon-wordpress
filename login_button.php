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


function loginwithamazon_enqueue_script() {
    $config = array(
        'ajaxurl' => admin_url( 'admin-ajax.php?action=loginwithamazon_config&callback=loginwithamazon_loader', 'https' )
    );
    wp_enqueue_script('loginwithamazon', LOGINWITHAMAZON__PLUGIN_URL . 'add_login.js', array(), '1.0', true );
    wp_localize_script( 'loginwithamazon', 'loginwithamazon', $config );
}

function loginwithamazon_add_footer_script() {
    echo '<div id="amazon-root"></div>';
}

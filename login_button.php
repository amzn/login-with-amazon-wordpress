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

if(get_option('loginwithamazon_client_id') && get_option('loginwithamazon_client_id') != '') {
	add_action( 'init', array( 'LoginWithAmazonUtility', 'sessionNonce' ) );
    add_action('wp_enqueue_scripts', 'loginwithamazon_enqueue_script');
    add_action('login_enqueue_scripts', 'loginwithamazon_enqueue_script');
    add_action('wp_footer', 'loginwithamazon_add_footer_script');
    add_action('login_footer', 'loginwithamazon_add_footer_script');
}

function loginwithamazon_enqueue_script() {
    wp_enqueue_script('loginwithamazon', LOGINWITHAMAZON__PLUGIN_URL . 'add_login.js');
}

function loginwithamazon_add_footer_script() {
    $popup = 'false';
    if(!empty($_SERVER['HTTPS'])) {
        $popup = 'true';
    }

    $csrf = LoginWithAmazonUtility::hmac( LoginWithAmazonUtility::getCsrfAuthenticator() );

    ?>
    <div id="amazon-root"></div>
    <script type="text/javascript">

        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId('<?php echo get_option('loginwithamazon_client_id'); ?>');
            amazon.Login.setUseCookie(true);
            <?php if(isset($_GET['loggedout']) && $_GET['loggedout'] == 'true'): ?>
            amazon.Login.logout();
            <?php endif; ?>
        };
        (function(d) {
            var a = d.createElement('script'); a.type = 'text/javascript';
            a.async = true; a.id = 'amazon-login-sdk';
            a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
            d.getElementById('amazon-root').appendChild(a);
        })(document);

        function activateLoginWithAmazonButtons(elementId) {
            document.getElementById(elementId).onclick = function() {
                var options = {
                    scope: 'profile',
                    state: '<?php echo $csrf; ?>',
                    popup: <?php echo $popup; ?>
                };
                amazon.Login.authorize(options, '<?php echo str_replace('http://', 'https://', site_url('wp-login.php')); ?>?amazonLogin=1');

                return false;
            };
        }
    </script>

<?php
}

<?php
/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
add_action('admin_menu', 'loginwithamazon_create_menu');
add_action('admin_init', 'loginwithamazon_register_settings');

function loginwithamazon_create_menu() {
    add_menu_page('Login With Amazon Settings', 'Login With Amazon Settings', 'administrator', __FILE__, 'loginwithamazon_settings_page');
}

function loginwithamazon_register_settings() {
    register_setting('loginwithamazon-settings-group', 'loginwithamazon_client_id');
}

function loginwithamazon_settings_page() {
    ?>
    <div class="wrap">
        <h2>Login With Amazon Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'loginwithamazon-settings-group' ); ?>
            <?php do_settings_sections( 'loginwithamazon-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Client ID</th>
                    <td><input type="text" name="loginwithamazon_client_id" value="<?php echo esc_attr(get_option('loginwithamazon_client_id')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php } ?>
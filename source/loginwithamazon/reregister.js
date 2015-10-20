/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
jQuery(function() {
    // Remove any fields not related to the reregister process
    jQuery('label[for="user_login"]').parent().remove();
    jQuery('label[for="rememberme"]').remove();

    // Update action button text to increase clarity
    jQuery('#wp-submit').attr('value', 'Enable Amazon Login');

    // Hide the Amazon login button to avoid confusion during this process
    var hideBtnStyle = '<style>#loginwithamazon_or_text, #loginwithamazon_button { display: none !important; }</style>';
    jQuery('head').append(hideBtnStyle);
});

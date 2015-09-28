/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
function getURLParameter(name, source) {
    return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
        '([^&;]+?)(&|#|;|$)').exec(source)||[,""])[1].replace(/\+/g,'%20'))||
        null;
}
var accessToken = getURLParameter("access_token", location.hash);
var csrfToken = getURLParameter("state", location.hash);

if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
    document.cookie = "amazon_Login_accessToken=" + accessToken + ";secure";
    var baseLocation = document.location.toString().split('#')[0];
    window.location =  baseLocation + '&access_token=' + accessToken + '&state=' + csrfToken;
}

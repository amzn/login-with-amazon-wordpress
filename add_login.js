/**
 * Amazon Login - Login for WordPress
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
function r(f){/in/.test(document.readyState)?setTimeout('r('+f+')',9):f()}
r(function(){
    var formTargetIDs = ['registerform', 'loginform'];
    var idx;
    var found;

    for (idx = 0; idx < formTargetIDs.length; ++idx) {
        var formElement = document.getElementById(formTargetIDs[idx]);
        if(formElement) {
            found = true;
            addLoginToForm(formElement, idx);
        }
    }

    if ( found ) {
        var url = loginwithamazon.ajaxurl + '&ts=' + Date.now();
        loginwithamazon_script( url, window.document.getElementsByTagName('head')[0], 'loginwithamazon' );
    }
});

function addLoginToForm(formElement, idx) {
    var orElement = buildOrElement();
    formElement.appendChild(orElement);
    var loginElement = buildLoginElement(idx);
    formElement.appendChild(loginElement);
    //activateLoginWithAmazonButtons is defined in the footer of each page.
    activateLoginWithAmazonButtons('LoginWithAmazon-' + idx);
}

function buildOrElement() {
    var orElement = document.createElement('div');
    orElement.id = 'loginwithamazon_or_text';
    orElement.style.clear = 'both';
    orElement.style.padding = '15px 0';
    orElement.style.textAlign = 'center';
    orElement.innerHTML = '- OR - ';
    return orElement;
}

function buildLoginElement(idx) {
    var loginElement = document.createElement('div');
    loginElement.id = 'loginwithamazon_button';
    loginElement.style.textAlign = 'center';
    loginElement.innerHTML = '<a href="#" id="LoginWithAmazon-' + idx + '" style="display:inline-block;"><img border="0" alt="Login with Amazon" src="https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_156x32.png" width="156" height="32" style="display:block;" /></a>';
    return loginElement;
}

function activateLoginWithAmazonButtons(elementId) {
    var cfg = window.loginwithamazon_config;
    document.getElementById(elementId).onclick = function() {
        amazon.Login.authorize( cfg.options, cfg.redirect );
        return false;
    };
}

function loginwithamazon_loader( cfg ) {
    window.loginwithamazon_config = cfg;
    window.onAmazonLoginReady = function() {
        amazon.Login.setClientId(cfg.client_id);
        amazon.Login.setUseCookie(true);
        cfg.logout && amazon.Login.logout();
    };

    loginwithamazon_script( 'https://api-cdn.amazon.com/sdk/login1.js', document.getElementById('amazon-root'), 'amazon-login-sdk' );
}

function loginwithamazon_script( url, container, id ) {
    var a = document.createElement('script');
    a.type = 'text/javascript'; a.async = true;
    if ( id ) {
        a.id = 'amazon-login-sdk';
    }
    a.src = url;
    container && container.appendChild(a);
}

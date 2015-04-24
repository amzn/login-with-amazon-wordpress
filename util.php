<?php
/**
 * Utilities for Login With Amazon.
 */
class loginWithAmazonUtil {
    /**
     * Retrieve amazon client id.
     *
     * @return string Amazon Client ID
     */
    public static function getAmazonClientId() {
        return 'test';
    }

    /**
     * Get user email address from Amazon access token.
     *
     * @return string|bool Email Address or False
     */
    public static function getEmailFromAccessToken($accessToken) {
        $result = self::json_curl_wrapper('https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($accessToken));
        if(!isset($result['aud']) || $result['aud'] != self::getAmazonClientId()) {
            return false;
        }
        
        $result = self::json_curl_wrapper('https://api.amazon.com/user/profile', array('Authorization: bearer ' . $accessToken));
        if(isset($result['email'])) {
            return $result['email'];
        } else {
            return false;
        }
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
     * Login or create account by email address.
     *
     * @param string $email Email Address
     * @return string "login" or "create" based on the action taken.
     */
    public static function loginOrCreateByEmail($email) {
        if (email_exists($email) {
            $user = get_user_by('email', $email);
            wp_set_auth_cookie($user->ID);
            $_SESSION['fblogin'] = true;
            return 'login';
        } else {
            $password = wp_generate_password();
            wp_create_user($email, $password, $email);
            return 'create';
        }
    }
}
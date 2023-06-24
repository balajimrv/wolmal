<?php

class Whcore_Api_Core extends Core_Api_Abstract {

    /**
     * Checks if a user's agent is Apple.
     *
     * @return bool
     */
    public function isApple() {
        return (isset($_SERVER['HTTP_USER_AGENT']) && (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPod') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')));
    }

    /**
     * Checks if a user vieweing site from mobile device.
     *
     * @return bool
     */
    public function isMobile() {
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;
        return (bool) $mobile;
    }

}

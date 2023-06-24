<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ViewMore.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_ConvertAppSiteUrl extends Zend_View_Helper_HtmlElement {

  public function convertAppSiteUrl($url) {
    if (Engine_Api::_()->sitemobile()->isApp() && !preg_match("~^(?:f|ht)tps?://~i", $url)) {
      $baseUrl = $this->view->baseUrl();
      if (strpos($url, $baseUrl) !== false) {
        $url = substr($url, strlen($baseUrl));
      }
      $http = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
      $host = $_SERVER['HTTP_HOST'];
      $baseFullUrl = $http . $host . $baseUrl;
      $baseFullUrl = rtrim($baseFullUrl, '/') . "/";
      $url = ltrim($url, '/');
      $url = $baseFullUrl . $url;
    }
    return $url;
  }

}
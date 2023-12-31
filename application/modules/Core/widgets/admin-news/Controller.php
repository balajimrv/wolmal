<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Widget_AdminNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Zend_Feed required DOMDocument
    if( !class_exists('DOMDocument', false) ) {
      $this->view->badPhpVersion = true;
      return;
    }

    // Get params
    $this->view->url = $url = 'https://feeds.feedburner.com/socialengine/blog';
    $this->view->max = $max = $this->_getParam('max', 4);
    $this->view->strip = $strip = $this->_getParam('strip', false);
    $cacheTimeout = 1800;

    // Cacheing
    $cache = Zend_Registry::get('Zend_Cache');
    if( $cache instanceof Zend_Cache_Core &&
        $cacheTimeout > 0 ) {
      $cacheId = get_class($this) . md5($url . $max . $strip);
      $channel = $cache->load($cacheId);
      if( !is_array($channel) || empty($channel) ) {
        $channel = null;
      } else if( time() > $channel['fetched'] + $cacheTimeout ) {
        $channel = null;
      }
    } else {
      $cacheId = null;
      $channel = null;
    }

    if( !$channel ) {
      $rss = Zend_Feed::import($url);
      
      $channel = array(
        'title'       => $rss->title(),
        'link'        => $rss->link(),
        'description' => $rss->description(),
        'items'       => array(),
        'fetched'     => time(),
      );

      // Loop over each channel item and store relevant data
      $count = 0;
      foreach( $rss as $item ) {
        if( $count++ >= $max ) break;
        $channel['items'][] = array(
          'title'       => $item->title(),
          'link'        => $item->link(),
          'description' => $item->description(),
          'pubDate'     => $item->pubDate(),
          'guid'        => $item->guid(),
        );
      }

      $this->view->isCached = false;

      // Caching
      if( $cacheId && !empty($channel) ) {
        $cache->save($channel, $cacheId);
      }
    } else {
      $this->view->isCached = true;
    }

    $this->view->channel = $channel;
  }
}

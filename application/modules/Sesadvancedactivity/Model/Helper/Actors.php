<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Actors.php 2017-01-07 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_Model_Helper_Actors extends Sesadvancedactivity_Model_Helper_Abstract
{
  public function direct($subject, $object, $separator = ' &rarr; ')
  {
    $pageSubject = Engine_Api::_()->core()->hasSubject() ? Engine_Api::_()->core()->getSubject() : null;

    $subject = $this->_getItem($subject, false);
    $object = $this->_getItem($object, false);
    
    // Check to make sure we have an item
    if( !($subject instanceof Core_Model_Item_Abstract) || !($object instanceof Core_Model_Item_Abstract) )
    {
      return false;
    }

    $attribs = array('class' => 'feed_item_username');

    if( null === $pageSubject ) {
      return $subject->toString($attribs) . $separator . $object->toString($attribs);
    } else if( $pageSubject->isSelf($subject) ) {
      return $subject->toString($attribs) . $separator . $object->toString($attribs);
    } else if( $pageSubject->isSelf($object) ) {
      return $subject->toString($attribs);
    } else {
      return $subject->toString($attribs) . $separator . $object->toString($attribs);
    }
  }
}

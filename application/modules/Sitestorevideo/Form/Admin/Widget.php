<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitestorevideo_comment_widgets', array(
        'label' => 'Store Profile Most Commented Videos',
        'maxlength' => '3',
        'description' => 'How many videos will be shown in the store profile most commented videos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_recent_widgets', array(
        'label' => 'Store Profile Most Recent Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the store profile most recent videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.recent.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_like_widgets', array(
        'label' => 'Store Profile Most Liked Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the store profile most liked videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.like.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_view_widgets', array(
        'label' => 'Store Profile Most Viewed Videos',
        'maxlength' => '3',
        'description' => 'How many videos will be shown in the store profile most viewed videos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.view.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_rate_widgets', array(
        'label' => 'Store Profile Top Rated Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the store profile top rated videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.rate.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_featured_widgets', array(
        'label' => 'Store Profile Featured Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the store profile featured videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.featured.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestorevideo_homerecentvideos_widgets', array(
        'label' => 'Recent Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the recent videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.homerecentvideos.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Form_Search extends Engine_Form {

  public function init() {

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'text', array(
        'label' => 'Search',
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));

    $this->addElement('hidden', 'tag', array(
        'order' => 990,
    ));

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Most Rated',
            'comment_count' => 'Most Commented',
            'like_count' => 'Most Liked',
            'featured' => 'Featured',
        ),
        'onchange' => 'this.form.submit();',
    ));
  }

}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchvideo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Searchvideo extends Engine_Form {

  public function init() {

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Text', 'text', array(
        'label' => 'Video Keywords',
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));

    $this->addElement('Hidden', 'tag', array());
if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")){
    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Highest Rated',
            'comment_count' => 'Most Commented',
            'like_count' => 'Most Liked',
        ),
        'onchange' => 'this.form.submit();',
    ));
}else{
     $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Highest Rated',
//            'comment_count' => 'Most Commented',
            'like_count' => 'Most Liked',
        ),
        'onchange' => 'this.form.submit();',
    ));
}
  }

}
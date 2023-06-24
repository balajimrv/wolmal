<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Featured.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Editors_Featured extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle('Featured Editor')
            ->setDescription('Displays a featured editor selected by admin.');

    //VALUE FOR BORDER COLOR.
    $this->addElement('Text', 'editor_title', array(
        'label' => 'Editor',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '/application/modules/Sitestoreproduct/views/scripts/admin-editors/add-featured-editor.tpl',
                    'class' => 'form element')))
    ));
    $this->addElement('Hidden', 'user_id', array());
  }

}
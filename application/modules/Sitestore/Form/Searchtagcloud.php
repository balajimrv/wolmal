<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchtagcloud.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Searchtagcloud extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form_tagscloud',
                'class' => 'global_form_box_tagscloud',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'sitestore_general'));

    $this->addElement('Hidden', 'tag', array(
        'order' => 2
    ));
  }

}

?>
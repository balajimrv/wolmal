<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutdefault.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Layoutdefault extends Engine_Form {

  public function init() {

    $this->setAttrib('id', 'form-upload');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {

			$store_profile_layout = "Store Timeline Cover Photo Layout - 1) " . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/admin/tabbedlayout.gif\');">Tabbed Layout</a>'. '     '. '2) <a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/admin/withouttabbedlayout.gif\');">Without Tabbed Layout</a>';
			$store_member_layout = "Group Cover Photo Layout - 1) " . '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/admin/tabbedlayout.gif\');">Tabbed Layout</a>'. '     '. '2) <a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/admin/withouttabbedlayout.gif\');">Without Tabbed Layout</a>';

			$this->addElement( 'Radio' , 'sitestore_layout_cover_photo' , array (
				'label' => 'Cover Photo Layout',
				'description' => "Select a layout for the cover photo to be placed on the profile of stores on your site.",
				'multiOptions' => array (
					1 => $store_profile_layout,
					0 => $store_member_layout
				) ,
				//'value' => $coreSettings->getSetting('sitestore.layout.cover.photo', 1),
        'escape' => false
			));
    }

    $this->addElement('Radio', 'sitestore_layout_setting', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formRadioButtonStructure.tpl',
                    'class' => 'form element'
            )))));

    $this->addElement('Button', 'submit1', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

  }

}

?>
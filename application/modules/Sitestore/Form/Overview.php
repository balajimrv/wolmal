<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Overview extends Engine_Form {

  public $_error = array();

  public function init() {
    $this->setTitle('Edit Store Overview')
            ->setDescription('Overview enables you to create a rich profile for your Store using the editor below. Compose the overview and click "Save Overview" to save it.')
            ->setAttrib('name', 'sitestores_overview');
    $upload_url = "";
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
    if (!empty($isManageAdmin)) {
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'store_id' => $store_id), 'sitestore_dashboard', true);
    }
    // Overview
    $this->addElement('TinyMce', 'body', array(
        'label' => '',
//             'required' => true,
        'allowEmpty' => false,
        'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr"))),
    ));

    $this->addElement('Button', 'save', array(
        'label' => 'Save Overview',
        'type' => 'submit',
    ));
  }

}

?>

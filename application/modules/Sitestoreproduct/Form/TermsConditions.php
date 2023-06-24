<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TermsConditions.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_TermsConditions extends Engine_Form {

  public function init() {

    $this->setTitle('Terms and Conditions')
          ->setAttrib('id', 'store_terms_conditions')
          ->setDescription('Compose the terms and conditions for your store and click "Save" to save it.');
            
    $this->addElement('TinyMce', 'terms_conditions', array(
        'allowEmpty' => false,
        'required' => true,
        'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
        'filters' => array(
          new Engine_Filter_Censor(),
          new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr"))),
    ));
		
    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }
}
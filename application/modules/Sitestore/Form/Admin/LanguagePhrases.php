<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LanguagePhrases.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_LanguagePhrases extends Engine_Form {

  protected $_isArray = true;
  protected $_elementsBelongTo = 'language_phrases';

  public function init() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    $isSitestoreActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isActivate', 0);
    
    if (!empty($isSitestoreActive)) {
			$this->clearDecorators()
							->addDecorator('FormElements');
			$elements = Engine_Api::_()->getApi('language', 'sitestore')->getDataWithoutKeyPhase();
			foreach($elements as $key => $element) {
				$this->addElement('Text', $key, array(
						'label' => "Text for '$element'",
						'value'=> $coreSettings->getSetting( "language.phrases.".str_replace('_',".",$key) ,$element),
				));
			}
    }
  }

}
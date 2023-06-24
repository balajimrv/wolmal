<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Form_Searchbox extends Engine_Form
{
  public function init()
  {
    $this->addElement('Text', 'title', array(
        'label' => '',
        'autocomplete' => 'off',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        )

    );
    $this->addElement('Hidden', 'store_id', array());    
  }
}
?>
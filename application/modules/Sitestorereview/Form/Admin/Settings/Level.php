<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestorereview_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    if( !$this->isPublic() ) {

      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Review Writing on Stores?',
        'description' => 'Do you want to let members write reviews on stores?',
        'multiOptions' => array(
          1 => 'Yes, allow write reviews on stores.',
          0 => 'No, do not allow write reviews on stores.',
        ),
        'value' => 1,
      ));

    }
  }
}
?>
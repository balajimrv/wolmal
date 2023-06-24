<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_SocialshareSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//SHARING IS ALLOWED OR NOT
    $sharingAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.socialshare', 1);

		$social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_preferred_5"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript">
		var addthis_config = {
							services_compact: "facebook, linkedin, google, digg, more",
							services_exclude: "print, email"
		}
		</script>
		<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

		//GET CODE FROM CORE SETTING
    $this->view->code = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.code.share', $social_share_default_code);

		//DONT RENDER THIS IF NOT AUTHORIZED OR DISABLE SHARING BY ADMIN
    if (!Engine_Api::_()->core()->hasSubject() || empty($sharingAllow) || empty($this->view->code)) {
      return $this->setNoRender();
    }
  }

}

?>
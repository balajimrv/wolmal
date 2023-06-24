<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php 
echo $this->partial('application/modules/Sitestore/views/scripts/partialPhotoWidget.tpl', array('paginator' => $this->paginator, 'showLightBox' => $this->showLightBox, 'show_detail' =>  0, 'includeCss' => 1, 'displayStoreName' => $this->displayStoreName, 'displayUserName' => $this->displayUserName, 'showFullPhoto' => $this->showFullPhoto, 'type' => 'creation_date', 'count' => $this->count, 'urlaction' => 'recent'));
?>


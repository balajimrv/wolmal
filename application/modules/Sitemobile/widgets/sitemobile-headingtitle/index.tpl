<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$random_val = rand();
$front = Zend_Controller_Front::getInstance();
$module_name = $front->getRequest()->getModuleName();
if (empty($this->pageHeaderTitle)) {
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
          . '-' . $request->getControllerName();
  $pageTitle = $this->translate($pageTitleKey);
  $pageTitleKey = 'mobilepagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
          . '-' . $request->getControllerName();
  $pageTitle = $this->translate($pageTitleKey);
  if (($pageTitle && $pageTitle != $pageTitleKey)) {
    $title = $pageTitle;
    if (($this->subject() && $this->subject()->getIdentity()) && $this->subject()->getTitle()) {
      $title = $pageTitle . " - " . $this->subject()->getTitle();
    }

    $pageHeaderTitle = $title;
  } else {
    $pageTitle = $title = str_replace(array('<title>', '</title>'), '', $this->headTitle()->toString());
    if (empty($title)) {
      $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
      $pageTitle = $title =  $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'));
    }
    if ($this->subject() && $this->subject()->getIdentity() && $this->subject()->getTitle()) {
      $title = $pageTitle . " - " . $this->subject()->getTitle();
    }
    $pageHeaderTitle = $title;
    if($module_name == "sitealbum")
        $pageHeaderTitle = $this->translate("Advanced Album");
    else if($module_name == "sitemember")
        $pageHeaderTitle = $this->translate("Advanced Member");
  }
} else {
  $pageHeaderTitle = $this->pageHeaderTitle;
    if($module_name == "sitealbum")
        $pageHeaderTitle = $this->translate("Advanced Album");
    else if($module_name == "sitemember")
        $pageHeaderTitle = $this->translate("Advanced Member");
}   
?>
<?php if (Engine_Api::_()->sitemobile()->isApp() && stripos($_SERVER['HTTP_USER_AGENT'], "iOS")): ?>
<div id="mvtop_<?php echo $random_val; ?>">
<h2 class="ui-title"><?php echo!empty($pageHeaderTitle) ? $this->translate($pageHeaderTitle) : '' ?></h2></div>
<?php else: ?>
<h2 class="ui-title"><?php echo!empty($pageHeaderTitle) ? $this->translate($pageHeaderTitle) : '' ?></h2>
<?php endif; ?>

<script type="text/javascript">
$('#mvtop_<?php echo $random_val; ?>').click(function(){	
	$('body').animate({scrollTop: 0}, 500);
 });
</script>
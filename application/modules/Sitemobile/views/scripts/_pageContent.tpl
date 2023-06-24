<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _pageContent.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php $hasIncludeHomeLink = $this->identity !== 'user-index-home' && $this->identity !== 'core-index-index' && 0;
; ?>
<?php
$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
$sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');
$settingsParams= array();
$settingsParams['dafaultValues'] = array('mobile' => 'panel_reveal_list', 'tablet' => 'panel_reveal_icon', 'appmobile' => 'panel_reveal_list', 'apptablet' => 'panel_reveal_icon');
$dashboardContentType = $sitemobileSettingsApi->getSetting('sitemobile.dashboard.contentType', $settingsParams);
 $pageTitle=  $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'));
//  if(Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
// $dashboardContentType = $coreSettingsApi->getSetting('sitemobile.dashboard.contentType', 'panel_reveal_list');
// } else {
// $dashboardContentType = $coreSettingsApi->getSetting('sitemobile.tablet.dashboard.contentType', 'panel_reveal_icon');
// }


if (empty($this->contentType)) {
  $this->contentType = 'page';
}
//$hasIncludeMenuLink = (($this->onLoad && $dashboardContentType == 'panel' ) || $dashboardContentType !== 'panel') && $this->identity !== 'sitemobile-browse-browse';
$hasIncludeMenuLink = $this->identity !== 'sitemobile-browse-browse';
$is_cache=true; 
if(stripos($this->identity,'-photo-view')):
  $is_cache = false;
endif;
if ($hasIncludeMenuLink && in_array($this->identity, array('core-index-index','user-auth-login', 'user-signup-index','sitemobile-error-requireuser','core-error-requireuser','core-index-landing','core-index-index-mobile','user-auth-login-mobile', 'user-signup-index-mobile','sitemobile-error-requireuser-mobile','core-error-requireuser-mobile'))) {
  $is_cache= false;
  $dashboardShowArray = $coreSettingsApi->getSetting('sitemobile.dashboard.display', array('login', 'signup'));

  $type = null;
  switch ($this->identity) {
    case 'core-index-index':
    case 'user-auth-login': 
    case 'sitemobile-error-requireuser':
    case 'core-error-requireuser':
    case 'core-index-landing':
    case 'core-index-index-mobile':
    case 'user-auth-login-mobile': 
    case 'sitemobile-error-requireuser-mobile':
    case 'core-error-requireuser-mobile':
      $type = 'login';
      break;
    case 'user-signup-index-mobile':
    case 'user-signup-index':
      $type = 'signup';
      break;
  }

  if(empty($dashboardShowArray)) 
    $dashboardShowArray = array();

  if (!empty($type) && !in_array($type, $dashboardShowArray)) {
    $hasIncludeMenuLink = false;
  }
}
if($this->noDomCache)
$is_cache= false;
?>
<?php if ($this->contentType == 'dialog'): ?>
  <?php if ($this->identity === 'core-utility-success'): ?>
    <div data-role="dialog" data-position-to="window" <?php echo $this->dataHtmlAttribs("dialog_success", array('data-theme' => "a", 'data-overlay-theme' => "a", "data-tolerance" => "15,15")); ?>  class="ui-content jqm_dialog_<?php echo $this->identity ?>" id="jqm_dialog_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>" data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" >
      <div data-role="content" <?php echo $this->dataHtmlAttribs("dialog_content"); ?> >
        <?php echo $this->partial(
                'utility/success.tpl', 'sitemobile', $this->getVars()); ?>
      </div> 
      <div  <?php echo $this->dataHtmlAttribs("dialog_footer"); ?> >
      </div> 
    </div>
  <?php else: ?>
    <div data-role="dialog" <?php echo $this->dataHtmlAttribs("dialog", array('data-overlay-theme' => "a", 'data-theme' => "c", "data-tolerance" => "15,15")); ?> data-close-btn="left" id="jqm_dialog_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>" class="jqm_dialog_<?php echo $this->identity ?>" data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" >

      <div data-role="header" <?php echo $this->dataHtmlAttribs("dialog_header"); ?> >
        <h1><?php echo!empty($this->sitemapPageHeaderTitle) ? $this->translate($this->sitemapPageHeaderTitle) : '' ?></h1>
      </div>
      <div data-role="content" <?php echo $this->dataHtmlAttribs("dialog_content"); ?> >
    <?php echo $this->content; ?>
      </div> 
      <div  <?php echo $this->dataHtmlAttribs("dialog_footer"); ?> >
      </div> 
    </div>
  <?php endif; ?>
  <?php elseif ($this->contentType == 'page'): ?>
<?php $dashBoardPanelId='dashboardPanelMenu'.rand(); ?>
  <div <?php echo $this->dataHtmlAttribs("page", array("data-role" => "page")); ?> id="jqm_page_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>"  class="ui-responsive-panel jqm_page_<?php echo $this->identity ?> <?php if ($this->hasFixed): ?>p_fixed <?php endif; ?>" <?php if($is_cache):?> data-dom-cache="true"<?php endif; ?>  <?php if ($this->onLoad): ?> data-url="<?php echo $this->url()?>" <?php endif; ?> data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" data-theme="c" data-dashboardpanelid="<?php echo $dashBoardPanelId?>">
    <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_overlay_list'))): ?>
      <div class="page_wrapper">
        <?php endif; ?>  
        <?php if ($this->headeContent || Zend_Registry::isRegistered('setFixedCreationForm') ||Zend_Registry::isRegistered('setOnlyHeaderTitle')): ?>
        <div <?php echo $this->dataHtmlAttribs("page_header", array("data-role" => "header")); ?> style="overflow: hidden" class="ui-page-header-layout">
          <?php if(Zend_Registry::isRegistered('setHeaderBack') ): ?>
           <a href='javascript://' class='ui-btn-left' data-rel='back' data-icon='arrow-l'  data-iconpos="notext"  data-logo="true"></a>
           <?php endif; ?>
        <?php if(Zend_Registry::isRegistered('setOnlyHeaderTitle')): ?>
         <h2 ><?php echo $this->translate(Zend_Registry::get('setOnlyHeaderTitle')); ?></h2>  
        <?php elseif(Zend_Registry::isRegistered('setFixedCreationForm')): ?>

            <?php //if (!$this->onLoad): ?>
           <?php if(Zend_Registry::isRegistered('setFixedCreationFormBack') && stristr(Zend_Registry::get('setFixedCreationFormBack'), 'cancel') !== FALSE): ?>
            <a href='javascript://' class='ui-btn-left' data-rel='back' data-icon='false'    ><?php echo $this->translate('Cancel') ?></a>
            <?php 
              else: ?>
                <a href='javascript://' class='ui-btn-left' data-rel='back' data-icon='arrow-l'  data-iconpos="notext"  data-logo="true"></a>
              <?php endif; ?>
                <?php if(Zend_Registry::isRegistered('setFixedCreationHeaderTitle')): ?>
                <h2 class="form-title"><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderTitle')); ?></h2><?php endif; ?>
           <?php if(Zend_Registry::isRegistered('setFixedCreationHeaderRemove')): ?>
             <a data-role="button" data-iconpos="notext" data-icon="trash"  href="<?php echo $this->url(array('action' => 'remove-photo')) ?>" class="header_submit_button smoothbox" ><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderRemove'))  ?></a><?php endif;?>
             
             <?php if(Zend_Registry::isRegistered('setFixedCreationHeaderForum')): ?>
             <a data-role="button" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" data-icon="plus" data-iconpos="notext" data-role="button" href="<?php echo $this->url(array('action' => 'topic-create')) ?>" class="header_submit_button" ><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderForum'))  ?></a><?php endif;?>
        
                <?php if(Zend_Registry::isRegistered('setFixedCreationHeaderSubmit')): ?>
           <a data-role="button" class="header_submit_button" data-rel="<?php echo Zend_Registry::get('setFixedCreationFormId')?>"><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderSubmit'))  ?></a><?php endif;?>
          <?php else: ?>

            <?php if ($hasIncludeMenuLink): ?>
              <?php if(Zend_Registry::isRegistered('setFixedCreationFormBack')): ?>
               <a href='javascript://' class='ui-btn-left' data-rel='back' data-icon='arrow-l'  data-iconpos="notext"  data-logo="true"></a>
              <?php else: ?>
               <?php 
               // Disable widget in case of REST ANDROID APP calling
               $session = new Zend_Session_Namespace();
               if(!isset($session->hideHeaderAndFooter) || (isset($session->hideHeaderAndFooter) && empty($session->hideHeaderAndFooter))):
                   ?>
              <a href="<?php echo in_array($dashboardContentType,  array('panel_overlay_list','panel_reveal_list','panel_overlay_icon','panel_reveal_icon')) ? '#'.$dashBoardPanelId : $this->url(array(), 'sitemobile_dashboard', true); ?>"  data-role="button" <?php echo $this->dataHtmlAttribs("dashboard_menu_button", array('data-icon' => "reorder")); ?> id="header-dashboard-menuButton" ><?php //echo $this->translate('Menu') ?></a>
              <?php endif; ?>
              <?php endif;?>
            <?php elseif (!$this->onLoad): ?>

              <!--          <a href='javascript:void(0);' class='ui-btn-left' data-rel='back' data-icon='arrow-l' <?php if (!$this->translate("SITEMOBILE_PAGE_BACK")): ?> data-iconpos="notext" <?php endif; ?> >
              <?php echo $this->translate("SITEMOBILE_PAGE_BACK") ?>
                        </a>-->
            <?php endif; ?>
            <div data-role="player_pause" class="ui-btn-right header-player-pause ui-btn-icon-notext ui-shadow ui-btn-corner-all ui-icon-pause" style="display: none;"> </div>
                <div data-role="scroll_top" class="scroll_top ui-btn-right header-second-right-button dnone ui-btn-icon-notext ui-shadow ui-btn-corner-all ui-icon-chevron-up" ></div>
          <?php echo $this->headeContent; ?>
        <?php endif; ?>  
        </div>
  <?php endif; ?>
        <?php 
          //add scrolling if contentscrolling set in zend registry

          $scrollAutoloading = false;
        ?>
        <div <?php echo $this->dataHtmlAttribs("page_content", array("data-role" => "content")); ?> data-content="main"  class="ui-content"  style="padding: 0;" >
               
        <div class="connection_offlinemode" style="display: none;">
          <span class="ui-icon ui-icon-warning-sign"></span>
        <?php echo  $this->translate("No Internet Connection"); ?>
        </div>
      <div data-role="wrapper" style="padding: 5px 5px 15px;">
          <div data-role="scroller" >
            <?php if ($this->identity === 'core-error-notfound'): ?>
              <?php echo $this->partial(
                      'error/notfound.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-utility-success'): ?>
              <?php echo $this->partial(
                      'utility/success.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-error-requireauth'): ?>
              <?php echo $this->partial(
                      'error/requireauth.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-error-error'): ?>
    <?php echo $this->partial(
                      'error/error.tpl', 'sitemobile', $this->getVars()); ?>
      <?php else: ?>
          <?php echo $this->content; ?>
        <?php endif; ?>
          </div>
        </div>
          
       
            <div class="feeds_loading scroll-pullup dnone">
              <i class="icon_loading"></i>
          </div>
        
      </div> 
    <?php if ($this->footerContent && strlen($this->footerContent) > 100 && !Zend_Registry::isRegistered('setFixedCreationForm')): ?>  
        <div <?php echo $this->dataHtmlAttribs("page_footer", array("data-role" => "footer")); ?>  class="ui-page-footer-layout">
      <?php echo $this->footerContent; ?>
        </div>
      <?php endif; ?>
      <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_overlay_list'))): ?>
      </div>
      <?php endif; ?>
      <?php if (in_array($dashboardContentType,  array('panel_overlay_list','panel_reveal_list','panel_overlay_icon','panel_reveal_icon')) && $hasIncludeMenuLink): ?>
       <div data-role="panel" class="dashboardPanelMenu ui-bar-d" <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_reveal_icon')) || Engine_Api::_()->sitemobile()->isApp() ): ?> data-animate="false" <?php endif; ?> data-theme="d" data-display="<?php echo (in_array($dashboardContentType,  array('panel_reveal_list','panel_reveal_icon'))) ? 'reveal':'overlay' ?>" id="<?php echo $dashBoardPanelId ?>"   <?php echo $this->dataHtmlAttribs("dashboard_panel", array('data-dismissible' => 'true')); ?> >
      <?php echo $this->content('dashboard_panel', true); ?>
      </div>
          <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_reveal_icon'))): ?>
        <div class="dashboard-panel-mini-menu ui-bar-d">
          <?php echo $this->action('browse', 'browse', 'sitemobile',  array('showSearch'=>0,'fromWidgt'=>1)) ?>
        </div>
    <?php endif; ?>
  <?php endif; ?>
  </div>
<?php endif; ?>
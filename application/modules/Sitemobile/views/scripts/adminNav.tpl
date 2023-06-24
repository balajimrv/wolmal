<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adminNav.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Mobile / Tablet Plugin'); ?></h2>
<?php
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi')):
  ?>
  <!--Show this tip only when mobi plugin is enabled-->
  <div class="tip">
    <span>
      <?php echo $this->translate('Another mobile plugin is enable, To ensure proper working of this mobile plugin, disable the already existing mobi plugin.'); ?>  <?php echo $this->htmlLink(array('reset' => false, 'module' => 'core', 'controller' => 'packages', 'action' => 'index'), $this->translate('Disable'), array('class' => 'buttonlink')) ?>
    </span>
  </div>
  <?php ?>
<?php endif; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->subNavigation)): ?>
  <div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>
<style type="text/css">

  .seaocore_admin_tabs > ul > li:hover ul
  {
    display: block;
  }
  .seaocore_admin_tabs ul ul {
    display: none;
    position: absolute;
    margin: 0;
    padding: .25em 0;
    min-width: 170px;
    background: #444;
    border-bottom-left-radius: 3px;
    border-bottom-right-radius: 3px;
    border-top-right-radius: 3px;
    z-index: 9999999999;
    margin-top: 26px;
  }

  .seaocore_admin_tabs ul ul li{
    float: none !important;
  }
  .seaocore_admin_tabs ul ul li:hover
  {
    background-color: #5BA1CD;
  }
  .seaocore_admin_tabs ul ul li a
  {
    letter-spacing: 0px;
    text-decoration: none;
    font-size: 8pt;
    display: block;
    padding: .5em 12px !important;
    outline: none;
    color: #aaa !important;
    background-color: #444 !important;
  }
  .seaocore_admin_tabs ul ul li a:hover
  {
    color: #fff !important;
    background: #555 !important;
  }
</style>
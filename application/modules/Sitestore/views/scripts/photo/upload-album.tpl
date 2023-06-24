<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: uploadalbum.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
?>
<script type="text/javascript">
  var updateTextFields = function()
  {
    var album = document.getElementById("album");
    var name = document.getElementById("title-wrapper");
    var auth_tag = document.getElementById("auth_tag-wrapper");

    if (album.value == 0)
    {
      name.style.display = "block";
      auth_tag.style.display = "block";
    }
    else
    {
      name.style.display = "none";
      auth_tag.style.display = "none";
    }
  }
  en4.core.runonce.add(updateTextFields);

  var album_id = '<?php echo $this->album_id ?>';
  var store_id = '<?php echo $this->sitestore->store_id; ?>';
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<?php $albumid = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null); ?>
<?php if (!empty($albumid)): ?>
  <?php $albums = Engine_Api::_()->getItem('sitestore_album', $albumid); ?>
<?php endif; ?>
<div class="sitestore_viewstores_head">
  <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>

  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>
    <?php echo $this->sitestore->__toString() ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php if (!empty($albumid) && empty($this->can_edit)) : ?>
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $albums->title; ?>
    <?php endif; ?>
  </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumcreate', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
  <div class="layout_right" id="communityad_uploadalbum">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumcreate', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_uploadalbum'))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewalbum.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<div class="sitestore_viewstores_head">
  <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2><?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), $this->sitestore->getTitle()) ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id), 'tab' => $this->tab_selected_id), $this->translate('Albums')) ?>
  </h2>
</div>
<!--RIGHT AD START HERE-->
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
  <div class="layout_right" id="communityad_viewalbum">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_adalbumview'))?>
  </div>
<?php endif; ?>
<!--RIGHT AD END HERE-->

<div class="layout_middle">
  <?php if (count($this->album) > 0) : ?>
    <div class="sitestore_album_box">
      <ul class="thumbs">
        <?php foreach ($this->album as $albums): ?>
          <li style="height:200px;">
            <?php if ($albums->photo_id != 0): ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title; ?>">
                <span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>
            <?php else: ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" class="thumbs_photo"  title="<?php echo $albums->title; ?>">
                <span><?php echo $this->itemPhoto($albums, 'thumb.normal') ?></span>
              </a>
            <?php endif; ?>
            <div class="sitestore_profile_album_title">
              <a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title; ?>"><?php echo $albums->title; ?></a>
            </div>
            <div class="sitestore_profile_album_stat">
              <?php echo $this->translate(array('%s photo', '%s photos', $albums->count()), $this->locale()->toNumber($albums->count())) ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>
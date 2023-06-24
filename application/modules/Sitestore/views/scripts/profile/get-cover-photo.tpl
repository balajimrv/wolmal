<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact-detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->sitestore->store_cover) && $this->photo) : ?>
  <div class="sitestore_cover_photo cover_photo_wap b_dark">
    <?php if (empty($this->can_edit)) : ?>
      <a href="<?php echo $this->photo->getHref(); ?>" <?php if (SEA_SITESTOREALBUM_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $this->photo->getHref(); ?>");return false;' <?php endif; ?>>
      <?php endif; ?>
      <?php echo $this->itemPhoto($this->photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style' => 'top:' . $this->coverTop . 'px')); ?>
      <?php if (empty($this->can_edit)) : ?></a><?php endif; ?>
    <?php if (!empty($this->can_edit)) : ?>
      <div class="cover_tip_wrap dnone">
        <div class="cover_tip"><?php echo $this->translate("Drag to Reposition Cover Photo") ?></div>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <?php if ($this->show_member): ?>
    <div class="sitestore_members_cover_listing" <?php if (!empty($this->showContent) || !empty($this->statistics)) : ?> style="border-bottom: none;" <?php endif; ?>>
      <?php $width= 100 /$this->membersCountView; 
        if($this->membersCountView > $this->membersCount && ($this->membersCountView - $this->membersCount) <=2 ):
          $width= 100 /$this->membersCount;
        endif;
      ?>
      <?php $i=1; foreach ($this->members as $member):
        if($i > $this->membersCountView):
          break;
        endif;
        $i++;
        $user = Engine_Api::_()->getItem('user', $member->user_id); ?>
        <div class="sitestore_members_cover_member" style="width:<?php echo $width?>%;"> 
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
          <span class="sitestore_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="sitestore_cover_photo_empty" ></div>
  <?php endif; ?>
<?php endif; ?>
<?php if (!empty($this->can_edit)) : ?>
  <div id="seao_cover_options" class="sitestore_cover_options <?php if (empty($this->sitestore->store_cover) || empty($this->photo)) : ?> dblock <?php endif; ?>">
    <ul class="edit-button">
      <li >
        <span class="sitestore_cover_photo_btn sitestore_icon_photos_settings cover_photo_btn"><?php if (!empty($this->sitestore->store_cover) && $this->photo) : ?><?php echo $this->translate("Edit Cover Photo"); ?><?php else: ?><?php echo $this->translate("Add Cover Photo"); ?><?php endif; ?></span>
        <ul class="sitestore_cover_options_pulldown">
          <li>
            <a href='<?php echo $this->url(array('action' => 'upload-cover-photo', 'store_id' => $this->sitestore->store_id), 'sitestore_dashboard', true); ?>'  class="icon_sitestore_photo_new smoothbox"><?php echo $this->translate('Upload Cover Photo'); ?></a>
          </li>
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum') && Engine_Api::_()->sitestore()->allowInThisStore($this->sitestore, 'sitestorealbum', 'spcreate')): ?>
            <li >
              <?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'store_id' => $this->sitestore->store_id, 'recent' => 1), 'sitestore_dashboard', true), $this->translate('Choose from Album Photos'), array(' class' => 'sitestore_icon_photos_manage smoothbox')); ?>
            </li>
          <?php endif; ?>
          <?php if (!empty($this->sitestore->store_cover) && $this->photo) : ?>
            <li><a  href="javascript:document.seaoCoverPhoto.reposition.start()" class="cover_reposition sitestore_icon_move"><?php echo $this->translate("Reposition"); ?></a></li>

            <li>
              <?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'action' => 'remove-cover-photo', 'store_id' => $this->sitestore->store_id), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox sitestore_icon_photos_delete')); ?>
            </li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
    <?php if (!empty($this->sitestore->store_cover)) : ?>
      <ul class="save-button dnone">
        <li >
          <span class="positions-save sitestore_cover_action"><?php echo $this->translate("Save Position"); ?></span>
        </li>
        <li>
          <span class="positions-cancel sitestore_cover_action"><?php echo $this->translate("Cancel"); ?></span>
        </li>
      </ul>
    <?php endif; ?>
  </div>
<?php endif; ?>
<div class="clr"></div>
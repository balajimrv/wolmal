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
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="cover-photo-wrap">
    <div class="cover-photo">
      <?php echo $this->itemPhoto($this->subject(), 'thumb.profile'); ?>
      <div class="cover-profile-photo-cover"></div>
    </div>
    <div class="cover-profile">
      <div class="cover-profile-info">
          <strong class="title"><?php echo $this->subject()->getTitle() ?></strong>
            <?php if( $this->auth  && isset($this->subject()->status)): ?>
              <div style="margin-left:10px;" class="f_small">
                <?php echo $this->viewMore($this->subject()->status) ?>
              </div>
            <?php endif; ?>
<!--          <p><?php //if($this->subject()->getType() == 'group'):
             //echo $this->translate('Led By ');
//            else:
//             echo $this->translate('Hosted By '); ?>
            <strong><?php //echo $this->translate($this->subject()->host); ?>
            </strong>
            <?php //endif; ?>
          </p>-->
      </div>
    </div>
  </div>
<?php else:?>
  <div class="sm_profile_item_photo">
    <?php if($this->subject()->getType() == 'blog' || $this->subject()->getType() == 'sitepagedocument_document' || $this->subject()->getType() == 'sitebusinessdocument_document' || $this->subject()->getType() == 'sitegroupdocument_document' ) :?>
      <?php echo $this->itemPhoto($this->subject()->getOwner(), 'thumb.profile') ?>
    <?php else :?>
      <?php echo $this->itemPhoto($this->subject(), 'thumb.profile') ?>
    <?php endif;?>
  </div>
  <div class="sm_profile_item_info">
    <div class="sm_profile_item_title">
      <?php echo $this->subject()->getTitle() ?>
    </div>
    <?php if( $this->auth  && isset($this->subject()->status)): ?>
    <div style="margin-left:10px;" class="f_small">
      <?php echo $this->viewMore($this->subject()->status) ?>
    </div>
  <?php endif; ?>
  </div>
<?php endif; ?>
<?php if (($this->subject()->getType() == 'user') && (!$this->auth)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.'); ?>
    </span>
  </div>
<?php endif; ?>
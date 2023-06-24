<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoremember/externals/styles/style_sitestoremember.css'); ?>
<?php
		$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->sitestore->store_id, $layout); ?>

<?php if(empty($this->isAjax)) :?>
	<div class='sitestore_cover_wrapper' id="sitestore_cover_photo">
<?php endif;?>

<?php if(!empty($this->isAjax)) :?>
  <?php if (!empty($this->sitestore->store_cover)) : ?>
     <?php if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorealbum' )) : ?>
			<div class="sitestore_cover_photo">
				<?php echo $this->itemPhoto($this->photo, 'thumb.profile', '', array('align' => 'left')); ?>
			</div>
    <?php else : ?> 
			<?php $k=0; ?>
			<div class="sitestore_members_cover_listing">
				<?php foreach ($this->paginator as $photo):
				$user = Engine_Api::_()->getItem('user', $photo->user_id); ?>
					<div class="sitestore_members_cover_member"> 
						<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
						<span class="sitestore_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
					</div>
					<?php $k++;?>
				<?php endforeach; ?>
			</div> 
    <?php endif; ?>
  <?php elseif(empty($this->sitestore->store_cover)  && empty($this->show_member) && empty($this->paginatorCount)) : ?>
		<div class="sitestore_cover_photo_empty"> </div>
  <?php elseif(!empty($this->show_member) || !empty($this->can_edit)) : ?>
		<?php $k=0; ?>
		<div class="sitestore_members_cover_listing">
			<?php foreach ($this->paginator as $photo):
			$user = Engine_Api::_()->getItem('user', $photo->user_id); ?>
				<div class="sitestore_members_cover_member"> 
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
			    <span class="sitestore_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
				</div>
				<?php $k++;?>
			<?php endforeach; ?>
		</div> 
  <?php endif; ?>
  <?php if (!empty($this->can_edit) && (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorealbum' ))) : ?>
		<div id="edit" class="sitestore_cover_options">
      <ul>
        <li>
          <span class="sitestore_cover_photo_btn sitestore_icon_photos_settings"><?php echo $this->translate("Edit Cover Photo");?></span>
          <ul class="sitestore_cover_options_pulldown">
          	<li>
            	<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => 0, 'tab' => $this->identity_temp), 'sitestore_photoalbumupload', true)  ?>'  class="icon_sitestore_photo_new"><?php echo $this->translate('Create an Album'); ?></a>
            </li>
            <li >
              <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$tab_id)),$this->translate('Choose from Album Photos'), array( ' class' => 'sitestore_icon_photos_manage')); ?>
            </li>
            <?php if (!empty($this->sitestore->store_cover)) : ?>
              <li>
                <?php echo $this->htmlLink(array('route' => 'sitestoremember_approve', 'action' => 'remove-coverphoto', 'store_id' => $this->sitestore->store_id), $this->translate('Remove Cover Photo'), array( ' class' => 'smoothbox sitestore_icon_photos_delete')); ?>
              </li>
            <?php endif; ?>
          </ul>
        </li> 
      </ul>
		</div>
  <?php endif; ?>
  <div class="clr"></div>
<?php endif;?>

<?php if(empty($this->isAjax)) :?>
	</div>
<?php endif;?>

<?php if(empty($this->isAjax)) :?>
	<script type="text/javascript">
		$('sitestore_cover_photo').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/loader.gif" /></center>';
		en4.core.request.send(new Request.HTML({
			'url' : en4.core.baseUrl + 'widget/index/mod/sitestore/name/store-cover-information-sitestore',
			'data' : {
				'format' : 'html',
				'subject' : en4.core.subject.guid,
				'isAjax': 1,
        'show_member': '<?php echo $this->show_member ?>'
			}
		}), {
			'element' : $('sitestore_cover_photo')
		});
	</script>
<?php endif;?>

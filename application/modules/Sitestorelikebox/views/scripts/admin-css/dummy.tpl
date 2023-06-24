<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: dummy.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/sitestorelikebox.css');
?>
<?php if ( $this->activeFileName == "dark.css" ): ?>
	<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/dark.css');

 //$this->headLink()->appendStylesheet( $this->baseUrl( '/application/modules/Sitestorelikebox/externals/styles/dark.css' )) ; ?>
<?php else: ?>
	<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/light.css');
//$this->headLink()->appendStylesheet( $this->baseUrl( '/application/modules/Sitestorelikebox/externals/styles/light.css' )) ; ?>
<?php endif ; ?>

<div id="splb_wrapper" style="height:658px;width:298px;">
	<div class="splb_header" id="like_box_header" title="<?php echo $this->translate('Header'); ?>">
		<?php echo $this->translate('Find us on'); ?>
		<a href="javascript:void(0);"><?php echo $this->translate(' %s', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')); ?></a>
	</div>
	<div class="splb_content">
		<div class="splb_content_top">
			<a href="javascript:void(0);">
				<img class="thumb_icon item_photo_sitestore_store thumb_icon" alt="" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png" title="Store Profile Photo">
			</a>
			<div class="splb_act">
				<a href="javascript:void(0);" title="<?php echo $this->translate('Title of Store'); ?>"><?php echo $this->translate( 'Title of Store' ) ?></a>
		<div class="sitestore_like_button">
			<a><i class="sitestore_like_thumbup_icon"></i><span><?php echo $this->translate( 'Like' ) ?></span></a>
		</div>
	</div>
</div>
<div class="splb_stream">
	<?php //HERE  TAB SHOW ?>
	<div class='splb_tabs_alt'>
		<ul class="splb_tabs" title="<?php echo $this->translate('Tab'); ?>">
			<li class=""  ><a><?php echo $this->translate( 'Updates' ) ; ?></a></li>
			<li class="active"  >
				<a><?php echo $this->translate( 'Info' ) ; ?></a>
			</li>
			<li class=""  >
				<a><?php echo $this->translate( 'Map' ) ; ?></a>
			</li>
		</ul>
	</div>
	<?php //HERE  INFO CONTENT SHOW ?>
	<div class='splb_stream_cont' >
		<div class='splb_info_tab'>
			<h4><?php echo $this->translate( 'Basic Information' ) ; ?></h4>
			<ul>
				<li>
					<span><?php echo $this->translate( 'Posted By:' ) ; ?> </span>
					<a href="javascript:void(0);"><span><?php echo $this->translate( 'Admin' ) ; ?></span></a>
				</li>
				<li>
					<span><?php echo $this->translate( 'Posted:' ) ; ?></span>
					<span><?php echo  date("F j, Y"); ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Comments:' ) ; ?></span>
					<span><?php echo $this->translate( '50' ) ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Likes:' ) ; ?></span>
					<span><?php echo $this->translate( '10' ) ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Price:' ) ; ?></span>
					<span><?php echo $this->translate( '100 USD' ) ; ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Location:' ) ; ?></span>
					<span><?php echo $this->translate( 'Fairview Park, Berkeley, CA' ) ; ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Description:' ) ; ?></span>
					<span><?php echo $this->translate( 'Store Description' ) ; ?></span>
				</li>
			</ul>
			<h4>
				<?php echo $this->translate( 'Contact Details' ) ; ?>
			</h4>
			<ul>
				<li>
					<span><?php echo $this->translate( 'Phone:' ) ; ?></span>
					<span><?php echo $this->translate( '9999999999' ) ; ?> </span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Email:' ) ; ?></span>
					<span><?php echo $this->translate( 'admin@yoursite.com' ) ; ?></span>
				</li>
				<li>
					<span><?php echo $this->translate( 'Website:' ) ; ?></span>
					<span><?php echo $this->translate( 'www.yoursite.com' ) ; ?></span>
				</li>
			</ul>
			<br />
		</div>
	</div>
<div class="splb_fanbox">
	<div class="splb_fanbox_heading" title="<?php echo $this->translate('Number of Likes'); ?>">
		<?php echo $this->translate( '10 people likes' ) ?>
			<b><?php echo $this->translate( 'Title of Store' ) ?></b>
		</div>
		<div class="splb_fanbox_items_list">
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>" />
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>" />
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>" />
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png"  title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
			<div class="splb_fanbox_items">
				<img height='48' width='48' src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/User/externals/images/nophoto_user_thumb_icon.bak.png" title="<?php echo $this->translate('Users who like this store'); ?>"/>
				<div><a href="javascript:void(0);" title="<?php echo $this->translate('Users who like this store'); ?>"><?php echo $this->translate('Admin'); ?></a></div>
			</div>
		</div>
	</div>
	<div class="splb_btm">
		<div class="splb_btm">
			<?php if (empty ($this->photo_name) || (!Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title'))): ?><?php echo $this->translate('Powered By :'); ?>
				<a href="javascript:void(0);" target="_blank" linkindex="32" title="<?php echo $this->translate('Site Title'); ?>"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));  ?></a>
			<?php else: ?><?php echo $this->translate('Powered By :'); ?>
				<a href="javascript:void(0);" target="_blank" linkindex="32" title="<?php echo $this->translate('Site Logo'); ?>">
					<span><img src="<?php echo  $this->photo_name ?>" /></span>
				</a>
			<?php endif; ?>
		</div>
  </div>
</div>
</div>
<style type="text/css">
  #smoothbox_window,
  #global_page_sitestorelikebox-admin-css-dummy,
  #global_page_sitestorelikebox-admin-css-dummy #global_content_simple{
    overflow-y: hidden !important;
    width: 100%;
    padding:0px !important;
  }
</style>
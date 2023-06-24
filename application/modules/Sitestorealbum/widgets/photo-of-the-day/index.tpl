<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestore/externals/styles/style_sitestore.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
$photoSettings = array();
$photoSettings['class'] = 'thumb';
$photoSettings['title'] =  $this->photoOfDay->getTitle();
if ($this->showLightBox):
$photoSettings["onclick"]="openSeaocoreLightBox('".$this->photoOfDay->getHref()."');return false;";
 // include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->photoOfDay->store_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo">
		  <?php echo $this->htmlLink($this->photoOfDay->getHref(), $this->itemPhoto($this->photoOfDay), $photoSettings); ?>
		</div>
		<div class="info">
			<div class="owner">
			  <?php
			  $owner = $this->photoOfDay->getOwner();
			  $parent = $parent = $sitestorealbum_object = Engine_Api::_()->getItem('sitestore_album', $this->photoOfDay->album_id);
			  echo $this->translate('in ').
			          $this->htmlLink($parent->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($parent->getTitle(), 45), 10),array('title' => $parent->getTitle()));
			  ?>
				<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $this->photoOfDay->store_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
				$tmpBody = strip_tags($sitestore_object->title);
				$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->photoOfDay->store_id, $this->photoOfDay->user_id, $this->photoOfDay->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>  
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?> 
					<?php echo $this->translate('by ').
									$this->htmlLink($owner->getHref(), $owner->getTitle(),array('title' => $owner->getTitle()));?>
        <?php endif;?>
			</div>
		</div>	
	</li>  
</ul>
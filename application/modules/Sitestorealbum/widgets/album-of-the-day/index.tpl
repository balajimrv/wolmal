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
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->albumOfDay->store_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo">
		  <?php echo $this->htmlLink($this->albumOfDay->getHref(array('tab' => $tab_id)), $this->itemPhoto($this->albumOfDay), array('title' => $this->albumOfDay->getTitle())); ?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->albumOfDay->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($this->albumOfDay->getTitle(), 45), 10),array('title' => $this->albumOfDay->getTitle())) ?>
			</div>
	    <div class="owner">
				<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $this->albumOfDay->store_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
				$tmpBody = strip_tags($sitestore_object->title);
				$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->albumOfDay->store_id, $this->albumOfDay->owner_id, $this->albumOfDay->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>      
			</div>	
			<div class="stats">
			  <?php echo $this->translate(array('%s photo', '%s photos', $this->albumOfDay->count()), $this->locale()->toNumber($this->albumOfDay->count())); ?>
			</div>
		</div>
	</li>
</ul>		
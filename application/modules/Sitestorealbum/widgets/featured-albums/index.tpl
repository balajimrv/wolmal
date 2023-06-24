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
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css')
?>
<ul class="generic_list_widget generic_list_widget_large_photo">
  <?php foreach( $this->paginator as $item ): ?>
    <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $item->store_id, $layout);?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(array('tab' => $tab_id)), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($item->getTitle(), 45), 10),array('title' => $item->getTitle())) ?>
        </div>
        <div class="stats">                
         <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
         <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
        </div>
        <div class="owner">
					<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $item->store_id);?>
					<?php
					$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
					$tmpBody = strip_tags($sitestore_object->title);
					$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>      
					&bull; <?php echo $this->translate(array('%s photo', '%s photos', $item->count()),$this->locale()->toNumber($item->count())); ?> 
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
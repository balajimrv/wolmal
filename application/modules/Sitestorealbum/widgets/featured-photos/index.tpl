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
<?php
$photoSettings=  array();
$photoSettings['class'] = 'thumb';
?>
<ul class="generic_list_widget generic_list_widget_large_photo">
  <?php foreach( $this->paginator as $item ): ?>
  <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $item->store_id, $layout);?>
  <?php if($this->showLightBox):
  $photoSettings["onclick"]="openSeaocoreLightBox('".$item->getHref()."');return false;";
  endif; ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), $photoSettings) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 13),array('title' => $item->getTitle())) ?>
        </div>
        <div class="owner">
          <?php
            $owner = $item->getOwner();
            $parent = $sitestorealbum_object = Engine_Api::_()->getItem('sitestore_album', $item->album_id);
            echo $this->translate('in ').
                $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
          ?>
					<?php
					$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
					$tmpBody = strip_tags($item->store_title);
					$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->user_id, $item->getSlug()),  $store_title,array('title' => $item->store_title)) ?>      
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?> 
						<?php echo $this->translate('By ').
									$this->htmlLink($owner->getHref(), $owner->getTitle(),array('title' => $owner->getTitle()));?>
          <?php endif;?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
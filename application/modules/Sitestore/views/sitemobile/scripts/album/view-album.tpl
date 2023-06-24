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
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Albums","icon"=>"arrow-d")
 );

echo $this->breadcrumb($breadcrumb);
?>

  <?php if (count($this->album) > 0) : ?>
   <div class="sm-content-list ui-listgrid-view">
      <ul data-role="listview" data-inset="false" data-icon="arrow-r">
        <?php foreach ($this->album as $albums): ?>
          <li id="thumbs-photo-<?php echo $albums->photo_id ?>">
            <a href="<?php echo $albums->getHref(array('store_id' => $albums->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $tab_id)); ?>">
                <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count()) ?></p>			
                <?php if(empty($albums->photo_id)):?>
                <?php echo $this->itemPhoto($albums, 'thumb.normal'); ?>	
                <?php else:?>
                <?php echo $this->itemPhoto($albums, 'thumb.profile'); ?>	
                <?php endif;?>		
               <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
                <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $albums->store_id); ?>
                <p><?php echo $this->translate("in ") ?>
                <b><?php echo $sitestore_object->title ?></b></p>     
            </a>             
          </li>
        <?php endforeach; ?>
      </ul>
    </div>	
  <?php endif; ?>

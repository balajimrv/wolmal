<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialPhotoWidget.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js');
  
  $this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');       
  $i=0;

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
$front = Zend_Controller_Front::getInstance(); 
$action = $front->getRequest()->getActionName();
$moduleName = $front->getRequest()->getModuleName();
?>
<script type="text/javascript">
  var submit_tostoreprofile = true;
  function ShowPhotoStore(storeurl) {
    if (submit_tostoreprofile) {
      window.location = storeurl;
    }
    else {
      submit_tostoreprofile = true;
    }
  }

</script>

<?php if(empty($this->showFullPhoto)):?>
<div class="sitestorealbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="sitestorealbum_sidebar_thumbs">

	    <?php foreach ($this->paginator as $sitestorephoto):  ?>
	      <li class="mbot5"> 
	        <?php //if (!$this->showLightBox): ?>
<!--	          <a href="javascript:void(0)" onclick='ShowPhotoStore("<?php //echo $sitestorephoto->getHref() ?>")' title="<?php //echo $sitestorephoto->title; ?>"  class="thumbs_photo">		
	            <span style="background-image: url(<?php //echo $sitestorephoto->getPhotoUrl('thumb.normal'); ?>);"></span>
	          </a>-->
	        <?php //else: ?>           
	           <a href="<?php echo $sitestorephoto->getHref() ?>" <?php if(SEA_SITESTOREALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitestorephoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitestorephoto->title; ?>" class="thumbs_photo">          
	            <span style="background-image: url(<?php echo $sitestorephoto->getPhotoUrl('thumb.normal'); ?>);"></span>
	          </a>
	        <?php //endif; ?>
          <?php if($this->displayStoreName):?> 
            <div class='sitestorealbum_thumbs_details'>	
                <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $sitestorephoto->store_id, $layout);?>
                <?php $parent = Engine_Api::_()->getItem('sitestore_album', $sitestorephoto->album_id);?>
								<?php echo $this->translate('in ').
										$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorephoto->store_id)), $sitestorephoto->store_title,array('title' => $sitestorephoto->store_title)) ?>
            </div>         
          <?php endif;?>    
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitestorephoto->owner_id)) :?>
              <div class='sitestorealbum_thumbs_details'>	
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?> 
									<?php $userItem = Engine_Api::_()->getItem('user', $sitestorephoto->owner_id);	 ?>         	
									<?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?> 
                <?php endif;?>
              </div>
            <?php endif;?>
          <?php endif;?>      
	        <?php if($this->show_detail == 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitestorealbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitestorephoto->comment_count), $this->locale()->toNumber($sitestorephoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like') :?>
		          <div class='sitestorealbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitestorephoto->like_count), $this->locale()->toNumber($sitestorephoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php else:?>

<div class="sitestorealbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="generic_sitestorealbum_photo_widget">
	    <?php foreach ($this->paginator as $sitestorephoto):  ?>	   
	      <li class="mbot5"> 
          <div class="photo">
            <?php //if (!$this->showLightBox): ?>
<!--              <a href="javascript:void(0)" onclick='ShowPhotoStore("<?php //echo $sitestorephoto->getHref() ?>")' title="<?php //echo $sitestorephoto->title; ?>"  class="thumbs_photo">		
                <img src="<?php //echo $sitestorephoto->getPhotoUrl('thumb.normal'); ?>" class="thumb_normal" />
              </a>-->
            <?php //else: ?>
             <a href="<?php echo $sitestorephoto->getHref() ?>" <?php if(SEA_SITESTOREALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitestorephoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitestorephoto->title; ?>" class="thumbs_photo">          
	            <img src="<?php echo $sitestorephoto->getPhotoUrl('thumb.normal'); ?>" class="thumb_normal" />
	          </a>
            <?php //endif; ?> 
          </div> 
          <?php if($this->displayStoreName):?>
            <div class='sitestorealbum_thumbs_details'>	
               <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $sitestorephoto->store_id, $layout);?>
               <?php $parent = Engine_Api::_()->getItem('sitestore_album', $sitestorephoto->album_id);?>
								<?php echo $this->translate('in ').
								$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorephoto->store_id)), $sitestorephoto->store_title,array('title' => $sitestorephoto->store_title)) ?>
            </div>         
          <?php endif;?> 
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitestorephoto->owner_id)) :?>
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?> 
                <div class='sitestorealbum_thumbs_details'>	
                <?php $userItem = Engine_Api::_()->getItem('user', $sitestorephoto->owner_id);	 ?>         	
                <?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?>                   </div>
              <?php endif;?>
            <?php endif;?>
          <?php endif;?>
	        <?php if($this->show_detail != 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitestorealbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitestorephoto->comment_count), $this->locale()->toNumber($sitestorephoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like'):?>
		          <div class='sitestorealbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitestorephoto->like_count), $this->locale()->toNumber($sitestorephoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php endif; ?> 
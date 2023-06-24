<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<ul class="seaocore_sidebar_list">
	<?php foreach ($this->paginator as $sitestoreproduct_video): ?>
    <?php $this->partial()->setObjectKey('sitestoreproduct_video');
        echo $this->partial('application/modules/Sitestoreproduct/views/scripts/partialWidget.tpl', $sitestoreproduct_video);
    ?>		  
  
    <?php
      $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.title.truncation', 18);
      $tmpBody = strip_tags($sitestoreproduct_video->getParent()->getTitle());
      $product_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
    ?>
  
    <?php echo $this->translate("in ") . $this->htmlLink($sitestoreproduct_video->getParent()->getHref(), $product_title, array('title' => $sitestoreproduct_video->getParent()->getTitle())) ?>    
    </div>
    <div class="seaocore_sidebar_list_details clr"> 
      <?php echo $this->translate(array('%s like', '%s likes', $sitestoreproduct_video->like_count), $this->locale()->toNumber($sitestoreproduct_video->like_count)) ?>,
      <?php echo $this->translate(array('%s view', '%s views', $sitestoreproduct_video->view_count), $this->locale()->toNumber($sitestoreproduct_video->view_count)) ?>
    </div>
    </div>
		</li>
	<?php endforeach; ?>
</ul>
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

<div class="sr_sitestoreproduct_social_share_wrapper sr_sitestoreproduct_side_widget">
	<?php if(!empty($this->optionsArray)): ?>
	  <div class="sr_sitestoreproduct_social_share">
	    <?php if ($this->viewer_id && in_array("siteShare", $this->optionsArray)):?>
	      <?php echo $this->htmlLink(array('module'=> 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $this->subject->getType(), 'id' => $this->subject->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), '', array('class' => 'smoothbox sitestoreproduct_share_icon_link seaocore_icon_share', 'title' => $this->translate('Share'))); ?>
	    <?php endif; ?>
	    
	    <?php if($this->subject->getType() == 'sitestoreproduct_wishlist' && in_array("friend", $this->optionsArray)): ?>
	      <?php echo $this->htmlLink(array('action' => 'tell-a-friend', 'route' => 'sitestoreproduct_wishlist_general', 'type' => $this->subject->getType(), 'wishlist_id' => $this->subject->getIdentity()), '', array('target' => '_blank', 'class' => 'smoothbox sitestoreproduct_share_icon_link icon_sitestoreproducts_tellafriend', 'title' => $this->translate('Tell a Friend'))); ?>    
	    <?php elseif($this->subject->getType() == 'sitestoreproduct_product' && in_array("friend", $this->optionsArray)): ?>
	      <?php echo $this->htmlLink(array('action' => 'tellafriend', 'route' => 'sitestoreproduct_specific', 'type' => $this->subject->getType(), 'product_id' => $this->subject->getIdentity()), '', array('target' => '_blank', 'class' => 'smoothbox sitestoreproduct_share_icon_link icon_sitestoreproducts_tellafriend', 'title' => $this->translate('Tell a Friend'))); ?>    
	    <?php endif; ?>  
	
	    <?php if($this->subject->getType() == 'sitestoreproduct_wishlist' && in_array("print", $this->optionsArray)): ?>
	      <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sitestoreproduct_wishlist_general', 'type' => $this->subject->getType(), 'wishlist_id' => $this->subject->getIdentity(), 'content_id' => $this->content_id), '', array('target' => '_blank', 'class' => 'sitestoreproduct_share_icon_link icon_sitestoreproducts_printer', 'title' => $this->translate('Print'))); ?>    
	    <?php elseif($this->subject->getType() == 'sitestoreproduct_product' && in_array("print", $this->optionsArray)): ?>
	      <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sitestoreproduct_specific', 'type' => $this->subject->getType(), 'product_id' => $this->subject->getIdentity()), '', array('target' => '_blank', 'class' => 'sitestoreproduct_share_icon_link icon_sitestoreproducts_printer', 'title' => $this->translate('Print'))); ?>    
	    <?php endif; ?>
	
	    <?php if ($this->viewer_id && in_array("report", $this->optionsArray)):?>
	      <?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->subject->getGuid()), '', array('class' => 'smoothbox sitestoreproduct_share_icon_link seaocore_icon_report', 'title' => $this->translate('Report'))); ?>
	    <?php endif; ?>
	    
	  </div>
	<?php endif; ?>
	
	<?php if (in_array("socialShare", $this->optionsArray)):?>
	  <div class="sr_sitestoreproduct_social_share">
	    <?php echo $this->code; ?>
	  </div>
	<?php endif; ?>
</div> 

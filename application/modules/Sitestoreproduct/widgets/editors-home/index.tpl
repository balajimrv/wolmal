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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>
<ul class="sr_sitestoreproduct_editor_product">
  <?php foreach( $this->editors as $editor ):?>
    <li>
    	<div class="sr_sitestoreproduct_editor_product_photo">
        <?php echo $this->htmlLink($editor->getHref(), $this->itemPhoto($editor, 'thumb.profile'), array('class' => 'editors_thumb')) ?>
      </div>
      <div class='sr_sitestoreproduct_editor_product_info'>
        <div class='sr_sitestoreproduct_editor_product_name'>
          <?php echo $this->htmlLink($editor->getHref(), $editor->getUserTitle($editor->user_id)) ?>
        </div>
                
				<?php if(!empty($editor->designation)): ?>
					<div class="sr_sitestoreproduct_editor_product_stat"><?php echo $editor->designation;?></div>
				<?php endif; ?>

        <?php 
				$params = array();
				$params['type'] = 'editor';
        $params['owner_id'] = $editor->user_id;
        ?> 
        <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->totalReviews($params); ?>
        <div class="sr_sitestoreproduct_editor_product_stat seaocore_txt_light"> 
          <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews));?>
        </div>          
          
        <?php if(!$editor->isSelf($this->viewer()) && $editor->getUserEmail($editor->user_id)): ?>
          <div class="sr_sitestoreproduct_editor_product_stat"><b><?php echo $this->htmlLink(array('route' => "sitestoreproduct_editor_general", 'action' => 'editor-mail', 'user_id' => $editor->user_id), $this->translate('Email %s',$editor->getUserTitle($editor->user_id)),  array('class'=>'smoothbox')) ?></b></div>
        <?php endif; ?>
          
				<div class="sr_sitestoreproduct_editor_product_stat"><b><?php echo $this->htmlLink($editor->getHref(), $this->translate('View Profile &raquo;')) ?></b></div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>

<div class="st_p_p_top_list_wrapper o_hidden">
  <?php foreach ($this->paginator as $sitestoreproduct): ?>
    <div class="st_p_p_top_list">
      <div class="st_p_p_top_list_thumb br_body_bg">
      <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal'), array('title' => $sitestoreproduct->getTitle())); ?>
      </div>
      
      <?php if (!empty($this->productTitle)) : ?>
        <div class="clr mtop5 fleft f_small">
          <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle(), array('title' => $sitestoreproduct->getTitle())); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!empty($this->linkSee)) : ?>
    <div class="st_p_p_top_list st_p_p_top_list_more">
      <div class="st_p_p_top_list_thumb br_body_bg ">
        <?php echo $this->htmlLink($this->storeObj->getHref(), "<b>" .  $this->totalCount . "</b></br>" . $this->translate("View Store")); ?>
      </div>
    </div>
  <?php endif; ?>
</div>


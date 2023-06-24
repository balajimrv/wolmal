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

<ul class="sr_sitestoreproduct_profile_side_product sr_sitestoreproduct_side_widget">
  <li>
    <?php echo $this->htmlLink($this->editor->getHref(), $this->itemPhoto($this->user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $this->user->getTitle()), array('title' => $this->user->getTitle())) ?>
    <div class='sr_sitestoreproduct_profile_side_product_info'>
      <div class='sr_sitestoreproduct_profile_side_product_title'>
        <?php echo $this->translate("By ").$this->htmlLink($this->editor->getHref(), $this->user->getTitle(), array('title' =>  $this->user->username)) ?>
      </div>
      <div class='sr_sitestoreproduct_profile_side_product_stats'>
        <?php echo $this->viewMore($this->editor->details, 64); ?>
      </div>
    </div>
  </li>
</ul>



<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
  <?php foreach( $this->paginator as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $user->getTitle()), array('title' => $user->getTitle())) ?>
      <div class='sitestore_sidebar_list_info'>
        <div class='sitestore_sidebar_list_title'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' =>  $user->getTitle())) ?>
        </div>
        <div class='sitestore_sidebar_list_details'>
          <?php echo $this->translate(array('%s review', '%s reviews', $user->review_count),$this->locale()->toNumber($user->review_count)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
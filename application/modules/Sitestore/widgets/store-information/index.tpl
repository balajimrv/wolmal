<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php
$link = $this->url(array("action" => "my-stores"), "sitestore_claimstores", true);
?>
<ul class="sitestore_sidebar_list">
  <?php foreach ($this->stores as $store) { ?>
    <li>
      <?php
        echo $this->htmlLink($store->getHref(), $this->itemPhoto($store, 'thumb.icon'));
      ?>

      <div class='sitestore_sidebar_list_info'>
        <div class='sitestore_sidebar_list_title'>    
  <?php echo $this->htmlLink($store->getHref(), $store->getTitle()); ?>
        </div>

          <?php if (in_array("sales", $this->showContent)) { ?>
          <div class="sitestore_sidebar_list_details">
            <?php
            if (empty($store->order_count))
              echo $this->translate("No Sale", $store->order_count);
            else {
              echo $this->translate(array('%s sale', '%s sales', $store->order_count), $this->locale()->toNumber($store->order_count));
            }
            ?>
          </div>
        <?php } ?>

        <?php if (in_array("rating", $this->showContent)) { ?>
          <?php
          if (($store->rating > 0)):
            $currentRatingValue = $store->rating;
            $difference = $currentRatingValue - (int) $currentRatingValue;
            if ($difference < .5) {
              $finalRatingValue = (int) $currentRatingValue;
            } else {
              $finalRatingValue = (int) $currentRatingValue + .5;
            }
            ?>
            <div class="sitestore_sidebar_list_details">
              <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                <?php for ($x = 1; $x <= $store->rating; $x++): ?>
                  <span class="rating_star_generic rating_star"></span>
      <?php endfor; ?>
            <?php if ((round($store->rating) - $store->rating) > 0): ?>
                  <span class="rating_star_generic rating_star_half"></span>
            <?php endif; ?>
              </span>		
            </div>
          <?php endif;
        } ?>

        <?php
        if (in_array("review", $this->showContent)) {
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
            echo '<div class="sitestore_sidebar_list_details">' . $this->translate(array('%s review', '%s reviews', $store->review_count), $this->locale()->toNumber($store->review_count)) . '</div>';
          }
        }
        ?>
      </div>
      
      <div class="sitestore_sidebar_list_btns clr">
        
    <!--Start Follow Button Work-->
    <?php if (in_array("follow", $this->showContent) && !empty($store) && !empty($this->viewer_id) && ($store->owner_id != $this->viewer_id)) : ?>
      <div>
    <?php include APPLICATION_PATH . '/application/modules/Sitestore/widgets/store-information/follow.tpl'; ?>   
    <?php // echo $this->content()->renderWidget("seaocore.seaocore-follow");  ?>
      </div>	
    <?php endif; ?>
    <!--End Follow Button Work-->
    
        <!--Start Like Button Work-->
        <?php if (in_array("like", $this->showContent)) : ?>
          <div>
            <?php include APPLICATION_PATH . '/application/modules/Sitestore/widgets/store-information/like.tpl'; ?>   
          </div>	
        <?php endif; ?>
        <!--End Like Button Work-->

    <!--Start Contact Button Work-->
    <?php
    if (in_array("contact", $this->showContent)) :
      $showMessageOwner = Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'messages', 'auth');
      if (!empty($store) && !empty($this->viewer_id) && ($store->owner_id != $this->viewer_id) && ($showMessageOwner != 'none')):
        $contactUrl = $this->url(array('action' => 'message-owner', 'store_id' => $store->getIdentity()), 'sitestore_profilestore', false);
        ?>
        <div style="display:inline-block" class="seaocore_button">
          <a href="javascript:void(0);" onClick="Smoothbox.open('<?php echo $contactUrl; ?>')">
            <span>Contact</span>
          </a>
        </div>
    <?php endif;
  endif; ?>
    <!--End Contact Button Work-->
    </div>  
    </li>
  <?php } ?>
</ul>

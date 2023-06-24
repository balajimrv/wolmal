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
<?php foreach( $this->posters as $user ): ?>
  <li>
    <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $user->getTitle()), array('title' => $user->getTitle())) ?>      
    <div class='seaocore_sidebar_list_info'>
      <div class='seaocore_sidebar_list_title'>
        <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' =>  $user->getTitle())) ?>
      </div>
      <div class="seaocore_sidebar_list_details">
       <?php if( $this->popularity == 'top_buyer' ):
               if( $this->listing_based_on == 'price' ):
                 echo $this->translate('Total Purchasing %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($user->grand_total));
               else:
                 echo $this->translate('Total Item Purchased %s', $this->locale()->toNumber($user->item_count));
               endif;
             elseif( $this->popularity == 'top_poster' ):
               echo $this->translate(array('%s product entry', '%s product entries', $user->product_count),$this->locale()->toNumber($user->product_count));
             endif;?>
      </div>
    </div>
  </li>
<?php endforeach; ?>
</ul>
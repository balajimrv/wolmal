 <?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php  $id = $this->id; ?>
<?php if(!empty ($this->showViewMore)): ?>
	<script type="text/javascript">
			en4.core.runonce.add(function() {
			hideViewMoreLinkSitestoreOffer();  
			});
			function getNextStoreSitestoreoffer(){
				return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
			}
			function hideViewMoreLinkSitestoreOffer(){
				if($('sitestore_offer_tabs_view_more'))
					$('sitestore_offer_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
			}
			
			function viewMoreTabOffers()
			{
			$('sitestore_offer_tabs_view_more').style.display ='none';
			$('sitestore_offers_tabs_loding_image').style.display ='';
			en4.core.request.send(new Request.HTML({
				method : 'post',
				'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreoffer/name/sitestore-dateoffer',
				'data' : {
					format : 'html', 
					isajax : 2,
					tab_show : '<?php echo $this->active_tab ?>',
          category_id : '<?php echo $this->category_id ?>',
          itemCount : '<?php  echo $this->totaloffers?>',
					store: getNextStoreSitestoreoffer()
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
				$('hideResponse_div').innerHTML=responseHTML;      
					var offercontainer = $('hideResponse_div').innerHTML;
					$('sitestoreoffer_global_content').innerHTML = responseHTML;
					$('sitestore_offers_tabs_loding_image').style.display ='none';
					$('hideResponse_div').innerHTML="";  
					$('sitestore_offers_tabs_loding_image').style.display ='none';
				  $('hideResponse_div').innerHTML="";
				}
			}));

			return false;

		}  
	</script>
<?php endif; ?>

<script type="text/javascript">

	var show_duration_offers = function (module_tab_id, module_active_tab, content_html_id, module_name) {
    if($('sitestore_offer_tabs_view_more'))
    $('sitestore_offer_tabs_view_more').style.display =  'none';
		if (module_active_tab == 1) {
			$('sitestoreoffer_offers_tab' + '2').erase('class');
			$('sitestoreoffer_offers_tab' + '3').erase('class');
			$('sitestoreoffer_offers_tab' + '1').set('class', 'active');
					
		}
		else if (module_active_tab == 2) {
			$('sitestoreoffer_offers_tab' + '1').erase('class');
			$('sitestoreoffer_offers_tab' + '3').erase('class');
			$('sitestoreoffer_offers_tab' + '2').set('class', 'active');
					
		}
					
		else if(module_active_tab == 3) {
			$('sitestoreoffer_offers_tab' + '1').erase('class');
			$('sitestoreoffer_offers_tab' + '2').erase('class');
			$('sitestoreoffer_offers_tab' + '3').set('class', 'active');
					
		}
		if($(content_html_id) != null) {
			$(content_html_id).innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" /></center>';
		}
		
		var request = new Request.HTML({
			'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreoffer/name/sitestore-dateoffer',
			'data' : {
				'format' : 'html',
				'isajax' : 1,
        'category_id' : '<?php echo $this->category_id ?>',
				'tab_show' : module_active_tab,
        'itemCount' : '<?php  echo $this->totaloffers?>',
			// 'table' : table_name
        'statistics': '<?php echo json_encode($this->statistics); ?>',
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$(content_html_id).innerHTML = responseHTML;
				<?php if(!empty ($this->showViewMore)): ?>
								hideViewMoreLinkSitestoreOffer();
				<?php endif; ?> 
			}
		});

		request.send();
	}
</script>

<?php if (empty($this->ajaxrequest)) : ?>
<ul id="sitestore_offer_dyanamic_code" class="layout_seaocore_sidebar_tabbed_widget">
  <li>
    <div class="seaocore_tabs_alt">
      <ul>
				<?php if ($this->active_tab == 1) {  ?>
					<li class = 'active' id = 'sitestoreoffer_offers_tab1' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 1, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php } else { ?>
					<li class = '' id = 'sitestoreoffer_offers_tab1' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 1, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php }?>
				<?php
						//PRINT FOR LINK
						echo "<a href='javascript:void(0);'>".$this->translate('This Week')."</a>";
				?>
					</li>
				
					<?php if ($this->active_tab == 2) {  ?>
					<li class = 'active' id = 'sitestoreoffer_offers_tab2' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 2, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php } else { ?>
					<li class = '' id = 'sitestoreoffer_offers_tab2' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 2, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php }?>
				<?php
				//PRINT FOR LINK
				echo "<a href='javascript:void(0);'>".$this->translate('This Month')."</a>";
				?>
				</li>
					<?php  if ($this->active_tab == 3) { ?>
					<li class = 'active' id = 'sitestoreoffer_offers_tab3' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 3, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php } else {  ?>
					<li class = '' id = 'sitestoreoffer_offers_tab3' onclick="javascript:show_duration_offers('sitestoreoffer_offers_tab' , 3, 'sitestoreoffer_global_content', 'sitestoreoffer');">
				<?php }?>
				<?php
						//ECHO FOR LINK
				echo "<a href='javascript:void(0);'>".$this->translate('Overall')."</a>";

				?>
					</li>
      </ul>
    </div>
  </li>
  <li id="hideResponse_div" style="display: none;"></li>
  <li id="sitestoreoffer_global_content">
    <ul>
<?php endif;?>
  <?php $counter = 1;?>
  <?php if( count($this->paginator) > 0  ) {  ?>
   <?php foreach ($this->paginator as $sitestore): ?>
    <li class="seaocore_sidebar_listing">
    	<div class="seaocore_thumb">
      <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);?>
			<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestore->store_id, $layout);?>
      <?php if(!empty($sitestore->photo_id)):?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $this->itemPhoto($sitestore, 'thumb.icon'),array('title' => $sitestore->getTitle())) ?>
			<?php else:?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), "<img class='thumb_icon'src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/offer_thumb.png' alt='' />",array('title' => $sitestore->getTitle())) ?>
      <?php endif;?>
			</div>
      <div class='seaocore_info'>
				<div class='seaocore_title sitestoreoffer_show_tooltip_wrapper'>
					<?php echo $item_title = $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $sitestore->title); ?>
					<?php
					$truncation_limit_desc = 500;
					$tmpBody = strip_tags($sitestore->description);
					$item_description = ( Engine_String::strlen($tmpBody) > $truncation_limit_desc ? Engine_String::substr($tmpBody, 0, $truncation_limit_desc) . '..' : $tmpBody );
					?>
					<div class="sitestoreoffer_show_tooltip">
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/tooltip_arrow.png" alt="" class="arrow" />
						<?php echo $sitestore->description; ?>
					</div>
				</div>

        <div class='seaocore_stats' style="margin:3px 0;">
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.truncation.limit', 13);
          $tmpBody = strip_tags($sitestore->sitestore_title);
          $item_sitestore_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php $item = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $item->getSlug()), $item_sitestore_title, array('title' => $sitestore->sitestore_title)) ?>
        </div>
       
				<div class="sitestore_offer_date clr">  
          <?php $today = date("Y-m-d H:i:s"); ?>
        	<?php if(in_array('expire', $this->statistics) && !empty($sitestore->end_settings) && $sitestore->end_time < $today):?>
                <div class="sitestore_offer_date">
                  <span><b><?php echo $this->translate('Expired');?></b></span>
                </div>
              <?php //endif;?>
          
          <?php elseif(in_array('claim', $this->statistics)):?>
          
           <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left fright">' .$sitestore->claimed.' '.$this->translate('Used') . '</span>'; ?>
            <?php if($sitestore->claim_count != -1):?>
            <?php $sitestore->claim_count  = $sitestore->claim_count - $sitestore->claimed ;?>
              <div class="sitestore_offer_date">
                <span>
                  <?php //echo $this->translate(array('<b>%1$s</b> Left', '<b>%1$s</b> Left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                  
                  <?php if($sitestore->claim_count == 1) : ?>
                  <?php echo $this->translate(array('<b>%1$s</b> coupon left', '<b>%1$s</b> coupon left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                 <?php else : ?>
                  <?php echo $this->translate(array('<b>%1$s</b> coupons left', '<b>%1$s</b> coupons left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                 <?php endif;?>
                </span>
              </div>
            <?php else : ?>
              <div class="sitestore_offer_date">
                <span><?php echo $this->translate('Unlimited Use') ?></span>
              </div>
            <?php endif;?>
          <?php endif;?>
          
        	<?php if(in_array('startdate', $this->statistics)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('Start date') . ':'; ?></span>
              <span><?php echo $this->timestamp(strtotime($sitestore->start_time)) ?></span>
            </div>
          <?php endif;?>
          
       		<?php if(in_array('enddate', $this->statistics)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('End date'). ":"; ?></span>
              <?php if($sitestore->end_settings == 1):?>
                <span><?php echo $this->timestamp(strtotime($sitestore->end_time)) ?></span>
              <?php else:?>
                <span><?php echo $this->translate('Never Expires');?></span>
              <?php endif;?>
            </div>
          <?php endif;?>
          
          <?php if(in_array('minpurchase', $this->statistics) && !empty($sitestore->minimum_purchase)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('Minimum Purchase:');?></span>
              <span><?php echo $sitestore->minimum_purchase;?></span>
            </div>
          <?php endif;?>
          
          <?php if(in_array('couponurl', $this->statistics)):?>
              <?php if(!empty($sitestore->enable_url) && !empty($sitestore->url)):?>
              <div class="sitestore_offer_date">
                <span><?php echo $this->translate('URL') . ":";?></span>
                <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?></a></span>
              </div>
              <?php endif;?>
            <?php endif;?>
        </div>      
          <div class="sitestore_offer_stats fleft">
            <?php if(in_array('couponcode', $this->statistics)):?>
              <span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
                <span class="sitestorecoupon_tip">
                  <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                  <i></i>
                </span>
                <input type="text" value="<?php echo $sitestore->coupon_code;?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
              </span>
            <?php endif;?>
              
            <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper mtop5">
              <?php if(in_array('discount', $this->statistics)):?>
                <span class="sitestorecoupon_tip">
                  <span><?php echo $this->translate('Coupon Discount Value');?></span>
                  <i></i>
                </span>
                <?php if(!empty($sitestore->discount_type)):
                  $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($sitestore->discount_amount);?>
                  <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                <?php else:?>
                  <span class="discount_value"><?php echo $sitestore->discount_amount . '%';?></span>&nbsp;&nbsp;
                <?php endif;?>
              <?php endif;?>
            </span>
          </div>
	    </div>  
    </li>
  <?php endforeach; ?>
  
  <?php }
  else
  { ?>
   <li><div class="tip"><span><?php echo $this->translate(' No entry could be found.') ?></span></div></li>
  <?php
  }
?>

<?php if (empty($this->ajaxrequest)) :?>
	</ul>
 </li>
<?php if (!empty($this->showViewMore)): ?>
<li class="seaocore_more">
	<div class="seaocore_sidebar_more_link" id="sitestore_offer_tabs_view_more" >
		<a href="javascript:void(0);" onclick="viewMoreTabOffers()" id="feed_viewmore_link"><?php echo $this->translate('See More');?> &raquo;</a>
		<?php
//		echo $this->htmlLink('javascript:void(0);', $this->translate('See More'), array(
//				'id' => 'feed_viewmore_link'
//		))
		?>
	</div>
	<div class="seaocore_sidebar_more_link" id="sitestore_offers_tabs_loding_image" style="display: none;">
		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" class="seaocore_sidebar_loader_img" />
	</div>
	</li>
<?php endif; ?>
</ul>
<?php endif;?>
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>
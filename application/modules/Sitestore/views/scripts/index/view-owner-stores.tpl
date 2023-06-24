<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
<!--  <div class="heading">
    <?php //echo $this->translate("%s Stores", $this->ownerObj->getTitle()); ?>
  </div>-->

<div class='seaocore_members_popup_content' style="height: 330px;">
    <?php
    
    
    if ($this->paginator->getTotalItemCount() > 0):
      ?>
      <ul class="seaocore_browse_list">
         foreach ($this->paginator as $item): ?>
          <li <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
        if ($item->featured): ?>class="lists_highlight"<?php endif;
    endif; ?> >
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
                if ($item->featured): ?>
                <span title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured')?></span>
              <?php endif;
            endif; ?>
            <div class='seaocore_browse_list_photo'>
              <?php
              echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'left')), array('target' => '_parent'));
              if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
                if (!empty($item->sponsored)):
                  $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
                  if (!empty($sponsored)) :
                    ?>
                    <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                      <?php echo $this->translate('SPONSORED'); ?>                 
                    </div>
                    <?php
                  endif;
                endif;
              endif;
              ?>  
            </div>
            <?php
            if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            $this->partial()->setObjectKey('sitestore');
            // echo $this->partial('partial_views.tpl', $item);
            ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<?php // $item = $this->sitestore; ?>
<?php $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview'); ?>
<div class='seaocore_browse_list_info'>
	<div class='seaocore_browse_list_info_title'>
		<span >
			<?php if( $item->declined==1 ): ?>
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/declined.gif', '', array('target' => '_parent', 'class' => 'icon', 'title' => $this->translate('Declined'))) ?>
			<?php endif;?>
			<?php if ($item->closed): ?>
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('target' => '_parent', 'class' => 'icon', 'title' => $this->translate('Closed'))) ?>
			<?php endif; ?>
			<?php if (empty($item->approved)&& empty ($item->declined)): ?>
						<?php  $approvedtitle='Not approved';  if(empty($item->aprrove_date)): $approvedtitle="Approval Pending"; endif;?>
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_approved0.gif', '', array('target' => '_parent', 'class' => 'icon', 'title' => $this->translate($approvedtitle))) ?>
			<?php endif; ?>
      <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
        <?php if (!empty($item->sponsored)): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('target' => '_parent', 'class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
        <?php endif; ?>
        <?php if (!empty($item->featured)): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('target' => '_parent', 'class' => 'icon', 'title' => $this->translate('Featured'))) ?>
        <?php endif; ?>
      <?php endif; ?>
		</span>
		<h3>
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $item->getTitle(), array('target' => '_parent')) ?>
		</h3>
	</div>
	<?php if ($sitestorereviewEnabled): ?>
		<?php if (($item->rating > 0)): ?>

			<?php
				$currentRatingValue = $item->rating;
				$difference = $currentRatingValue- (int)$currentRatingValue;
				if($difference < .5) {
					$finalRatingValue = (int)$currentRatingValue;
				}
				else {
					$finalRatingValue = (int)$currentRatingValue + .5;
				}
			?>

			<div class='seaocore_browse_list_info_date'>
				<span class="sitestore_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
					<span class="clr">
						<?php for ($x = 1; $x <= $item->rating; $x++): ?>
						<span class="rating_star_generic rating_star" ></span>
						<?php endfor; ?>
						<?php if ((round($item->rating) - $item->rating) > 0): ?>
						<span class="rating_star_generic rating_star_half" ></span>
						<?php endif; ?>
					</span>
				</span>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class='seaocore_browse_list_info_date'>
    
		  <?php echo $this->timestamp(strtotime($item->creation_date)); ?><?php if(!$postedBy):?>,<?php endif;?>
      <?php if($postedBy):?>
        - <?php echo $this->translate('posted by'); ?>
		    <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_parent')) ?>,
      <?php endif;?>
      <?php $productCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($item->store_id)?>
      <?php echo $this->translate(array('%s product', '%s products', $productCount), $this->locale()->toNumber($productCount)) ?>,
		<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>,
<!--		<?php // echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
      <?php // echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,-->
		<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>

	</div>

			<?php if((!empty($item->location) && $this->enableLocation) || (!empty($item->price) && $this->enablePrice) ): ?>
		<div class="seaocore_browse_list_info_date">
			<?php if(!empty($item->price) && $this->enablePrice): ?>
			<?php  echo $this->translate("Price: "); echo Engine_Api::_()->sitestore()->getPriceWithCurrency($item->price);?><?php endif; ?><?php if((!empty($item->location) && $this->enableLocation) && (!empty($item->price ) && $this->enablePrice)): ?><?php  echo $this->translate(", "); ?><?php endif; ?><?php if(!empty($item->location) && $this->enableLocation): ?>
			<?php  echo $this->translate("Location: "); echo $this->translate($item->location); ?><?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if(Engine_Api::_()->sitestore()->hasPackageEnable()):?>
		<div class='seaocore_browse_list_info_date clr'>
		<?php echo $this->translate('Package: ') ?>           
			<a target="_parent" href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitestore_packages', true) ?>' title="<?php echo $this->translate(ucfirst($item->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($item->getPackage()->title)); ?>
			</a>
		</div>
	<?php endif; ?>
	<div class='seaocore_browse_list_info_date'>
		<?php if(Engine_Api::_()->sitestore()->hasPackageEnable()):?>
			<?php if(!$item->getPackage()->isFree()):  ?>
				<span>
					<?php echo $this->translate('Payment: ')?>
					<?php if($item->status=="initial"):
							echo $this->translate("Not made");
					elseif($item->status=="active"):
								echo $this->translate("Yes");
							else:
									echo $this->translate(ucfirst($item->status));
								endif;
									?>
				</span>
				<?php if(!empty($item->aprrove_date)): ?>
					|
					<?php endif; ?>
			<?php endif; ?>
		<?php endif;?>
		<?php if(!empty($item->aprrove_date)): ?>

			<span style="color: chocolate;"><?php echo $this->translate('First Approved on '). $this->timestamp(strtotime($item->aprrove_date)) ?></span>
			<?php if(Engine_Api::_()->sitestore()->hasPackageEnable()):?>
				|
				<span style="color: green;">
					<?php $expiry=Engine_Api::_()->sitestore()->getExpiryDate($item);
					if($expiry !=="Expired" && $expiry !== $this->translate('Never Expires'))
						echo $this->translate("Expiration Date: ");

					echo $expiry;
					?>
				</span>
			<?php endif;?>
	<?php endif ?>
	</div>
	<div class='seaocore_browse_list_info_blurb'>
		        
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
            <?php
            echo $this->viewMore($item->body, 200, 5000)
            ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="tip">
        <span> <?php
echo $this->translate('You do not have any stores yet.');
      ?>
        </span>
      </div>
    <?php endif;
    // echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestore")); ?>
  </div>
</div>
<div class="seaocore_members_popup_bottom">
  <button type='button' onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close"); ?></button>
</div>
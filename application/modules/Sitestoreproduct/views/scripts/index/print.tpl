<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/style_print.css');
?>

<div class="seaocore_print_page">
	<div class="seaocore_print_title">
		<span class="right">
			<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
		</span>
		<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $this->sitestoreproduct->closed != 1): ?>
			<span class="left">
				<?php echo $this->translate($this->sitestoreproduct->getTitle()) ?>
			</span>
		<?php endif; ?>	
	</div>
	<div class='seaocore_print_profile_fields'>
		<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $this->sitestoreproduct->closed == 1): ?>
			<div class="tip"> 
				<span> <?php echo $this->translate('This product has been closed by the owner.'); ?> </span>
			</div>
			<br/>
		<?php else: ?>
      <div class="seaocore_print_photo">
      	<?php echo $this->itemPhoto($this->sitestoreproduct, 'thumb.profile', '' , array('align' => 'left')); ?>
      	<div id="printdiv" class="seaocore_print_button">
					<a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl?>application/modules/Sitestoreproduct/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
				</div>
      </div>
      <div class="seaocore_print_details">	      
				<h4>
					<?php echo $this->translate("Product Information") ?>
				</h4>

				<ul>
					<li>
            <span><?php echo $this->translate('Created By'); ?></span>
						<span><?php echo $this->translate($this->sitestoreproduct->getParent()->getTitle()) ?></span>
					</li>
					<li>
            <span><?php echo $this->translate('Created On : '); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestoreproduct->creation_date))) ?></span>
					</li>
					<?php if(!empty($this->sitestoreproduct->comment_count)): ?>
						<li>
							<span><?php echo $this->translate('Comments :'); ?></span>
							<span><?php echo $this->translate( $this->sitestoreproduct->comment_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->sitestoreproduct->view_count)): ?>
						<li>
							<span><?php echo $this->translate('Views :'); ?></span>
							<span><?php echo $this->translate( $this->sitestoreproduct->view_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->sitestoreproduct->like_count)): ?>
						<li>
							<span><?php echo $this->translate('Likes :'); ?></span>
							<span><?php echo $this->translate( $this->sitestoreproduct->like_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->sitestoreproduct->review_count) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)): ?>
						<li>
							<span><?php echo $this->translate('Reviews :'); ?></span>
							<span><?php echo $this->translate( $this->sitestoreproduct->review_count) ?></span>
						</li>
					<?php endif; ?>            
					<?php if ($this->category_name): ?>
						<li>
							<span><?php echo $this->translate('Category :'); ?></span> 
							<span>
                <?php echo $this->translate($this->category_name) ?>
								<?php if ($this->subcategory_name): ?> &raquo;
									<?php echo $this->translate($this->subcategory_name) ?>
                
                  <?php if ($this->subsubcategory_name): ?> &raquo;
                    <?php echo $this->translate($this->subsubcategory_name) ?>
                  <?php endif; ?>
                
								<?php endif; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ($this->sitestoreproductTags): $tagCount=0;?>
					 <li>
						<span><?php echo $this->translate('Tag :'); ?></span>
							<span>
								<?php foreach ($this->sitestoreproductTags as $tag): ?>
									<?php if (!empty($tag->getTag()->text)):?>
										<?php if(empty($tagCount)):?>
											<?php echo "#". $tag->getTag()->text?>
											<?php "#".$tagCount++; ?>
										<?php else: ?>
											<?php echo $tag->getTag()->text?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<li>
						<span><?php echo $this->translate('Description :'); ?></span>
						<span><?php echo $this->translate(''); ?> <?php echo $this->sitestoreproduct->body ?></span>
					</li>
					
				</ul>
        <?php if($this->sitestoreproduct->profile_type): ?>
          <?php $str = $this->fieldValueLoop($this->sitestoreproduct, $this->fieldStructure); ?>
          <?php if(!empty($str) ): ?>
            <h4>
              <?php echo $this->translate('Profile Information') ?>
            </h4>
            <?php echo Engine_Api::_()->sitestoreproduct()->removeMapLink($this->fieldValueLoop($this->sitestoreproduct, $this->fieldStructure)) ?>					
          <?php endif; ?>
        <?php endif; ?>
			</div>	
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
 function printData() {
		document.getElementById('printdiv').style.display = "none";
		window.print();
		setTimeout(function() {
					document.getElementById('printdiv').style.display = "block";
		}, 500);
	}
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
			->prependStylesheet($baseUrl.'application/modules/Seaocore/externals/styles/style_print.css');
?>
<link href="<?php echo $baseUrl . 'application/modules/Seaocore/externals/styles/style_print.css'?>" type="text/css" rel="stylesheet" media="print">
<?php
  $contactPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($this->sitestore, 'contact');
?>
<div class="seaocore_print_store">
	<div class="seaocore_print_title">	
    <span class="left">
      <?php echo $this->sitestore->getTitle(); ?>
    </span>
		<span class="right">
			<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
		</span>
	</div>
	<div class='seaocore_print_profile_fields'>
		<?php if ($this->sitestore->closed == 1): ?>
			<div class="tip"> 
				<span> <?php echo $this->translate('This store has been closed by the poster.'); ?> </span>
			</div>
			<br/>
		<?php else: ?>
			<div class="seaocore_print_photo">
				<?php echo $this->itemPhoto($this->sitestore, 'thumb.normal', '' , array('align'=>'left')); ?>
				<div id="printdiv" class="seaocore_print_button">
					<a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
				</div>
			</div>
			<div class="seaocore_print_details">	      
				<h4>
					<?php echo $this->translate('Store Information') ?>
				</h4>
				<ul>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?>
            <li>
              <span><?php echo $this->translate('Posted By:'); ?> </span>
              <span><?php echo $this->translate($this->sitestore->getParent()->getTitle()) ?></span>
            </li>
          <?php endif;?>
					<li>
						<span><?php echo $this->translate('Posted On:'); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->creation_date))) ?></span>
					</li>
					<?php if(!empty($this->sitestore->comment_count)): ?>
					<li>
						<span><?php echo $this->translate('Comments:'); ?></span>
						<span><?php echo $this->translate( $this->sitestore->comment_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitestore->view_count)): ?>
					<li>
						<span><?php echo $this->translate('Views:'); ?></span>
						<span><?php echo $this->translate( $this->sitestore->view_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitestore->like_count)): ?>
					<li>
						<span><?php echo $this->translate('Likes:'); ?></span>
						<span><?php echo $this->translate( $this->sitestore->like_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if ($this->category): ?>
					<li>
						<span><?php echo $this->translate('Category:'); ?></span> 
						<span><?php echo $this->translate($this->category->category_name) ?>
						<?php if ($this->subcategory): ?> &raquo;
						<?php echo $this->translate($this->subcategory->category_name) ?>
            <?php endif; ?>
            <?php if ($this->subsubcategory): ?> &raquo;
            <?php echo $this->translate($this->subsubcategory->category_name); ?>
						<?php endif; ?>
						</span>
					</li>
					<?php endif; ?>
					<?php if ($this->sitestoreTags): $tagCount=0;?>
						<li>
							<span><?php echo $this->translate('Tag:'); ?></span>
								<span>
								<?php foreach ($this->sitestoreTags as $tag): ?>
								<?php if (!empty($tag->getTag()->text)):?>
								<?php if(empty($tagCount)):?>
								<?php echo "#". $tag->getTag()->text?>
								<?php "#".$tagCount++; else: ?>
								<?php echo $tag->getTag()->text?>
								<?php endif; ?>
								<?php endif; ?>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<li>
						<span><?php echo $this->translate('Description:'); ?></span>
						<span><?php echo $this->translate(''); ?> <?php echo $this->sitestore->body ?></span>
					</li>
           <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
           <?php if($this->sitestore->price && $enablePrice):?>
          <li>
            <span><?php echo $this->translate('Price:'); ?></span>
            <span><?php echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore->price)?></span>
          </li>
          <?php endif; ?>
           <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
           <?php if($this->sitestore->location && $enableLocation):?>
          <li>
            <span><?php echo $this->translate('Location:'); ?></span>
            <span><?php echo $this->sitestore->location ?></span>
          </li>
          <?php endif; ?>
				</ul>
					<?php
						$user = Engine_Api::_()->user()->getUser($this->sitestore->owner_id);
						$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact_detail');
						$availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');
						$options_create = array_intersect_key($availableLabels, array_flip($view_options));
					?>
          <?php if(!empty($contactPrivacy)): ?>
            <?php if(!empty($options_create) && (!empty($this->sitestore->email) || !empty($this->sitestore->website) || !empty($this->sitestore->phone))):?>
							<h4>
								<?php echo $this->translate('Contact Details:');  ?>
							</h4>
            <?php endif; ?>
						<ul>               
							<?php if($options_create['phone'] == 'Phone'):?>
								<?php if(!empty($this->sitestore->phone)):?>
								<li>
									<span><?php echo $this->translate('Phone:'); ?></span>
									<span><?php echo $this->translate(''); ?> <?php echo $this->sitestore->phone ?></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['email'] == 'Email'):?>
								<?php if(!empty($this->sitestore->email)):?>
								<li>
									<span><?php echo $this->translate('Email:'); ?></span>
									<span><?php echo $this->translate(''); ?>
									<a href='mailto:<?php echo $this->sitestore->email ?>'><?php echo $this->sitestore->email ?></a></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['website'] == 'Website'):?>
								<?php if(!empty($this->sitestore->website)):?>
								<li>
									<span><?php echo $this->translate('Website:'); ?></span>
									<?php if(strstr($this->sitestore->website, 'http://')):?>
									<span><a href='<?php echo $this->sitestore->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
									<?php else:?>
									<span><a href='http://<?php echo $this->sitestore->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
									<?php endif;?>
								</li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
          <?php endif; ?>
                            <?php if($this->sitestore->profile_type): ?>
                <?php $str = $this->ProfileFieldValueLoop($this->sitestore, $this->fieldStructure); ?>
                <?php if(!empty($str) ): ?>
                  <h4>
                    <?php echo $this->translate('Profile Information') ?>
                  </h4>
                  <?php echo $this->ProfileFieldValueLoop($this->sitestore, $this->fieldStructure) ?>	
                       
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

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: packages.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.view', 1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecoupon/views/scripts/getcode.tpl'; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl'; ?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;

	?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle sitestore_create_wrapper clr">
	<h3><?php echo $this->translate("Open a New Store") ?></h3>
	<p><?php echo $this->translate("Open a store using these quick, easy steps and get going.");?></p>	
	<h4 class="sitestore_create_step fleft"><?php echo $this->translate('1. Choose a Store Package');?></h4>
	
	<?php //start coupon plugin work. ?>
	<?php if (!empty($this->modules_enabled) && in_array("sitestore_package", $this->modules_enabled)) : ?>
	<h4 class="sitestore_create_step fright"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon" onclick="javascript:preview('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'sitestore_package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></h4>
	<?php endif; ?>
	
	<div class='sitestore_package_store'>
		<?php if( count($this->paginator) ): ?>
			
        
        <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
                                <?php if (empty($package_view)): ?>
                                <ul class="sitestore_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to stores created under them. You can change your package anytime later.');?></span>
				</li>
		 		<?php   foreach ($this->paginator as $item): ?>
					<li>
			 			<div class="sitestore_package_list_title">
	            <div class="sitestore_create_link">
                <?php
                  $temModule = @unserialize($item->modules);
                ?>
								<?php if (!empty($this->parent_id)): ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'parent_id' => $this->parent_id), 'sitestore_general', true);                  
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Open a Store'); ?> &raquo;</a>
								<?php elseif(!empty($this->store_id)) :?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id, 'store_id' => $this->store_id), 'sitestore_general', true);                 
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Open a Store'); ?> &raquo;</a>
								<?php else: ?>
                <?php
                  $url = $this->url(array("action"=>"create" ,'id' => $item->package_id), 'sitestore_general', true);                  
                ?>
									<a href='<?php echo $url; ?>' ><?php echo $this->translate('Open a Store'); ?> &raquo;</a>
								<?php endif; ?>
	           </div>
           	 <h3>             
                <a href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitestore_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
              </h3>            	 
			 			</div>
             <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/package/_packageInfo.tpl'; ?>
	          
			 		</li>
		    <?php endforeach; ?>
				<br />
				<div>
				  <?php echo $this->paginationControl($this->paginator); ?>
				</div>
			</ul>	
            <?php else:?>
                                        <ul class="seaocore_package_list">
				<li>
					  <span>  <?php echo $this->translate('Select a package that best matches your requirements. Packages differ in terms of features available to stores created under them. You can change your package anytime later.');?></span>
				</li>
                                        <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/package/_verticalPackageInfo.tpl'; ?>
                                </ul>
                                        <?php endif;?>
		<?php else: ?>
		 <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no packages yet.") ?>
		    </span>
		  </div>
		<?php endif; ?>
  </div>
</div>
	
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
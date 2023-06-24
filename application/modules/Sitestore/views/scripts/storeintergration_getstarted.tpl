<?php 
$viewer = Engine_Api::_()->user()->getViewer();
$addableCheck = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'addable.integration');
$createPrivacy = 1;
$sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules',
'core')->isModuleEnabled('sitestoreintegration');
if(!empty($sitestoreintegrationEnabled)) :
	$getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
	$getStoreType = Engine_Api::_()->sitestoreintegration()->getStoreType($getHost);
	if( !empty($getStoreType)):
		$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitestoreintegration')->getIntegrationItems();

		foreach($mixSettingsResults as $modNameValue):
			if($addableCheck == 1) :
			  $Params = Engine_Api::_()->sitestoreintegration()->integrationParams($modNameValue["resource_type"], $modNameValue['listingtype_id']);
				$createPrivacy =  $Params['create_privacy'] ;
			endif;
			if (Engine_Api::_()->sitestore()->hasPackageEnable()) :
				if($createPrivacy) :
					if (Engine_Api::_()->sitestore()->allowPackageContent($this->subject->package_id,
					"modules", $modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id'])) :  ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitestore_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitestore_getstarted_des">
								<?php 
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $modNameValue['listingtype_id'])->toarray(); 
                  $titleSinUc = ucfirst($listingType['title_singular']);
                  $titleSinLc = strtolower($listingType['title_singular']);
                  ?>
									<b><?php echo $this->translate("$titleSinUc Listings"); ?></b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p><?php $item_title = strtolower($modNameValue["item_title"]);
								if ($modNameValue["resource_type"] == 'sitereview_listing') :
									echo $this->translate("Post new $titleSinLc listings to this store."); ?><?php
								else:
									echo $this->translate("Add %s to this store.", $item_title);
								endif;
								?></p>
								<div class="sitestore_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'store_id' => $this->store_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitestoreintegration_create', true) ?>'><?php echo $this->translate("Post / Manage $titleSinUc Listings"); ?></a>
									<?php else: ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'store_id' => $this->store_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitestoreintegration_create', true) ?>'><?php echo $this->translate("Add / Manage %s", $modNameValue["item_title"]);?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
	      <?php	endif;  ?>
			<?php else : ?>
				<?php
				if($createPrivacy) :
					$isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($this->subject,
					$modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id']);
					if (!empty($isStoreOwnerAllow)) : ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitestore_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitestore_getstarted_des">
								<?php 
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $modNameValue['listingtype_id'])->toarray(); 
                  $titleSinUc = ucfirst($listingType['title_singular']);
                  $titleSinLc = strtolower($listingType['title_singular']);
                  ?>
									<b><?php echo $this->translate("$titleSinUc Listings"); ?></b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p><?php $item_title = strtolower($modNameValue["item_title"]);
								if ($modNameValue["resource_type"] == 'sitereview_listing') :
									echo $this->translate("Post new $titleSinLc listings to this store."); ?><?php
								else:
									echo $this->translate("Add %s to this store.", $item_title);
								endif;
								?></p>
								<div class="sitestore_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'store_id' => $this->store_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitestoreintegration_create', true) ?>'><?php echo $this->translate("Post / Manage $titleSinUc Listings"); ?></a>
									<?php else: ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'store_id' => $this->store_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitestoreintegration_create', true) ?>'><?php echo $this->translate("Add / Manage %s", $modNameValue["item_title"]);?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif;?>
		<?php	endforeach;  ?>
	<?php endif; ?>
<?php endif;	?>
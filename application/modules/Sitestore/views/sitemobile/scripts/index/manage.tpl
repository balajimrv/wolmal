<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>	

		<?php  
			$sitestore_approved = Zend_Registry::isRegistered('sitestore_approved') ? Zend_Registry::get('sitestore_approved') : null;
			$renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.renew.email', 2))));?>
	  <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
		  <div class="tip"> 
		  <span><?php echo $this->translate('You have already created the maximum number of stores allowed. If you would like to create a new store, please delete an old one first.'); ?> </span> 
		  </div>
		  <br/>
	  <?php endif; ?>
	  
	  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
      <div class="sm-content-list">
		    <ul class="seaocore_browse_list" data-role="listview" data-inset="false">
		    <?php foreach ($this->paginator as $sitestore): ?>
			   <li  data-icon="arrow-r">
          <a href="<?php echo $sitestore->getHref();?>">			           
            <!-- ADD PARTIAL VIEWS -->
            <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/sitemobile_partial_views.tpl';?>
            
            <?php $expiry=Engine_Api::_()->sitestore()->getExpiryDate($sitestore);
            if($expiry !=="Expired" && $expiry !== $this->translate('Never Expires'))
              echo $this->translate("Expiration Date: ");?>
            <p style="color: green;">
           <?php  echo $expiry; ?>
            </p>
			    </a>            
              
            
            <?php if(false):?>
                        <a href="#manage_<?php echo $sitestore->getGuid()?>" data-rel="popup"></a>        
                        <div data-role="popup" id="manage_<?php echo $sitestore->getGuid()?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme'=>"c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
                        <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                            <?php if ($this->can_edit): ?>
                                <?php if(empty ($sitestore->declined)): ?>
                                <?php if($sitestore->draft == 0) echo $this->htmlLink(array('route' => 'sitestore_publish', 'store_id' => $sitestore->store_id), $this->translate('Publish Store'), array('class'=>'buttonlink smoothbox icon_sitestore_publish ui-btn-default')) ?>
                                  <?php if (!$sitestore->closed): ?>
                                  <a href='<?php echo $this->url(array('store_id' => $sitestore->store_id, 'closed' => 1), 'sitestore_close', true) ?>' class='buttonlink icon_sitestores_close ui-btn-default'><?php echo $this->translate('Close Store'); ?></a>
                                  <?php else: ?>
                                  <a href='<?php echo $this->url(array('store_id' => $sitestore->store_id, 'closed' => 0), 'sitestore_close', true) ?>' class='buttonlink icon_sitestores_open ui-btn-default'><?php echo $this->translate('Open Store'); ?></a>
                                  <?php endif; ?>
                                <?php endif; ?>
                              <?php endif; ?>						
                            <a href="#" data-rel="back" class="ui-btn-default ui-btn-main">
                              <?php echo $this->translate('Cancel'); ?>
                            </a>
                        </div> 
                        </div>
              <?php endif; ?>	
                        
                        
			    </li>
		    <?php endforeach; ?>
		  </ul>
      </div>
	  <?php elseif ($this->search): ?>
			<div class="tip"> <span> <?php if(!empty($sitestore_approved)){ echo $this->translate('You do not have any store which matches your search criteria.'); }else { echo $this->translate($this->store_manage_msg); } ?> </span> </div>
	  <?php else: ?>
			<div class="tip">
				<span> <?php if(!empty($sitestore_approved)){ echo $this->translate('You do not have any stores yet.'); }else { echo $this->translate($this->store_manage_msg); } ?>
				</span>
			</div>
		<?php endif; ?>
      
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsettings.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
  
<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'adsettings'), $this->translate('Store Ad Settings'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'adsettings'), $this->translate('Product Ad Settings'), array())
    ?>
    </li>			
  </ul>
</div>  

<div class='clear sitestore_settings_form'>
  <div class='settings'> 
    <?php echo $this->form->render($this); ?>
  </div>
</div> 

<script type="text/javascript">
//var show_community_ad = '<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1); ?>';
  window.addEvent('domready', function() {
    showads('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1); ?>');
    //showlightboxads('<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lightboxads', 1); ?>');
  });

//   function showlightboxads(option) {
//     if(show_community_ad == 1) {
// 			if($('sitestore_adtype-wrapper')) {
// 				if(option == 1) {
// 					$('sitestore_adtype-wrapper').style.display = 'block';
// 				}
// 				else {
// 					$('sitestore_adtype-wrapper').style.display = 'none';
// 				}
// 			}
// 		}
//   }

  function showads(option) {	 	
    if(option == 1) {
//       if($('sitestore_lightboxads-wrapper')) {
//         $('sitestore_lightboxads-wrapper').style.display = 'block';
//         if($('sitestore_lightboxads-1').checked) {
//           $('sitestore_adtype-wrapper').style.display = 'block';
//         }
//         else {
//           $('sitestore_adtype-wrapper').style.display = 'none';
//         }
//       }
			if($('sitestore_admylikes-wrapper')) {
				$('sitestore_admylikes-wrapper').style.display = 'block';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitestore_adnotewidget-wrapper').style.display = 'block';
        $('sitestore_adnoteview-wrapper').style.display = 'block';
        $('sitestore_adnotebrowse-wrapper').style.display = 'block';
        $('sitestore_adnotecreate-wrapper').style.display = 'block';
        $('sitestore_adnoteedit-wrapper').style.display = 'block';
        $('sitestore_adnotedelete-wrapper').style.display = 'block';
        $('sitestore_adnoteaddphoto-wrapper').style.display = 'block';
        $('sitestore_adnoteeditphoto-wrapper').style.display = 'block';
        $('sitestore_adnotesuccess-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitestore_adeventwidget-wrapper').style.display = 'block';
        $('sitestore_adeventcreate-wrapper').style.display = 'block';
        $('sitestore_adeventedit-wrapper').style.display = 'block';
        $('sitestore_adeventdelete-wrapper').style.display = 'block';
        $('sitestore_adeventview-wrapper').style.display = 'block';
        $('sitestore_adeventbrowse-wrapper').style.display = 'block';
        	$('sitestore_adeventaddphoto-wrapper').style.display = 'block';
				$('sitestore_adeventeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitestore_adalbumwidget-wrapper').style.display = 'block';
        $('sitestore_adalbumview-wrapper').style.display = 'block';
         $('sitestore_adalbumbrowse-wrapper').style.display = 'block';
        $('sitestore_adalbumcreate-wrapper').style.display = 'block';
        $('sitestore_adalbumeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitestore_addicussionwidget-wrapper').style.display = 'block';
        $('sitestore_addiscussionview-wrapper').style.display = 'block';
        $('sitestore_addiscussioncreate-wrapper').style.display = 'block';
        $('sitestore_addiscussionreply-wrapper').style.display = 'block';				
      }
	
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitestore_addocumentwidget-wrapper').style.display = 'block';
        $('sitestore_addocumentview-wrapper').style.display = 'block';
        $('sitestore_addocumentbrowse-wrapper').style.display = 'block';
        $('sitestore_addocumentcreate-wrapper').style.display = 'block';
        $('sitestore_addocumentedit-wrapper').style.display = 'block';
        $('sitestore_addocumentdelete-wrapper').style.display = 'block';		 		
      }
		  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitestore_advideowidget-wrapper').style.display = 'block';
        $('sitestore_advideoview-wrapper').style.display = 'block';
        $('sitestore_advideobrowse-wrapper').style.display = 'block';
        $('sitestore_advideocreate-wrapper').style.display = 'block';
        $('sitestore_advideoedit-wrapper').style.display = 'block';
        $('sitestore_advideodelete-wrapper').style.display = 'block';			 		
      }
		  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitestore_adpollwidget-wrapper').style.display = 'block';
        $('sitestore_adpollview-wrapper').style.display = 'block';
        $('sitestore_adpollbrowse-wrapper').style.display = 'block';
        $('sitestore_adpollcreate-wrapper').style.display = 'block';
        $('sitestore_adpolldelete-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitestore_adreviewwidget-wrapper').style.display = 'block';
        $('sitestore_adreviewcreate-wrapper').style.display = 'block';
        $('sitestore_adreviewedit-wrapper').style.display = 'block';
        $('sitestore_adreviewdelete-wrapper').style.display = 'block';			
        $('sitestore_adreviewview-wrapper').style.display = 'block';	
        $('sitestore_adreviewbrowse-wrapper').style.display = 'block';		
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitestore_adofferwidget-wrapper').style.display = 'block';		
        $('sitestore_adofferstore-wrapper').style.display = 'block';
        $('sitestore_adofferlist-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isformenabled ?>) {
        $('sitestore_adformwidget-wrapper').style.display = 'block';
        $('sitestore_adformcreate-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitestore_adinvite-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitestore_adbadgeview-wrapper').style.display = 'block';
      }		
			
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitestore_adlocationwidget-wrapper').style.display = 'block';
        $('sitestore_adoverviewwidget-wrapper').style.display = 'block';
        $('sitestore_adinfowidget-wrapper').style.display = 'block';
        $('sitestore_adclaimview-wrapper').style.display = 'block';
        $('sitestore_adtagview-wrapper').style.display = 'block';
      }		
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitestore_admusicwidget-wrapper').style.display = 'block'; 	
        $('sitestore_admusicview-wrapper').style.display = 'block';
        $('sitestore_admusicbrowse-wrapper').style.display = 'block';
        $('sitestore_admusiccreate-wrapper').style.display = 'block';
        $('sitestore_admusicedit-wrapper').style.display = 'block';
      }
      
	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitestoreintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitestore_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'block';
					<?php  } ?>
       <?php endif;?>
      }
   	  //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->istwitterenabled ?>) {
        $('sitestore_adtwitterwidget-wrapper').style.display = 'block'; 	
      }
			if(<?php echo $this->ismemberenabled ?>) {
        $('sitestore_admemberwidget-wrapper').style.display = 'block'; 	
        $('sitestore_admemberbrowse-wrapper').style.display = 'block'; 	
      }

    } 
    else {
      if($('sitestore_lightboxads-wrapper')) {
        $('sitestore_lightboxads-wrapper').style.display = 'none';
        $('sitestore_adtype-wrapper').style.display = 'none';
      }
			if($('sitestore_admylikes-wrapper')) {
				$('sitestore_admylikes-wrapper').style.display = 'none';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitestore_adnotewidget-wrapper').style.display = 'none';
        $('sitestore_adnoteview-wrapper').style.display = 'none';
        $('sitestore_adnotebrowse-wrapper').style.display = 'none';
        $('sitestore_adnotecreate-wrapper').style.display = 'none';
        $('sitestore_adnoteedit-wrapper').style.display = 'none';
        $('sitestore_adnotedelete-wrapper').style.display = 'none';
        $('sitestore_adnoteaddphoto-wrapper').style.display = 'none';
        $('sitestore_adnoteeditphoto-wrapper').style.display = 'none';
        $('sitestore_adnotesuccess-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitestore_adeventwidget-wrapper').style.display = 'none';
        $('sitestore_adeventcreate-wrapper').style.display = 'none';
        $('sitestore_adeventedit-wrapper').style.display = 'none';
        $('sitestore_adeventdelete-wrapper').style.display = 'none';
        $('sitestore_adeventview-wrapper').style.display = 'none';
        $('sitestore_adeventbrowse-wrapper').style.display = 'none';
        $('sitestore_adeventaddphoto-wrapper').style.display = 'none';
				$('sitestore_adeventeditphoto-wrapper').style.display = 'none';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitestore_adalbumwidget-wrapper').style.display = 'none';
        $('sitestore_adalbumview-wrapper').style.display = 'none';
        $('sitestore_adalbumbrowse-wrapper').style.display = 'none';
        $('sitestore_adalbumcreate-wrapper').style.display = 'none';
        $('sitestore_adalbumeditphoto-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitestore_addicussionwidget-wrapper').style.display = 'none';
        $('sitestore_addiscussionview-wrapper').style.display = 'none';
        $('sitestore_addiscussioncreate-wrapper').style.display = 'none';
        $('sitestore_addiscussionreply-wrapper').style.display = 'none';								
      }
		
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitestore_addocumentwidget-wrapper').style.display = 'none';
        $('sitestore_addocumentview-wrapper').style.display = 'none';
        $('sitestore_addocumentbrowse-wrapper').style.display = 'none';
        $('sitestore_addocumentcreate-wrapper').style.display = 'none';
        $('sitestore_addocumentedit-wrapper').style.display = 'none';
        $('sitestore_addocumentdelete-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitestore_advideowidget-wrapper').style.display = 'none';
        $('sitestore_advideoview-wrapper').style.display = 'none';
        $('sitestore_advideobrowse-wrapper').style.display = 'none';
        $('sitestore_advideocreate-wrapper').style.display = 'none';
        $('sitestore_advideoedit-wrapper').style.display = 'none';
        $('sitestore_advideodelete-wrapper').style.display = 'none';			 		
      }
			  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitestore_adpollwidget-wrapper').style.display = 'none';
        $('sitestore_adpollview-wrapper').style.display = 'none';
        $('sitestore_adpollbrowse-wrapper').style.display = 'none';
        $('sitestore_adpollcreate-wrapper').style.display = 'none';
        $('sitestore_adpolldelete-wrapper').style.display = 'none';
      }
		
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitestore_adreviewwidget-wrapper').style.display = 'none';
        $('sitestore_adreviewcreate-wrapper').style.display = 'none';
        $('sitestore_adreviewedit-wrapper').style.display = 'none';
        $('sitestore_adreviewdelete-wrapper').style.display = 'none';
        $('sitestore_adreviewview-wrapper').style.display = 'none';
        $('sitestore_adreviewbrowse-wrapper').style.display = 'none';						
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitestore_adofferwidget-wrapper').style.display = 'none';		
        $('sitestore_adofferstore-wrapper').style.display = 'none';
        $('sitestore_adofferlist-wrapper').style.display = 'none';
      }
		  			
      if(<?php echo $this->isformenabled ?>) {
        $('sitestore_adformwidget-wrapper').style.display = 'none';
        $('sitestore_adformcreate-wrapper').style.display = 'none';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitestore_adinvite-wrapper').style.display = 'none';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitestore_adbadgeview-wrapper').style.display = 'none';
      }			
				
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitestore_adlocationwidget-wrapper').style.display = 'none';
        $('sitestore_adoverviewwidget-wrapper').style.display = 'none';
        $('sitestore_adinfowidget-wrapper').style.display = 'none';
        $('sitestore_adclaimview-wrapper').style.display = 'none';
        $('sitestore_adtagview-wrapper').style.display = 'none';
      }
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitestore_admusicwidget-wrapper').style.display = 'none'; 	
        $('sitestore_admusicview-wrapper').style.display = 'none';
        $('sitestore_admusicbrowse-wrapper').style.display = 'none';
        $('sitestore_admusiccreate-wrapper').style.display = 'none';
        $('sitestore_admusicedit-wrapper').style.display = 'none';
       }

	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitestoreintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitestore_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'none';
					<?php  } ?>
       <?php endif;?>
      }

			if(<?php echo $this->ismemberenabled ?>) {
        $('sitestore_admemberwidget-wrapper').style.display = 'none'; 	
        $('sitestore_admemberbrowse-wrapper').style.display = 'none'; 	
      }

	    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
			if(<?php echo $this->istwitterenabled ?>) {
        $('sitestore_adtwitterwidget-wrapper').style.display = 'none'; 	
      }
    } 	
  } 
</script>

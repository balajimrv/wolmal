<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'statistic'), $this->translate('Store Statistics'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'statistic'), $this->translate('Product Statistics'), array())
    ?>
    </li>
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'settings', 'action' => 'order-statistics'), $this->translate('Product Orders Statistics'), array()) ?>
    </li>    
  </ul>
</div>  

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate('Statistics for stores'); ?></h3>
        <p class="form-description"> <?php echo $this->translate('Below are some valuable statistics for the Stores submitted on this site.'); ?> </p>
        <table class='admin_table sitestore_admin_statistic' width="100%">
          <tbody>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalSitestore) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Published Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalPublish) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Stores in Draft'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalDrafted) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Closed Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalClosed) ?></td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Open Stores'); ?> :</td>
              <td>
                <?php echo $this->locale()->toNumber($this->totalopen) ?>
              </td>
            </tr>
            <tr>
              <td width="45%"><?php echo $this->translate('Total Approved Stores'); ?> :</td>
              <td>
                <?php echo $this->locale()->toNumber($this->totalapproved) ?>
              </td>
            </tr>	            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Disapproved Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totaldisapproved) ?></td>
            </tr>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Featured Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalfeatured) ?></td>
            </tr>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Sponsored Stores'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalsponsored) ?></td>
            </tr>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Reviews'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalreview) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Discussions'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldiscussion) ?></td>
              </tr>
            <?php endif; ?>            
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Discussions Posts'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldiscussionpost) ?></td>
              </tr>
            <?php endif; ?>            
            <tr>
              <td width="45%"><?php echo $this->translate('Total Photos'); ?> :</td>
              <td><?php echo $this->locale()->toNumber($this->totalphotopost) ?></td>
            </tr>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Albums'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalalbumpost) ?></td>
              </tr>
            <?php endif; ?>            
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Notes'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalnotepost) ?></td>
              </tr>
            <?php endif; ?>           
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Events'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaleventpost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Documents'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totaldocumentpost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Videos'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalvideopost) ?></td>
              </tr>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Polls'); ?> :</td>
                <td>
                  <?php echo $this->locale()->toNumber($this->totalpollpost) ?></td>
              </tr>
            <?php endif; ?>                       
            <tr>
              <td width="45%"><?php echo $this->translate('Total Comments Posts'); ?> :</td>
              <td>
                <?php if (!empty($this->totalcommentpost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totalcommentpost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr> 

            <tr>
              <td width="45%"><?php echo $this->translate('Total Likes'); ?> :</td>
              <td>
                <?php if (!empty($this->totallikepost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totallikepost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr> 

            <tr>
              <td width="45%"><?php echo $this->translate('Total Views'); ?> :</td>
              <td>
                <?php if (!empty($this->totalviewpost)) : ?>
                  <?php echo $this->locale()->toNumber($this->totalviewpost) ?></td>
              <?php else : ?>
                <?php echo 0 ?>
              <?php endif; ?>
            </tr>

            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Offers'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalofferpost) ?></td>
              </tr>
            <?php endif; ?> 
                        
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')): ?>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Playlist'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalplaylists) ?></td>
              </tr>
              <tr>
                <td width="45%"><?php echo $this->translate('Total Songs'); ?> :</td>
                <td><?php echo $this->locale()->toNumber($this->totalsongs) ?></td>
              </tr>
            <?php endif; ?>
                        
		        <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
							<?php
								$sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules',
									'core')->isModuleEnabled('sitestoreintegration');
								if(!empty($sitestoreintegrationEnabled)) :
								?>
								<?php
								$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' ,
								'sitestoreintegration')->getIntegrationItems();
								foreach($mixSettingsResults as $modNameValue): ?>
									<?php if ($modNameValue['resource_type'] == 'sitereview_listing') : ?>
										<?php $countResults = Engine_Api::_()->getDbtable( 'contents' , 'sitestoreintegration'
									)->getCountResults($modNameValue['resource_type'], $modNameValue['listingtype_id']); ?>
										<tr>
											<td width="45%"><?php echo $this->translate("Total " . $modNameValue['item_title']); ?> :</td>
											<td><?php echo $this->locale()->toNumber($countResults) ?></td>
										</tr>
									<?php else : ?>
									<?php $countResults = Engine_Api::_()->getDbtable( 'contents' , 'sitestoreintegration'
									)->getCountResults($modNameValue['resource_type']); ?>
										<tr>
											<td width="45%"><?php echo $this->translate("Total " . $modNameValue['item_title']); ?> :</td>
											<td><?php echo $this->locale()->toNumber($countResults) ?></td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
            <?php //END FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
            
          </tbody>
        </table>
    </form>
  </div>
</div>

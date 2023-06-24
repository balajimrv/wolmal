<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>


<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
  
<div class='tabs'>
  <ul class="navigation">
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'statistic'), $this->translate('Store Statistics'), array())
    ?>
    </li>
    <li class="active">
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
        <h3><?php echo $this->translate('Statistics for Products');?></h3>
        <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Products submitted on this site.');?>
        </p>
        <br />
        
        <table class='admin_table sr_sitestoreproduct_statistics_table' width="100%">
          <tbody>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Products");?> :</td>
            	<td><?php echo $this->totalSitestoreproduct ?></td>
            </tr> 
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Simple Products");?> :</td>
            	<td><?php echo $this->totalSimpleProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Grouped Products");?> :</td>
            	<td><?php echo $this->totalGroupedProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Configurable Products");?> :</td>
            	<td><?php echo $this->totalConfigurableProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Virtual Products");?> :</td>
            	<td><?php echo $this->totalVirtualProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Bundled Products");?> :</td>
            	<td><?php echo $this->totalBundledProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Downloadable Products");?> :</td>
            	<td><?php echo $this->totalDownloadableProducts ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Editors");?> :</td>
            	<td><?php echo $this->totalEditors ?></td>
            </tr>                        
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Published Products");?> :</td>
            	<td><?php echo $this->totalPublish ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Products in Draft ");?> :</td>
            	<td><?php echo $this->totalDrafted ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Closed Products");?> :</td>
            	<td><?php echo $this->totalClosed ?></td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Open Products");?> :</td>
            	<td>
	            	<?php echo $this->totalOpen ?>
            	</td>
            </tr>
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Approved Products");?> :</td>
            	<td>
            		<?php echo $this->totalapproved ?>
            	</td>
            </tr>	
            
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Disapproved Products");?> :</td>
            	<td><?php echo $this->totaldisapproved ?></td>
            </tr>
            
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Featured Products");?> :</td>
            	<td><?php echo $this->totalfeatured ?></td>
            </tr>
            
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Sponsored Products");?> :</td>
            	<td><?php echo $this->totalsponsored ?></td>
            </tr>
     
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Editor Reviews');?> :</td>
            	<td><?php echo $this->totalEditorReviews ?></td>
           </tr>
           
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Editor Reviews in Draft');?> :</td>
            	<td><?php echo $this->totalDraftEditorReviews ?></td>
           </tr>           
      
           <tr>
            	<td width="50%"><?php echo $this->translate('Total User Reviews');?> :</td>
            	<td><?php echo $this->totalUserReviews ?></td>
           </tr>
           
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Approved Non-logged in user Reviews');?> :</td>
            	<td><?php echo $this->totalApprovedVisitorsReviews ?></td>
           </tr>
      
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Dis-approved Non-logged in user Reviews');?> :</td>
            	<td><?php echo $this->totalDisApprovedVisitorsReviews ?></td>
           </tr>              
            
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Reviews');?> :</td>
            	<td><?php echo $this->totalReviews ?></td>
            </tr>
            
           <tr>
            	<td width="50%"><?php echo $this->translate('Total Discussions');?> :</td>
            	<td><?php echo $this->totalDiscussionTopics ?></td>
            </tr>
            
            <tr>
            	<td width="50%"><?php echo $this->translate('Total Discussions Posts');?> :</td>
            	<td><?php echo $this->totalDiscussionPosts ?></td>
            </tr>

            <tr>
            	<td width="50%"><?php echo $this->translate("Total Photos in Products");?> :</td>
            	<td><?php echo $this->totalPhotos ?></td>
            </tr>
            
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Videos in Products");?> :</td>
            	<td><?php echo $this->totalVideos ?></td>
            </tr>
            
            <tr>
							<td width="50%"><?php echo $this->translate('Total Comments Posts');?> :</td>
            	<td><?php echo $this->totalProductComments ?></td>
            </tr>
            
            <tr>
							<td width="50%"><?php echo $this->translate('Total Likes');?> :</td>
            	<td><?php echo $this->totalProductLikes ?></td>
            </tr>
            
            <tr>
            	<td width="50%"><?php echo $this->translate("Total Wishlists");?> :</td>
            	<td><?php echo $this->totalWishlists ?></td>
            </tr>
            
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
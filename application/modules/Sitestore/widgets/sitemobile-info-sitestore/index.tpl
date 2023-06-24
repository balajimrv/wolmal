<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<?php
$contactPrivacy=0;
$profileTypePrivacy=0;
$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($this->sitestore, 'contact');
	if(!empty($isManageAdmin)) {
		$contactPrivacy = 1;
	}

  // PROFILE TYPE PRIVACY
  $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($this->sitestore, 'profile');
		if(!empty($isManageAdmin)) {
			$profileTypePrivacy = 1;
		}
?>

<?php if($this->showtoptitle == 1):?>

		<?php echo $this->translate("Basic Information") ?>

<?php endif;?>
<!--<div id='id_<?php echo $this->content_id; ?>'>-->
      
<!--<div class='profile_fields'>-->
	<h4 id='show_basicinfo'>
		<span><?php echo $this->translate('Basic Information'); ?></span>
	</h4>
  
	<div class="sm_ui_item_profile_details">
	<table>
		<tbody>
    <?php if($postedBy):?>
     <tr valign="top">
        <td class="label"><div><?php echo $this->translate('Posted By:'); ?> </div></td>
        <td><?php echo $this->htmlLink($this->sitestore->getParent(), $this->sitestore->getParent()->getTitle()) ?></td>
      </tr>
     <?php endif;?>
    	<tr valign="top">
					<td class="label"><div><?php echo $this->translate('Posted:'); ?></div></td>
      <td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->creation_date))) ?></td>
    </tr> 
    <tr valign="top">
					<td class="label"><div><?php echo $this->translate('Last Updated:'); ?></div></td>
			<td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->modified_date))) ?></td>
   </tr>
    <?php if(!empty($this->sitestore->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')): ?>
    	<tr valign="top">
			<td class="label"><div> <?php echo ($this->sitestore->member_title && $this->sitestore->member_count >1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1) ) ? $this->sitestore->member_title . ':' :$this->translate('Members:'); ?></div></td>
			<td><?php echo $this->sitestore->member_count ?></td>
    </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitestore->comment_count)): ?>
    	<tr valign="top">
    		<td class="label"><div><?php echo $this->translate('Comments:'); ?></div></td>
				<td><?php echo $this->sitestore->comment_count ?></td>
       </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitestore->view_count)): ?>
    	<tr valign="top">
      <td class="label"><div><?php echo $this->translate('Views:'); ?></div></td>
			<td><?php echo $this->sitestore->view_count ?></td>
      </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitestore->like_count)): ?>
    <tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Likes:'); ?></div></td>
			<td><?php echo $this->sitestore->like_count ?></td>
     </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitestore->follow_count) && isset($this->sitestore->follow_count)): ?>
  	<tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Followers:'); ?></div></td>
				<td><?php echo $this->translate( $this->sitestore->follow_count) ?></td>
    </tr>
    <?php endif; ?>
    <tr valign="top" class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>		 
		    <td>	
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name)) ?>
				</td>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>	
		    <td>	<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </td>	
	    <?php endif; ?>
    </tr>	
   <tr valign="top">    	
    	<?php if (count($this->sitestoreTags) >0): $tagCount=0;?>
    		<td class="label"><div><?php echo $this->translate('Tags:'); ?></div></td>	
        <td>
    		 <?php foreach ($this->sitestoreTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
							<a href='<?php echo $this->url(array('action' => 'index'), "sitestore_general"); ?>?tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
							<?php $tagCount++; else: ?>
							<a href='<?php echo $this->url(array('action' => 'index'), "sitestore_general"); ?>?tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </td>	
			<?php endif; ?>
  </tr>	
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
     <?php if($this->sitestore->price && $enablePrice):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Price:'); ?></div></td>	
      <td><?php echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore->price) ?></td>	
     </tr>	
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
     <?php if($this->sitestore->location && $enableLocation):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Location:'); ?></div></td>	
      <td><?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($this->sitestore->location), $this->sitestore->location, array('target' => 'blank')) ?>
      </td>
   </tr>	
    <?php endif; ?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Description:'); ?></div></td>
      <td><?php echo $this->viewMore($this->sitestore->body,300,5000) ?></td>
    </tr>	
  </tbody>
  </table>
    </div>
  <?php
		$user = Engine_Api::_()->user()->getUser($this->sitestore->owner_id);
		$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact_detail');
    $availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');		
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));
  ?>
 <?php if(!empty($contactPrivacy)): ?>
  <?php if(!empty($options_create) && (!empty($this->sitestore->email) || !empty($this->sitestore->website) || !empty($this->sitestore->phone))):?>
  <h4>
		<span><?php echo $this->translate('Contact Details');  ?></span>
	</h4>  	
   <div class="sm_ui_item_profile_details">
	<table>
		<tbody>
          <tr valign="top" style="display:none;"> </tr>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitestore->phone)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Phone:'); ?></div></td>
          <td><?php echo $this->translate(''); ?> <a href="tel:<?php echo $this->sitestore->phone?>"> <?php echo $this->sitestore->phone?> </a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitestore->email)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Email:'); ?></div></td>
          <td><?php echo $this->translate(''); ?>
          <a href='mailto:<?php echo $this->sitestore->email ?>'><?php echo $this->sitestore->email ?></a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitestore->website)):?>
        <tr valign="top"> 
         <td class="label"><div><?php echo $this->translate('Website:'); ?></div></td>
          <?php if(strstr($this->sitestore->website, 'http://') || strstr($this->sitestore->website, 'https://')):?>
          <td><a href='<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></td>
          <?php else:?>
          <td><a href='http://<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></td>
          <?php endif;?>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
    </tbody>
  </table>
     </div>
    <?php endif; ?>
  <?php endif; ?>
 	<?php if(!empty ($profileTypePrivacy)):
    $str =  $this->profileFieldValueLoop($this->sitestore, $this->fieldStructure)?>
		<?php if($str): ?>
			<h4 >
				<span><?php  echo $this->translate('Profile Information');  ?></span>
			</h4>
			<?php echo $this->profileFieldValueLoop($this->sitestore, $this->fieldStructure) ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php echo $this->content()->renderWidget("sitemobile.comments", array('type' => $this->sitestore->getType(), 'id' => $this->sitestore->getIdentity())); ?>
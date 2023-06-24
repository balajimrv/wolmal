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
<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl .
'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<script type="text/javascript">

	var store_communityads;
	var contentinformtion;
	var store_showtitle;
	//prev_tab_id = '<?php //echo $this->content_id; ?>';	
  if(contentinformtion == 0) {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}		
		if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
			$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
		}	
		if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
			$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'block';
		}	
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
			$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
	  }
    if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
			$('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'none';
	  }
  }
  
</script>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinfowidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)) : ?>
    <?php $flag = 1; ?>
  <?php else:?>
    <?php $flag = 0; ?>
  <?php endif;?>
<?php endif;?>
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
	<div class="layout_simple_head" id="layout_info">
		<?php echo $this->translate("Basic Information") ?>
	</div>
<?php endif;?>
<div id='id_<?php echo $this->content_id; ?>'>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
<?php if(Engine_Api::_()->getApi('SubCore', 'sitestore')->getSampleAdWidgetEnabled($this->sitestore) || $flag ) : ?>
	<div class="layout_right" >
	<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adcreatelink', 1)) : ?>
			<?php 
				echo $this->content()->renderWidget("communityad.getconnection-link");
		  ?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpreview', 1)) : ?>
			<?php
				// Render Sample Store Ad widget
				echo $this->content()->renderWidget("communityad.storead-preview"); 
			?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>		
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinfowidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)) : ?>
			<div id="communityad_info" >
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinfowidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'store_info'))?>
			</div>
		<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('SubCore', 'sitestore')->getSampleAdWidgetEnabled($this->sitestore) || $flag ) : ?>
	</div>
	<?php endif; ?>
	<div class="layout_middle">
<?php endif;?>
<div class='profile_fields'>
	<h4 id='show_basicinfo'>
		<span><?php echo $this->translate('Basic Information'); ?></span>
	</h4>
	<ul>
    <?php if($postedBy && in_array("posted_by", $this->showContent)):?>
      <li>
        <span><?php echo $this->translate('Posted By:'); ?> </span>
        <span><?php echo $this->htmlLink($this->sitestore->getParent(), $this->sitestore->getParent()->getTitle()) ?></span>
      </li>
     <?php endif;?>
     <?php if(in_array("posted", $this->showContent)): ?>
    <li>
    	<span><?php echo $this->translate('Posted:'); ?></span>
      <span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->creation_date))) ?></span>
    </li> 
    <?php endif; ?>
    <?php if(in_array("last_update", $this->showContent)): ?>
    <li>
    	<span><?php echo $this->translate('Last Updated:'); ?></span>
			<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->modified_date))) ?></span>
    </li>
    <?php endif; ?>
    <?php if(in_array("members", $this->showContent) && !empty($this->sitestore->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')): ?>
    	<li>
			<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1); ?>
			<?php if ($this->sitestore->member_title && $memberTitle) : ?>
				<?php if ($this->sitestore->member_count == 1) : ?>
					<span><?php echo $this->translate('Members:'); ?></span>
					<span><?php echo $this->sitestore->member_count ?></span>
				<?php else: ?>
					<span><?php echo $this->sitestore->member_title . ':' ?></span>
					<span><?php echo $this->sitestore->member_count ?></span>
				<?php endif; ?>
			<?php else: ?>
			<span><?php echo $this->translate('Members:'); ?></span>
			<span><?php echo $this->sitestore->member_count ?></span>
			<?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if(in_array("comments", $this->showContent) && !empty($this->sitestore->comment_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Comments:'); ?></span>
				<span><?php echo $this->sitestore->comment_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(in_array("views", $this->showContent) && !empty($this->sitestore->view_count)): ?>
      <li>
      	<span><?php echo $this->translate('Views:'); ?></span>
				<span><?php echo $this->sitestore->view_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(in_array("likes", $this->showContent) && !empty($this->sitestore->like_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Likes:'); ?></span>
				<span><?php echo $this->sitestore->like_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(in_array("followers", $this->showContent) && !empty($this->sitestore->follow_count) && isset($this->sitestore->follow_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Followers:'); ?></span>
				<span><?php echo $this->translate( $this->sitestore->follow_count) ?></span>
      </li>
    <?php endif; ?>
    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'sitestore','action' => 'index'), 'sitestore_general', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="subcategory" name="subcategory" value=""/>
      <input type="hidden" id="categoryname" name="categoryname" value=""/>
      <input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>
      <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
      <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
        echo $this->start_date; ?>"/>
      <input type="hidden" id="page" name="page"  value=""/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
               echo $this->end_date; ?>"/>
    </form>
     <?php if(in_array("category", $this->showContent)): ?>
    <li class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <span><?php echo $this->translate('Category:'); ?></span> 
		    <span>
		    				
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name)) ?>
				
		    </span>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <span><?php echo $this->translate('Category:'); ?></span>
		    <span><?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </span>
	    <?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if(in_array("tags", $this->showContent)): ?>
    <li>
    	<?php if (count($this->sitestoreTags) >0): $tagCount=0;?>
    		<span><?php echo $this->translate('Tags:'); ?></span>
        <span>
    		 <?php foreach ($this->sitestoreTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
							<?php $tagCount++; else: ?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </span>
			<?php endif; ?>
    </li>
    <?php endif; ?>
  
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
     <?php if(in_array("price", $this->showContent) && $this->sitestore->price && $enablePrice):?>
    <li>
    	<span><?php echo $this->translate('Price:'); ?></span>
      <span><?php echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore->price) ?></span>
    </li>
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
     <?php if(in_array("location", $this->showContent) && $this->sitestore->location && $enableLocation):?>
    <li>
    	<span><?php echo $this->translate('Location:'); ?></span>
      <span><?php echo $this->sitestore->location ?>&nbsp; - 
      <b>
				<?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($this->sitestore->store_id, $this->sitestore->location);
				echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitestore->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
      </b>
      </span>
    </li>
    <?php endif; ?>
    <?php if(in_array("description", $this->showContent)): ?>
     <li>
    	<span><?php echo $this->translate('Description:'); ?></span>
      <span><?php echo $this->viewMore($this->sitestore->body,300,5000) ?></span>
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
		<span><?php echo $this->translate('Contact Details');  ?></span>
	</h4>  	
    <ul>
    	<li style="display:none;"></li>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitestore->phone)):?>
        <li>
          <span><?php echo $this->translate('Phone:'); ?></span>
          <span><?php echo $this->translate(''); ?> <?php echo $this->sitestore->phone ?></span>
        </li>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitestore->email)):?>
        <li>
          <span><?php echo $this->translate('Email:'); ?></span>
          <span><?php echo $this->translate(''); ?>
						<a href="javascript:void(0);" onclick="sitestoreShowSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitestore_profilestore', 'module' => 'sitestore', 'controller' => 'profile', 'action' => 'email-me', "id" => $this->sitestore->store_id), 'default' , true)); ?>'); return false;"><?php echo $this->sitestore->getTitle(); ?></a>
          </span>
        </li>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitestore->website)):?>
        <li>
          <span><?php echo $this->translate('Website:'); ?></span>
          <?php if(strstr($this->sitestore->website, 'http://') || strstr($this->sitestore->website, 'https://')):?>
          <span><a href='<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
          <?php else:?>
          <span><a href='http://<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
          <?php endif;?>
        </li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
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

	<br />
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.checkcomment.widgets', 1)):
		
	 ?>
		<div id="info_comment">
		<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>
		</div>
	<?php endif; ?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
	</div>
<?php endif; ?>
</div>

<script type="text/javascript">

  $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
     if($('global_content').getElement('.layout_sitestoreintegration_profile_items')) {
			  $('global_content').getElement('.layout_sitestoreintegration_profile_items').style.display = 'none';
	   }

     if(store_showtitle != 0 ) {
			if($('profile_status')) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?></h2>";
			}	  
			if($('layout_info')) {
				$('layout_info').style.display = 'block';
				$('show_basicinfo').style.display = 'none';
			}	  	
    }
    
    hideWidgetsForModule('sitestoreinfo');

    if($('global_content').getElement('.layout_sitestore_contactdetails_sitestore')) {
      $('global_content').getElement('.layout_sitestore_contactdetails_sitestore').style.display = 'block';
    }

  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitestore_info_sitestore';
		setLeftLayoutForStore(); 
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitestore_info_sitestore')==null)){
      scrollToTopForStore($("global_content").getElement(".layout_sitestore_info_sitestore"));
    }	        
  });
                    
  var tagAction =function(tag)
  {    
    $('tag').value = tag;
    $('filter_form').submit();
  }
	if($("info_comment"))
  var info_comment = $("info_comment").innerHTML;

function sitestoreShowSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<?php
$reviewApi = Engine_Api::_()->sitestoreproduct();
$expirySettings = $reviewApi->expirySettings();
$approveDate = null;
if ($expirySettings == 2):
  $approveDate = $reviewApi->adminExpiryDuration();
endif;
?>
<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var searchSitestoreproducts = function() {

    var formElements = $('filter_form').getElements('li');
    formElements.each( function(el) {
      var field_style = el.style.display;
      if(field_style == 'none') {
        el.destroy();
      }
    });

    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {
      $('filter_form').submit();
    }
  }
  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride':'min','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride':'max','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });

  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; ?>

<div class='layout_right'> 
	<div class="sr_sitestoreproduct_search_criteria">
	  <?php echo $this->form->setAttrib('class', 'sitestoreproduct-search-box')->render($this) ?>
	</div>  
</div>

<div class='layout_middle'>
  <?php $sitestoreproduct_approved = true;
  $renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.renew.email', 2)))); ?>
  <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
    <div class="tip"> 
      <span><?php echo $this->translate("You have already created the maximum number of products allowed. If you would like to create a new product, please delete an old one first."); ?> </span> 
    </div>
    <br/>
  <?php endif; ?>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="sr_sitestoreproduct_browse_list">
      <?php foreach ($this->paginator as $item): ?>
        <li class="b_medium">
          <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
          	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
							<?php if($item->featured):?>
								<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
              <?php endif;?>
							<?php if($item->newlabel):?>
								<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
							<?php endif;?>
						<?php endif;?>
						
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'center'))) ?>
            
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
							<?php if (!empty($item->sponsored)): ?>
									<div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
										<?php echo $this->translate('SPONSORED'); ?>                 
									</div>
							<?php endif; ?>
						<?php endif; ?>
          </div>
          <div class='sr_sitestoreproduct_browse_list_options'>

            <?php if ($this->can_edit): ?>
              <a href='<?php echo $this->url(array('action' => 'edit', 'product_id' => $item->product_id), "sitestoreproduct_specific", true) ?>' class='buttonlink seaocore_icon_edit'><?php if (!empty($sitestoreproduct_approved)) {
              echo $this->translate("Dashboard");
              } else {
              echo $this->translate($this->product_manage);
              } ?></a>
            <?php endif; ?>

            <?php if ($this->allowed_upload_photo): ?>
              <a href='<?php echo $this->url(array('product_id' => $item->product_id), "sitestoreproduct_albumspecific", true) ?>' class='buttonlink icon_sitestoreproducts_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
            <?php endif; ?>

            <?php if ($this->allowed_upload_video): ?>
              <a href='<?php echo $this->url(array('product_id' => $item->product_id), "sitestoreproduct_videospecific", true) ?>' class='buttonlink icon_sitestoreproducts_video_new'><?php if (!empty($sitestoreproduct_approved)) {
              echo $this->translate('Add Videos');
              } else {
              echo $this->translate($this->product_manage);
              } ?></a>
            <?php endif; ?>

            <?php if ($item->draft == 1 && $this->can_edit)
              echo $this->htmlLink(array('route' => "sitestoreproduct_specific", 'action' => 'publish', 'product_id' => $item->product_id), $this->translate("Publish Product"), array(
                  'class' => 'buttonlink smoothbox icon_sitestoreproduct_publish')) ?> 

            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && !$item->closed && $this->can_edit): ?>
              <a href='<?php echo $this->url(array('action' => 'close', 'product_id' => $item->product_id), "sitestoreproduct_specific", true) ?>' class='buttonlink icon_sitestoreproducts_close'><?php echo $this->translate("Close Product"); ?></a>
            <?php elseif ($this->can_edit): ?>
              <a href='<?php echo $this->url(array('action' => 'close', 'product_id' => $item->product_id), "sitestoreproduct_specific", true) ?>' class='buttonlink icon_sitestoreproducts_open'><?php echo $this->translate("Open Product"); ?></a>
            <?php endif; ?>

            <?php if ($this->can_delete): ?>
              <a href='<?php echo $this->url(array('action' => 'delete', 'product_id' => $item->product_id), "sitestoreproduct_specific", true) ?>' class='buttonlink seaocore_icon_delete'><?php echo $this->translate("Delete Product"); ?></a>
            <?php endif; ?>
          </div>

          <div class='sr_sitestoreproduct_browse_list_info'>
            <div class='sr_sitestoreproduct_browse_list_info_header o_hidden'>
              <div class="sr_sitestoreproduct_list_title"> 
              	<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              </div>	
            </div>

            <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate('created by'); ?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
            <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
            
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2): ?>
              <?php echo $this->partial('_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $item)) ?>,
            <?php endif; ?>        
                    
            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
            <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </div>
            <?php if($approveDate && $approveDate > $item->approved_date):?>
            <div class="sitestoreproduct_browse_list_info_expiry seaocore_txt_red">
              <?php echo $this->translate('Expired');?>
            </div>
            <?php elseif($expirySettings == 2 && $approveDate && $approveDate < $item->approved_date):?>
              <?php $exp = $item->getExpiryTime();?>
							<div class='seaocore_browse_list_info_date clear'>
							<?php echo $exp ? $this->translate("On Sale Till: %s", $this->locale()->toDate($exp, array('size' => 'medium'))) : ''; ?>
							</div>
            <?php elseif($expirySettings == 1):?> 
              <div class="seaocore_browse_list_info_date clear">
								<?php $current_date = date("Y-m-d i:s:m", time());?>
               <?php if(!empty($item->end_date)  && $item->end_date !='0000-00-00 00:00:00'):?>
								<?php if($item->end_date >= $current_date):?>
									 <?php echo $this->translate("On Sale Till: %s", $this->locale()->toDate(strtotime($item->end_date), array('size' => 'medium'))); ?>
								<?php else:?>
									<?php echo $this->translate("On Sale Till: %s", 'Expired', array('size' => 'medium')); ?>
									<?php echo $this->translate('(You can edit the end date to make the product live again.)');?>
								<?php endif;?>
                <?php endif;?>
              </div>
            <?php endif; ?>

            <div class='sr_sitestoreproduct_browse_list_info_blurb'>
			        <?php
			        echo substr(strip_tags($item->body), 0, 350);
			        if (strlen($item->body) > 349)
			          echo "...";
			        ?>
            </div>
            <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
            	<span class="sr_sitestoreproduct_browse_list_info_footer_icons">
								<?php if (empty($item->approved)): ?>
									<i title="<?php echo $this->translate('Not approved');?>" class="sr_sitestoreproduct_icon seaocore_icon_disapproved"></i>
								<?php endif; ?>
								<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) :?>   
									<?php if (!empty($item->sponsored)): ?>
										<i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
									<?php endif; ?>
									
									<?php if (!empty($item->featured)): ?>
										<i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
									<?php endif; ?>
								<?php endif; ?>	
								
								<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $item->closed): ?>
									<i title="<?php echo $this->translate('Closed');?>" class="sr_sitestoreproduct_icon icon_sitestoreproducts_close"></i>
								<?php endif; ?>
            	</span>
            </div>
          </div>
        </li>
    <?php endforeach; ?>
    </ul>
      <?php elseif ($this->search): ?>
    <div class="tip"> 
      <span>
  <?php if (!empty($sitestoreproduct_approved)) {
    echo $this->translate('You do not have any product that match your search criteria.');
  } else {
    echo $this->translate($this->product_manage_msg);
  } ?> 
      </span> 
    </div>
<?php else: ?>
    <div class="tip">
      <span> 
  <?php if (!empty($sitestoreproduct_approved)) {
    echo $this->translate('You do not have any %s.', @strtolower($product_title_plural));
  } else {
    echo $this->translate($this->product_manage_msg);
  } ?>
		
      </span> 
    </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues,'pageAsQuery' => true,)); ?>  
</div>

<script type="text/javascript">
  
  var profile_type = 0;
  var previous_mapped_level = 0;  
  var sitestoreproduct_categories_slug = <?php echo json_encode($this->categories_slug); ?>;
  function showFields(cat_value, cat_level) {
       
    if(cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value); 
      if(profile_type == 0) { profile_type = ''; } else { previous_mapped_level = cat_level; }
      $('profile_type').value = profile_type;
      changeFields($('profile_type'));      
    }
  }
  
  $('filter_form').getElement('.browsesitestoreproducts_criteria').addEvent('keypress', function(e){   
    if( e.key != 'enter' ) return;
    searchSitestoreproducts();
  });

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getMapping('profile_type')); ?>;
    for(i = 0; i < mapping.length; i++) {
      if(mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }

 function addOptions(element_value, element_type, element_updated, domready) {

    var element = $(element_updated);
    if(domready == 0){
      switch(element_type){    
        case 'cat_dependency':
          $('subcategory_id'+'-wrapper').style.display = 'none';
          clear($('subcategory_id'));
          $('subcategory_id').value = 0;
          $('categoryname').value = sitestoreproduct_categories_slug[element_value];
  
        case 'subcat_dependency':
          $('subsubcategory_id'+'-wrapper').style.display = 'none';
          clear($('subsubcategory_id'));
          $('subsubcategory_id').value = 0;
          $('subsubcategoryname').value = '';
          if(element_type=='subcat_dependency')
            $('subcategoryname').value = sitestoreproduct_categories_slug[element_value];
          else
            $('subcategoryname').value='';
      }
    }
    
    if(element_value <= 0) return;  
   
    var url = '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'categories'), "default", true); ?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        element_value : element_value,
        element_type : element_type
      },

      onSuccess : function(responseJSON) {
        var categories = responseJSON.categories;
        var option = document.createElement("OPTION");
        option.text = "";
        option.value = 0;
        element.options.add(option);
        for (i = 0; i < categories.length; i++) {
          var option = document.createElement("OPTION");
          option.text = categories[i]['category_name'];
          option.value = categories[i]['category_id'];
          element.options.add(option);
          sitestoreproduct_categories_slug[categories[i]['category_id']]=categories[i]['category_slug'];
        }

        if(categories.length  > 0 )
          $(element_updated+'-wrapper').style.display = 'block';
        else
          $(element_updated+'-wrapper').style.display = 'none';
        
        if(domready == 1){
          var value=0;
          if(element_updated=='category_id'){
            value = search_category_id;
          }else if(element_updated=='subcategory_id'){
            value = search_subcategory_id;
          }else{
            value =search_subsubcategory_id;
          }
          $(element_updated).value = value;
        }
      }

    }),{'force':true});
  }

  function clear(element)
  { 
    for (var i = (element.options.length-1); i >= 0; i--)	{
      element.options[ i ] = null;
    }
  }
  
  var search_category_id,search_subcategory_id,search_subsubcategory_id;
  window.addEvent('domready', function() {
    
    search_category_id='<?php echo $this->category_id ?>';
   
    if(search_category_id !=0) {
      
      addOptions(search_category_id,'cat_dependency', 'subcategory_id',1);
      
      search_subcategory_id='<?php echo $this->subcategory_id ?>';      
      
      if(search_subcategory_id !=0) {
        search_subsubcategory_id='<?php echo $this->subsubcategory_id ?>';
        addOptions(search_subcategory_id,'subcat_dependency', 'subsubcategory_id',1);
      }
    }   
  });
  
</script>
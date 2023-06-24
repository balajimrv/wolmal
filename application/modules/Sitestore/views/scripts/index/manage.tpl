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
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("http://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  
  window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('location'));
  });
</script>
<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<script type="text/javascript">
  var storeAction =function(store){
    $('store').value = store;
    $('filter_form').submit();
  }
   var searchSitestores = function() {

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
    } while( nextEl )
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    ))
?>
	<div class='layout_middle'>
		<?php  
			$item_approved = Zend_Registry::isRegistered('sitestore_approved') ? Zend_Registry::get('sitestore_approved') : null;
			$renew_date= date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.renew.email', 2))));?>
	  <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
		  <div class="tip"> 
		  	<span><?php echo $this->translate('You have already created the maximum number of stores allowed. If you would like to open a new store, please delete an old one first.'); ?></span> 
		  </div>
		  <br/>
	  <?php endif; ?>
	  
	  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
		  <ul class="seaocore_browse_list">
				<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
		    <?php foreach ($this->paginator as $item): ?>
			    <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?><?php if($item->featured):?>class="lists_highlight"<?php endif;?><?php endif;?>>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
             <?php if($item->featured):?>
               <span title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured')?></span>
            <?php endif;?>
          <?php endif;?>
			      <div class='seaocore_browse_list_photo'>
			        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align'=>'left'))) ?> 
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
            <?php if (!empty($item->sponsored)): ?>
              <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
              if (!empty($sponsored)) { ?>
                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
              <?php } ?>
            <?php endif; ?>
          <?php endif; ?>  
			      </div>
	
			      <div class='seaocore_browse_list_options'>
							<?php if ($this->can_edit): ?>
	                <?php if(empty ($item->declined)): ?>
	                <a href='<?php echo $this->url(array('store_id' => $item->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php if(!empty($item_approved)){ echo $this->translate('Dashboard'); }else { echo $this->translate($this->store_manage); } ?></a>
	
	                <?php if($item->draft == 0) echo $this->htmlLink(array('route' => 'sitestore_publish', 'store_id' => $item->store_id), $this->translate('Publish Store'), array('class'=>'buttonlink smoothbox icon_sitestore_publish')) ?>
                      <a href='<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'product', 'menuId' => 62, 'method' => 'manage'), 'sitestore_store_dashboard', true); ?>' class='buttonlink item_icon_sitestoreproduct'><?php echo $this->translate('My Products'); ?></a>                      
                    <?php endif; ?>

	              <?php endif; ?>
								<?php if ($this->can_delete): ?>
									<a href='<?php echo $this->url(array('store_id' => $item->store_id), 'sitestore_delete', true) ?>' class='buttonlink icon_sitestores_delete'><?php echo $this->translate('Delete Store'); ?></a>
								<?php endif; ?>
								<?php if (Engine_Api::_()->sitestore()->canShowPaymentLink($item->store_id)): ?>
								<div class="tip">
									<span>
										<a href='javascript:void(0);' onclick="submitSession(<?php echo $item->store_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
									</span>
								</div>
							<?php endif; ?>
	
	            <?php if (Engine_Api::_()->sitestore()->canShowRenewLink($item->store_id)): ?>
	              <div class="tip">
									<span>
										<a href='javascript:void(0);' onclick="submitSession(<?php echo $item->store_id ?>)"><?php echo $this->translate('Renew Store'); ?></a>
									</span>
								</div>
	            <?php endif; ?>
	          </div>
	
              <?php  $this->partial()->setObjectKey('sitestore');
				         echo $this->partial('partial_views.tpl', $item); ?>
              <?php echo $this->viewMore($item->body,200,5000) ?>
			        </div>
			      </div>
			    </li>
		    <?php endforeach; ?>
		  </ul>
	  <?php elseif ($this->search): ?>
			<div class="tip"> <span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any store which matches your search criteria.'); }else { echo $this->translate($this->store_manage_msg); } ?> </span> </div>
	  <?php else: ?>
			<div class="tip">
				<span> <?php if(!empty($item_approved)){ echo $this->translate('You do not have any stores yet.'); }else { echo $this->translate($this->store_manage_msg); } ?>
					<?php if ($this->can_create): ?>
						<?php  if (Engine_Api::_()->sitestore()->hasPackageEnable()):
						$createUrl=$this->url(array('action'=>'index'), 'sitestore_packages');
						else:
						$createUrl=$this->url(array('action'=>'create'), 'sitestore_general');
						endif; ?>
						<?php echo $this->translate('Get started by %1$screating%2$s a new store.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestore")); ?>

	
	<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
			<input type="hidden" name="store_id_session" id="store_id_session" />
	</form>
</div>

<script type="text/javascript">
  function submitSession(id){
    
    document.getElementById("store_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>

<?php $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitestore', 'category_id');
if(!empty($row->display)):
?>
<script type="text/javascript">
  var form;

  var subcategoryies = function(category_id, sub, subcatname, subsubcat)
  {
	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form=$('filter_form_category');
		}

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id?>';
    }
    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id?>';
    }
    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id?>';
    }
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }
    if(category_id != 0) {
      if(sub == '')
      subsubcat = 0;
      changesubcategory(sub, subsubcat);
    }

  	var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitestore_general', true);?>';
    en4.core.request.send(new Request.JSON({
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
      	clear('subcategory_id');
        var  subcatss = responseJSON.subcats;
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = sub;
          form.elements['subcategory'].value = $('subcategory_id').value;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = $('subcategory_id').value;
          if(form.elements['subsubcategory'])
          form.elements['subsubcategory'].value = subsubcat;
          if(form.elements['subsubcategory_id'])
          form.elements['subsubcategory_id'].value = subsubcat;
        }

        if(subcatss.length == 0) {
	      	form.elements['categoryname'].value = 0;
        }

        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));
  };
      var changesubcategory = function(subcatid, subsubcat)
		{
			var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitestore_general', true);?>';
   		var request = new Request.JSON({
		    url : url,
		    data : {
		      format : 'json',
		      subcategory_id_temp : subcatid
				},
		    onSuccess : function(responseJSON) {
		    	clear('subsubcategory_id');
		    	var  subsubcatss = responseJSON.subsubcats;
		      addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
            $('subsubcategory_id').value = subsubcat;
            if(form.elements[' subsubcategory_id'])
            form.elements[' subsubcategory_id'].value = $('subsubcategory_id').value;
            if(form.elements[' subsubcategory'])
            form.elements['subsubcategory'].value = $('subsubcategory_id').value;
            if($('subsubcategory_id')) {
              $('subsubcategory_id').value = subsubcat;
            }
          }

          if(subcatid == 0) {
            clear('subsubcategory_id');
            if($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
          }
		    }
			});
      request.send();
		};
        function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }
  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }
  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;			
    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'block';
      $('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } 
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }	
  

  var cat = '<?php echo $this->category_id ?>';

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';
    subcategoryies(cat, sub, subcatname,subsubcat);
  }
  
  function show_subcat(cat_id) 
  {		

    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/plus16.gif';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/plus16.gif';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/minus16.gif';
      }		
    }
  }
  
</script>

<?php endif;?>

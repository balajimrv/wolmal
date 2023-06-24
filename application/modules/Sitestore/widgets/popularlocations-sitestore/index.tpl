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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">
  var locationAction =function(cityValue)
  {
    if($("tag"))
      $("tag").value='';
    var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_location')){
				form=$('filter_form_location');
			}
    form.elements['sitestore_location'].value = cityValue;
    
		form.submit();
  }

</script>

<ul class="seaocore_browse_category">
  <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitestore_general', true) ?>' style='display: none;'>
    <input type="hidden" id="sitestore_location" name="sitestore_location"  value=""/>
  </form>
  <?php foreach ($this->sitestoreLocation as $sitestoreLocation): ?>
    <?php if (!empty($sitestoreLocation->city) ||  !empty($sitestoreLocation->state)): ?>
      <li>
        <div class="cat"  <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $sitestoreLocation->city ||  $this->searchLocation == $sitestoreLocation->state ) ): ?>style="font-weight: bold;" <?php endif; ?> >
          <a href="javascript:void(0);" onclick="locationAction('<?php if(!empty($sitestoreLocation->city))echo $sitestoreLocation->city; else echo $sitestoreLocation->state;  ?>')" ><?php echo ucfirst($sitestoreLocation->city) ?><?php $state=null;if(!empty($sitestoreLocation->city)&& !empty($sitestoreLocation->state))$state.=" [";$state.=ucfirst($sitestoreLocation->state);if(!empty($sitestoreLocation->city)&& !empty($sitestoreLocation->state))$state.="] ";echo $state;?></a>
          <?php if(!empty($sitestoreLocation->city)): echo "(" . $sitestoreLocation->count_location . ")"; else: echo "(" . $sitestoreLocation->count_location_state . ")"; endif;?>
        </div>	
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>
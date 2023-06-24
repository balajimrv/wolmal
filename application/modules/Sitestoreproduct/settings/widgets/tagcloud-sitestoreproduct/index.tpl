<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">

  var tagAction = function(tag_id, tag){
    if($('filter_form')) {
      form=document.getElementById('filter_form');
    }else if($('filter_form_tag')){
      form=$('filter_form_tag');
    }   
    form.elements['tag_id'].value = tag_id;
    form.elements['tag'].value = tag;
    if( $('filter_form')) {
      $('filter_form').submit();
    }
		else {
      $('filter_form_tag').submit();
    }
  }
</script>

<form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitestoreproduct_general', true) ?>' style='display: none;'>
	<input type="hidden" id="tag_id" name="tag_id"  value=""/>
  <input type="hidden" id="tag" name="tag"  value=""/>
</form>

<ul class="seaocore_sidebar_list">
	<li>
		<div>
			<?php foreach($this->tag_array as $key => $frequency):?>
				<?php $string =  $this->string()->escapeJavascript($key); ?>
				<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency'])*$this->tag_data['step'] ?>
      
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>, "<?php echo urlencode($key) ?>");' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
      
<!--				<a href='<?php //echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php //echo urlencode($key) ?>&tag_id=<?php //echo $this->tag_id_array[$key] ?>' style="font-size:<?php //echo $step ?>px;" title=''><?php //echo $key ?><sup><?php //echo $frequency ?></sup></a> -->
			<?php endforeach;?>
		</div>		
	</li>
  
	<li>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)): ?>
      <?php echo $this->htmlLink(array('route' => "sitestoreproduct_general", 'action' => 'tagscloud'), $this->translate('Explore Brands &raquo;'), array('class' => 'more_link')) ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('route' => "sitestoreproduct_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'more_link')) ?>
    <?php endif; ?>
  </li>
</ul>


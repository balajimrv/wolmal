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

<div class="sr_sitestoreproduct_quick_specs sr_sitestoreproduct_side_widget">
	
	<?php echo $this->show_fields ?>

 	<?php if(empty($this->review) && $this->contentDetails->content_id && $this->show_specificationlink): ?>
		<div class="sr_sitestoreproduct_more_link">
	  	<a href="javascript:void(0);" onclick='showInfoTab();return false;'>   <?php echo $this->translate("%s",$this->show_specificationtext) . ' &raquo;'; ?></a>
	  </div>
	<?php elseif(!empty($this->review)): ?>
		<div class="sr_sitestoreproduct_more_link">
	  	<a href="<?php echo $this->sitestoreproduct->getHref(). '/tab/' . $this->tab_id?>"><?php echo $this->translate("%s",$this->show_specificationtext) . ' &raquo;'; ?></a>
	  </div>
  <?php endif;?>
</div>

<?php if(empty($this->review) && $this->contentDetails->content_id && $this->show_specificationlink): ?>
	<script type="text/javascript">
		function showInfoTab(){ 
			
			if($('main_tabs')) {
				tabContainerSwitch($('main_tabs').getElement('.tab_' + '<?php echo $this->contentDetails->content_id ?>'));
			}
			
			var params = {
				requestParams :<?php echo json_encode($this->contentDetails->params) ?>,
				responseContainer :$$('.layout_sitestoreproduct_specification_sitestoreproduct')
			}
		
			params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
			en4.sitestoreproduct.ajaxTab.sendReq(params);
			
			if($('main_tabs')) {
				location.hash = 'main_tabs';
			}
		}
	</script>
<?php endif;?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tagscloud.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">  
	var tagAllAction = function(tag_id, tag){
		$('tag').value = tag;
		$('tag_id').value = tag_id;
		$('filter_form_tagscloud').submit();
	}
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; ?>
<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/Adintegration.tpl';
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtagview', 3)  && $review_communityad_integration): ?>
  <div class="layout_right" id="communityad_tagcloud">
    <?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtagview', 3), 'tab' => 'tagcloud', 'communityadid' => 'communityad_tagcloud', 'isajax' => 0));
    ?>
  </div>
<?php endif; ?>

<div class="layout_middle">
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)): ?>
    <h3><?php echo $this->translate("Popular Product Brands"); ?></h3>
    <p class="mtop5"><?php echo $this->translate("Browse the brands created for products by the various members."); ?></p>
  <?php else: ?>
    <h3><?php echo $this->translate("Popular Product Tags"); ?></h3>
    <p class="mtop5"><?php echo $this->translate("Browse the tags created for products by the various members."); ?></p>  
  <?php endif;?>
	<?php if(!empty($this->tag_array)):?>
		<div class="mtop10">
			<?php foreach($this->tag_array as $key => $frequency):?>
				<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency'])*$this->tag_data['step'] ?>
				<a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php echo urlencode($key) ?>&tag_id=<?php echo $this->tag_id_array[$key] ?>' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>&nbsp; 
			<?php endforeach;?>
		</div>
	<?php endif; ?>

</div>
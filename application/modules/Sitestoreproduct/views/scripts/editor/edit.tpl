<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
  $this->headLink()
				->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>

<!--WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS-->
<!--CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()-->
<?php $this->tinyMCESEAO()->addJS();?>

<script type="text/javascript">
	function doRating(element_id, ratingparam_id, classstar) {
   
		$(element_id + '_' + ratingparam_id).getParent().getParent().className= 'sr-es-box rating-box ' + classstar;
		$('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
	}

	function doDefaultRating(element_id, ratingparam_id, classstar) {
   
		$(element_id + '_' + ratingparam_id).getParent().getParent().className= 'sr_sitestoreproduct_eg_rating ' + classstar;
		$('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
	}

</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; ?>

<div class="sr_sitestoreproduct_view_top">
	<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>	
	  <?php echo $this->sitestoreproduct->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->translate('Editor Review') ?>
	</h2>
</div>

<div class='layout_middle'>
	<?php echo $this->form->setAttrib('class', 'sr_sitestoreproduct_review_form global_form')->render($this); ?>

	<div id="addPageLink" class="form-wrapper">
		<div class="form-label">&nbsp;</div>
		<div class="form-element">
			<a href="javascript: void(0);" onclick="return addAnotherPage('');"><b><?php echo $this->translate("Add another page") ?></b>      </a>
		</div>
	</div>

</div>

<script type="text/javascript">

	var optionParent = $('body').getParent().getParent();
	$('addPageLink').inject(optionParent, 'before');

	addPageLink = $('addPageLink');

	var count = 1;

	<?php foreach($this->bodyElementValue as $value):?>
    addAnotherPage(<?php echo Zend_Json_Encoder::encode($value) ?>);
	<?php endforeach;?>

	function addAnotherPage(pre_field_value) {

		//START CATEGORY CODE
		var textarea = document.createElement("textarea");
		textarea.id = 'body_'+count;
		textarea.name = 'body_'+count;

		if(pre_field_value != '') {
			textarea.value = pre_field_value;
		}

		var wrapperDiv = document.createElement("div");
		wrapperDiv.id = 'body_'+count+'-wrapper';
		wrapperDiv.setAttribute("class", "form-wrapper");

		var labelDiv = document.createElement("div");
		labelDiv.id = 'body_'+count+'-label';
		labelDiv.setAttribute("class", "form-label");

		var labelChieldDiv = document.createElement("label");
		labelChieldDiv.innerHTML = '<?php echo $this->translate("Page ")?>' + count + '<?php echo $this->translate(" Summary");?>'

		var elementDiv = document.createElement("div");
		elementDiv.id = 'body_'+count+'-element';
		elementDiv.setAttribute("class", "form-element");

		var fleftDiv = document.createElement("div");
		fleftDiv.setAttribute("class", "fleft");

		textarea.inject(fleftDiv);
		fleftDiv.inject(elementDiv);
		labelDiv.inject(wrapperDiv);
		labelChieldDiv.inject(labelDiv);
		elementDiv.inject(wrapperDiv);
		wrapperDiv.inject(addPageLink, 'before');

		addTinyMCE('body_'+count);

		count = count+1;
	}

	function addTinyMCE(element_id) {
		      <?php echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => $this->upload_url)); ?>
	}

</script>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/editorReviewElements.tpl'; ?>
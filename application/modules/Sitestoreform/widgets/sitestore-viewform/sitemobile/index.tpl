<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
  var submitformajax = 1;
  var store_url = '<?php echo $this->store_url;?>';
</script>

<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php
	  /* Include the common user-end field switching javascript */
	  echo $this->partial('_jsSwitch.tpl', 'fields', array(
	      'topLevelId' => (int) @$this->topLevelId,
	      'topLevelValue' => (int) @$this->topLevelValue
	    ))
	?>
<div id="show_tab_content_child"> 
	<?php 
		echo $this->form->setAttrib('data-ajax', 'true')->render($this) ;
	?>
</div>
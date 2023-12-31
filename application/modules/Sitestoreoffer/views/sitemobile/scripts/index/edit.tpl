<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Coupons","icon"=>"arrow-d")
   );

echo $this->breadcrumb($breadcrumb);
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
?>

<div class="clr">
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">

	sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'block');
      $.mobile.activePage.find('#photo-wrapper').css('display', 'none');
    } else {
      $.mobile.activePage.find('#photo-wrapper').css('display', 'block');
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'none');
    } 
  });

  var endsettingss = '<?php echo $this->sitestoreoffer->end_settings;?>';
  
  function updateTextFields(value) {
		if (value == 0)
    {
      if($("#end_time-wrapper"))
      $("#end_time-wrapper").css("display","none");
    } else if (value == 1)
    { if($("#end_time-wrapper"))
      $("#end_time-wrapper").css("display","block");
    }
  }

  sm4.core.runonce.add(updateTextFields(endsettingss));

</script>
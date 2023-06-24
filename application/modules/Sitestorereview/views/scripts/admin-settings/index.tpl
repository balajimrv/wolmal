<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestorereview/views/scripts/_navigationAdmin.tpl'; ?>

<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<script type="text/javascript">
  $$('input[type=radio]:([name=sitestorereview_proscons])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=sitestorereview_limit_proscons-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });
  if($('sitestorereview_proscons-1')){
    $('sitestorereview_proscons-1').addEvent('click', function(){
      $('sitestorereview_limit_proscons-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    });
  }
  if($('sitestorereview_proscons-0')){
    $('sitestorereview_proscons-0').addEvent('click', function(){
      $('sitestorereview_limit_proscons-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    });
  }
	
  window.addEvent('domready', function() { 
    if($('sitestorereview_proscons-1')) {
      var e4 = $('sitestorereview_proscons-1');
      $('sitestorereview_limit_proscons-wrapper').setStyle('display', (e4.checked ?'block':'none'));
    }
    if($('sitestorereview_proscons-0')) {
      var e5 = $('sitestorereview_proscons-0');
      $('sitestorereview_limit_proscons-wrapper').setStyle('display', (e5.checked?'none':'block'));
    }
  });
	
</script>
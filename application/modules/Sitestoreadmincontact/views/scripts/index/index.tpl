<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>


<script type="text/javascript">
  var composeInstance;
  en4.core.runonce.add(function() {
    var tel = new Element('div', {
      'id' : 'compose-tray',
      'styles' : {
        'display' : 'none'
      }
    }).inject($('submit'), 'before');

    var mel = new Element('div', {
      'id' : 'compose-menu'
    }).inject($('submit'), 'after');
    // @todo integrate this into the composer
    if( !Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad() ) {
      composeInstance = new Composer('body', {
        overText : false,
        menuElement : mel,
        trayElement: tel,
        baseHref : '<?php echo $this->baseUrl() ?>',
        hideSubmitOnBlur : false,
        allowEmptyWithAttachment : false,
        submitElement: 'submit',
        type: 'message'
      });
    }
  });
</script>

<script type="text/javascript">

window.addEvent('domready', function() {

  if(document.getElementById('compose-menu')) {
    var facebook_toggle = document.getElementById('compose-menu').getElement('.composer_facebook_toggle');
    facebook_toggle.style.display = "none";
    var twitter_toggle = document.getElementById('compose-menu').getElement('.composer_twitter_toggle');
    twitter_toggle.style.display = "none";
  }

});

</script>


<?php if(!empty($this->results)) :?>


<?php foreach( $this->composePartials as $partial ): ?>
  <?php echo $this->partial($partial[0], $partial[1]) ?>
<?php endforeach; ?>

<?php echo $this->form->setAttrib('class', 'global_form_popup sitestoreadmincontact_message_create')->render($this) ?>
<?php else:?>
<div class='clear sitestore_settings_form'>
  <div class='global_form_popup'>
    <div class="tip">
	  	<span><?php echo $this->translate('You can not compose a message because no Stores have been created yet.'); ?></span> 
	  </div>
  </div>
</div>
<?php endif;?>


<style type="text/css">
.sitestoreadmincontact_message_create .form-label label{
	font-weight:bold;
	margin:10px 0 5px;
	float:left;
	clear:both;	
}
.sitestoreadmincontact_message_create .form-element{
	clear:both;	
}
.sitestoreadmincontact_message_create .form-element input[type="text"]{
	width:200px;	
}
.sitestoreadmincontact_message_create .compose-content {
	min-height: 4em;
	width: 400px;
}
.sitestoreadmincontact_message_create #compose-menu a.buttonlink{
	margin-right:10px;
	margin-top:10px;
}
</style>



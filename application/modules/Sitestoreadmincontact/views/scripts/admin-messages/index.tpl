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
<script type="text/javascript">
  var package_id='';
  var categories_id='';
  function getValues() {
    if($('admincontact').packages) {
      for (i=0; i<$('admincontact').packages.length;i++)
      {
      if ($('admincontact').packages[i].selected)
       package_id = package_id  + $('admincontact').packages[i].value + ',';   
      }
    }
    
    if($('admincontact').categories) {
      for (i=0; i<$('admincontact').categories.length;i++)
      {
      if ($('admincontact').categories[i].selected)
        categories_id = categories_id  + $('admincontact').categories[i].value + ',';   
      }
    }
    
    <?php
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $url = $view->url(array('action' => 'index'), 'sitestoreadmincontact_messages_general', true);
    ?>
        
    var url = '<?php echo $url; ?>';
    <?php if (Engine_Api::_()->sitestore()->hasPackageEnable())  :?>
      url = url + '?package_id=' + package_id + '&categories_id=' + categories_id;
    <?php else: ?>
      url = url + '?categories_id=' + categories_id ;
    <?php endif; ?>  
    package_id = '';    
    categories_id='';
    Smoothbox.open(url);
  }

</script>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear sitestore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: product-document.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript" >
  var submitformajax = 0;
   
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
  <?php $url_back = $this->url(array('action' => 'product-document', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
   <a href="javascript:void(0);" onclick='showAjaxBasedContent("<?php echo $url_back; ?>")' class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Documents Page'); ?></a>
  <?php echo $this->form->render($this); ?>
</div>
</div>


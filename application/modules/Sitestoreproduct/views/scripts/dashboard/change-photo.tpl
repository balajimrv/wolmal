<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: change-photo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="sr_sitestoreproduct_dashboard_content">
    <?php
      if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
        echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore' => $this->sitestore));
      endif;
      ?>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
  function removePhotoProduct(url) {
    window.location.href=url;
  }
</script>
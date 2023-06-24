<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editstyle.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript" >
  var submitformajax = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
	<div class="sitestoreproduct_editstyle">
		<?php echo $this->form->render($this); ?>
	</div>
</div>
</div>

<script type="text/javascript" >
  miniLoadingImage = 1;
</script>
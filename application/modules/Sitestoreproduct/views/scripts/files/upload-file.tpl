<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-file.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;

    if( $this->uploadType == 'sample' ):
      $type = 'sample';
      $sample_class = "active";
      $main_class = "";
      $backLinkText = 'Back to Manage Sample Files';
    else:
      $type = 'index';
      $sample_class = "";
      $main_class = "active";
      $backLinkText = 'Back to Manage Main Files';
    endif;
  ?>
  
  <h3><?php echo $this->translate("Downloadable Information") ?></h3>
<!--  <p><?php echo $this->translate("Below, you can upload and manage main files and sample files for this product.") ?></p><br/>

  <div class="tabs mbot10">
    <ul class="navigation sr_sitestoreproduct_navigation_common">
      <li class="<?php echo $main_class ?>">
        <a class="" href="<?php echo $this->url(array('action' => 'index', 'product_id'=>$this->product_id, 'type' => 'main'), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("Main Files") ?></a>
        <p><?php echo $this->translate("Here, you can upload and manage all the main files for this product. Below, you can also enable / disable files.") ?></p>
      </li>
      <li class="<?php echo $sample_class ?>">
        <a class="" href="<?php echo $this->url(array('action' => 'sample', 'product_id'=>$this->product_id, 'type' => 'sample'), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("Sample Files") ?></a>
        <p><?php echo $this->translate("Here, you can upload and manage all the sample files for this product. Below, you can also enable / disable files.") ?></p>
      </li>
    </ul>
  </div><br/>-->

  <a  class="sr_sitestoreproduct_item_icon_back buttonlink" href="<?php echo $this->url(array('action' => $type, 'product_id' => $this->product_id), 'sitestoreproduct_files', true) ?>" ><?php echo $this->translate("%s", $backLinkText) ?></a>
  <br />
  
  <?php if(!empty($this->error)) : ?>
    <div class="tip">
      <span>
        <?php echo $this->error ?>
      </span>
    </div>
  <?php else : ?>
    <div class="sitestoreproduct_create_product mtop10 sitestore_upload_product">
    <?php echo $this->form->render($this) ?>
    </div>
  <?php endif; ?>
</div>
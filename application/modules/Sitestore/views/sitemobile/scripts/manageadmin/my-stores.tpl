<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: myadminstores.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>

<div class="sitestore_view_select"> 
<h3 class="sitestore_mystore_head"><?php echo $this->translate('Stores I Admin'); ?></h3>
</div>
  <?php
  $sitestore_approved = Zend_Registry::isRegistered('sitestore_approved') ? Zend_Registry::get('sitestore_approved') : null;
  ?>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>	   
<div class="sm-content-list">
  <ul data-role="listview" data-inset="false">
			<?php foreach ($this->paginator as $sitestore): ?>
				<li>
          <a href="<?php echo $sitestore->getHref();?>">
          <!--ADD PARTIAL VIEWS -->
          <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/sitemobile_partial_views.tpl';?>
            <?php $expiry=Engine_Api::_()->sitestore()->getExpiryDate($sitestore);
            if($expiry !=="Expired" && $expiry !== $this->translate('Never Expires'))
              echo $this->translate("Expiration Date: ");?>
            <p style="color: green;">
           <?php  echo $expiry; ?>
            </p>
			    </a>       
        </li>
			<?php endforeach; ?>
</ul>
</div>
  <?php else: ?>
    <div class="tip">
      <span> <?php if (!empty($sitestore_approved)) {
      echo $this->translate('You do not have any stores yet.');
    } else {
      echo $this->translate($this->store_manage_msg);
    } ?>
      </span>
    </div>
<?php endif; ?>
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>

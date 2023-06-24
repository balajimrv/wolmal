<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitestore_viewstores_head">
  <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitestore->__toString() ?>
  </h2>
</div>
<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
        <h3><?php echo $this->translate('Delete Store?'); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the Store with the title "%1s" last modified %2s? It will not be recoverable after being deleted.', $this->sitestore->title, $this->timestamp($this->sitestore->modified_date)); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete'); ?></button>
          <?php echo $this->translate('or'); ?> <a href='<?php echo $this->url(array('action' => 'manage'), 'sitestore_general', true) ?>'><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>
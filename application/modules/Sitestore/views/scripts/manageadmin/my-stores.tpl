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
<script type="text/javascript">

  function smoothboxstore(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
<script type="text/javascript">

  var storeAction =function(store){
    $('store').value = store;
    $('filter_form').submit();
  }
</script>
  <?php //include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
  <?php //echo $this->form->render($this) ?>

<div class='layout_middle'>
<!--    <h3 class="sitestore_mystore_head"><?php //echo $this->translate('Stores I Admin'); ?></h3>-->
  
  <?php
  $sitestore_approved = Zend_Registry::isRegistered('sitestore_approved') ? Zend_Registry::get('sitestore_approved') : null;
  $renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.renew.email', 2))));
  ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="seaocore_browse_list">
      <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <div class='seaocore_browse_list_photo'>
						<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal')) ?> 
          </div>

          <div class='seaocore_browse_list_options'>
            <?php if ($this->can_edit): ?>
              <?php if (empty($item->declined)): ?>
                <a href='<?php echo $this->url(array('store_id' => $item->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php if (!empty($sitestore_approved)) {
                 echo $this->translate('Dashboard');
              } else {
                  echo $this->translate($this->store_manage);
              } ?></a>

							<?php if ($item->draft == 0)
								echo $this->htmlLink(array('route' => 'sitestore_publish', 'store_id' => $item->store_id), $this->translate('Publish Store'), array('class' => 'buttonlink icon_sitestore_publish', 'onclick' => 'smoothboxstore(this);return false')) ?>
							<?php if (!$item->closed): ?>
								<a href='<?php echo $this->url(array('store_id' => $item->store_id, 'closed' => 1, 'check' => 1), 'sitestore_close', true) ?>' class='buttonlink icon_sitestores_close'><?php echo $this->translate('Close Store'); ?></a>
							<?php else: ?>
								<a href='<?php echo $this->url(array('store_id' => $item->store_id, 'closed' => 0, 'check' => 1), 'sitestore_close', true) ?>' class='buttonlink icon_sitestores_open'><?php echo $this->translate('Open Store'); ?></a>
							<?php endif; ?>
            <?php endif; ?>
						<?php endif; ?>
						<?php if ($this->can_delete): ?>
											<a href='<?php echo $this->url(array('store_id' => $item->store_id), 'sitestore_delete', true) ?>' class='buttonlink icon_sitestores_delete'><?php echo $this->translate('Delete Store'); ?></a>
										<?php endif; ?>
										<?php if (Engine_Api::_()->sitestore()->canShowPaymentLink($item->store_id)): ?>
											<div class="tip">
												<span>
													<a href='javascript:void(0);' onclick="submitSession(<?php echo $item->store_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
												</span>
											</div>
						<?php endif; ?>

            <?php if (Engine_Api::_()->sitestore()->canShowRenewLink($item->store_id)): ?>
              <div class="tip">
                <span>
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->store_id ?>)"><?php echo $this->translate('Renew Store'); ?></a>
                </span>
              </div>
              <?php endif; ?>
          </div>

					<?php  $this->partial()->setObjectKey('sitestore');
					echo $this->partial('partial_views.tpl', $item); ?>

							<?php
							// Not mbstring compat
							echo substr(strip_tags($item->body), 0, 350);
							if (strlen($item->body) > 349)
								echo "...";
							?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <div class="tip">
      <span> <?php if (!empty($sitestore_approved)) {
      echo $this->translate('You do not have any stores yet.');
    } else {
      echo $this->translate($this->store_manage_msg);
    } ?>
  <?php if ($this->can_create): ?>
    <?php
    if (Engine_Api::_()->sitestore()->hasPackageEnable()):
      $createUrl = $this->url(array('action' => 'index'), 'sitestore_packages');
    else:
      $createUrl = $this->url(array('action' => 'create'), 'sitestore_general');
    endif;
    ?>
    <?php echo $this->translate('Get started by %1$screating%2$s a new store.', '<a href=\''. $createUrl. '\'>', '</a>'); ?>
  <?php endif; ?>
      </span>
    </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestore")); ?>
</div>

<form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
  <input type="hidden" name="store_id_session" id="store_id_session" />
</form>

<script type="text/javascript">
  function submitSession(id){
    
    document.getElementById("store_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>
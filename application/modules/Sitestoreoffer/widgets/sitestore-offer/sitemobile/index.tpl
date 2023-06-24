<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $viewer_id = $this->viewer->getIdentity(); ?>
<?php if (!empty($viewer_id)): ?>
  <?php date_default_timezone_set($this->viewer->timezone); ?>
<?php endif; ?>

<?php
//include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if ($this->paginator->getTotalItemCount()): ?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestoreoffer_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>
<div class="sm-content-list">	
  <ul data-role="listview" data-inset="false" >
    <?php foreach ($this->paginator as $sitestore): ?>
      <li data-icon="arrow-r">
        <a href="<?php echo $sitestore->getHref(); ?>">
          <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
          <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
          $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.sitemobile-profile-sitestoreoffers', $sitestore->store_id, $layout); ?>
          <?php if (!empty($sitestore->photo_id)): ?>
            <?php echo $this->itemPhoto($sitestore, 'thumb.icon'); ?>
          <?php else: ?>
            <?php echo "<img src='" . $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_icon.png' alt='' />"; ?>
          <?php endif; ?>
          <h3><?php echo $sitestore->title; ?></h3>
          <?php $item = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
          <p><?php echo $this->translate("in "); ?>
            <b><?php echo $sitestore->sitestore_title ?></b>
          </p>
          <p>
            <?php echo $this->translate('End date:'); ?>
            <?php if ($sitestore->end_settings == 1): ?><?php echo $this->translate(gmdate('M d, Y', strtotime($sitestore->end_time))) ?></span><?php else: ?><?php echo $this->translate('Never Expires'); ?><?php endif; ?>
          </p>
          <?php $today = date("Y-m-d H:i:s"); ?>
          <?php $claim_value = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer')->getClaimValue($this->viewer_id, $sitestore->offer_id, $sitestore->store_id); ?>
          
           <?php if(false):?>
          <?php if ($sitestore->claim_count == -1 && ($sitestore->end_time > $today || $sitestore->end_settings == 0)): ?>
            <?php $show_offer_claim = 1; ?>
          <?php elseif ($sitestore->claim_count > 0 && ($sitestore->end_time > $today || $sitestore->end_settings == 0)): ?>
            <?php $show_offer_claim = 1; ?>
          <?php else: ?>
            <?php $show_offer_claim = 0; ?>
          <?php endif; ?>
          <p>
            <?php echo $sitestore->claimed . ' ' . $this->translate('claimed'); ?>
            <?php if ($sitestore->claim_count != -1): ?>
            -
            <?php echo $sitestore->claim_count . ' ' . $this->translate('claims left') ?>
            <?php endif; ?>
          </p>
          <?php endif; ?>

        <?php if(false):?>
        <p class="ui-li-aside"><strong>
            <?php if (!empty($show_offer_claim) && empty($claim_value)): ?>
              <?php
              $request = Zend_Controller_Front::getInstance()->getRequest();
              $urlO = $request->getRequestUri();
              $request_url = explode('/', $urlO);
              $param = 1;
              if (empty($request_url['2'])) {
                $param = 0;
              }
             // $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
              $currentUrl = urlencode($urlO);
              ?>

              <?php if (!empty($this->viewer_id)): ?>
                <?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />' . $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'getoffer', 'id' => $sitestore->offer_id), $this->translate('Get Coupon'), array('class' => 'smoothbox'));
                ?>
              <?php else: ?>
                <?php
                $offer_tabinformation = $this->url(array('action' => 'getoffer', 'id' => $sitestore->offer_id, 'param' => $param, 'request_url' => $request_url['1']), 'sitestoreoffer_general') . "?" . "return_url=" . $return_url . $_SERVER['HTTP_HOST'] . $currentUrl;
                $title = $this->translate('Get Coupon');
                echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />' . "<a href=$offer_tabinformation>$title</a>";
                ?>
              <?php endif; ?>
              <?php elseif (!empty($claim_value) && !empty($show_offer_claim) || ($sitestore->claim_count == 0 && $sitestore->end_time > $today && !empty($claim_value))): ?>
              <?php echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />' . $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'resendoffer', 'id' => $sitestore->offer_id), Zend_Registry::get('Zend_Translate')->_('Resend Coupon'), array('class' => 'smoothbox')); ?>
    <?php else: ?>
                <b><?php echo $this->translate('Expired'); ?></b>
    <?php endif; ?>
          </strong></p>
          <?php endif;?>
          </a>
      </li>
  <?php endforeach; ?>
  </ul>
</div>
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
	$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitestore';
	$RESOURCE_TYPE = 'sitestore_store';
	$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.sponsored', 1);
	$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
	<div class="sitestore_view_select">
	<h3 class="sitestore_mystore_head"><?php echo $this->translate('Stores I Like'); ?></h3>
  </div>
<?php if ($this->paginator->count() > 0): ?>
<div class="sm-content-list">
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>	   
<ul data-role="listview" data-inset="false">
			<?php foreach ($this->paginator as $sitestore): ?>
				<li>
          <a href="<?php echo $sitestore->getHref();?>">
          <!--ADD PARTIAL VIEWS -->
            <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/sitemobile_partial_views.tpl';?>
          
            <p><?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?></p>
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
			<?php $translatestore = "<a href=".$this->url(array('action' => 'index'), 'sitestore_general', true).">" . $this->translate("Explore stores") . "</a>";
			echo $this->translate("You have not liked any stores yet.");?>
		</span>
	</div>
  <?php endif; ?>



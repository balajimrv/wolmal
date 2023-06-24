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
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorediscussion/externals/styles/style_sitestorediscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '' , array('align'=>'left'))) ?>
	<h2>	
	  <?php echo $this->sitestore->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink(array( 'route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
  </h2>  
</div>
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addiscussionview', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
	<div class="layout_right" id="communityad_topicindex">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addiscussionview', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_topicindex'))?>
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="sitestore_discussion_view">
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <br />
	    <?php echo $this->paginationControl($this->paginator) ?>
	    <br />
	  </div>
	<?php endif; ?>
	
	<ul class="sitestore_sitestores">
	  <?php foreach( $this->paginator as $topic ):
	      $lastpost = $topic->getLastPost();
	      $lastposter = $topic->getLastPoster();
	      ?>
	    <li>
	      <div class="sitestore_sitestores_replies">
	        <span>
	          <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
	        </span>
	        <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
	      </div>
	      <div class="sitestore_sitestores_lastreply">
	        <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
	        <div class="sitestore_sitestores_lastreply_info">
	          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
	          <br />
	          <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitestore_sitestores_lastreply_info_date')) ?>
	        </div>
	      </div>
	      <div class="sitestore_sitestores_info">
	        <h3<?php if( $topic->sticky ): ?> class='sitestore_sitestores_sticky'<?php endif; ?>>
	          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            <?php if(($resource=$topic->getResource())!=null):?>
            <span style="float: right;">
            <?php echo $this->translate("In ".$resource->getMediaType().":") ?>
            <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle()) ?>
          </span>
          <?php endif;?>
	        </h3>
          
	        <div class="sitestore_sitestores_blurb">
	          <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
	        </div>
	      </div>
	    </li>
	  <?php endforeach; ?>
	</ul>
	
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <?php echo $this->paginationControl($this->paginator) ?>
	  </div>
	<?php endif; ?>
</div>	
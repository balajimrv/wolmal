<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>
<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/Adintegration.tpl';
?>

<div class="sr_sitestoreproduct_view_top">
	<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>	
		<?php echo $this->sitestoreproduct->__toString() ?>	
		<?php echo $this->translate('&raquo; '); ?>
		<?php echo $this->htmlLink($this->sitestoreproduct->getHref(array('tab'=> $this->tab_selected_id)), $this->translate('Discussions')) ?>
	</h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtopicview', 3) && $review_communityad_integration): ?>
	<div class="layout_right" id="communityad_adtopicview">
		<?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtopicview', 3), 'tab' => 'adtopicview', 'communityadid' => 'communityad_adtopicview', 'isajax' => 0)); ?>
	</div>
<?php endif; ?>

<div class="layout_middle">
  <div class="sitestoreproduct_sitestoreproducts_options">

   <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->translate("Back to Product"), array('class' => 'buttonlink icon_back')) ?>
    <?php 
      if ($this->can_post) 
      {
        echo $this->htmlLink(array('route' => "sitestoreproduct_extended", 'controller' => 'topic', 'action' => 'create', 'subject' => $this->sitestoreproduct->getGuid(), 'content_id' => $this->tab_selected_id ), $this->translate('Post New Topic'), array('class' => 'buttonlink icon_sitestoreproduct_post_new')) ;
      }
    ?>
  </div>

  <?php if( $this->paginator->count() > 1 ): ?>
    <div>
      <br />
      <?php echo $this->paginationControl($this->paginator) ?>
      <br />
    </div>
  <?php endif; ?>

  <ul class="sitestoreproduct_sitestoreproducts">
    <?php foreach( $this->paginator as $topic ): ?>
      <?php 
          $lastpost = $topic->getLastPost();
          $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
      ?>
      <li>

        <div class="sitestoreproduct_sitestoreproducts_replies seaocore_txt_light">
          <span>
            <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
          </span>
          <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
        </div>

        <div class="sitestoreproduct_sitestoreproducts_lastreply">
          <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
          <div class="sitestoreproduct_sitestoreproducts_lastreply_info">
            <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
            <br />
            <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitestoreproduct_sitestoreproducts_lastreply_info_date seaocore_txt_light')) ?>
          </div>
        </div>

        <div class="sitestoreproduct_sitestoreproducts_info">
          <h3<?php if( $topic->sticky ): ?> class='sitestoreproduct_sitestoreproducts_sticky'<?php endif; ?>>
            <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
          </h3>
          <div class="sitestoreproduct_sitestoreproducts_blurb">
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
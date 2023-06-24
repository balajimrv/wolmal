<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorereview/externals/styles/style_sitestorereview.css')
	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestorereview/externals/styles/show_star_rating.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitestore->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Reviews')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->sitestorereview->title ?>
	</h2>
</div>
<div class='layout_left'>
  <div class='sitestorereviews_gutter'>
		<?php if(!empty($photo_review)):?>
				<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
			<?php else:?>
				<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), $this->itemPhoto($this->sitestore, 'thumb.normal')) ?>
		<?php endif;?>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'sitestorereviews_gutter_name')) ?>
  </div>  
  <ul class="sitestorereviews_gutter_options quicklinks">
		<li>
			<?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Back to Store'),array('class'=>'buttonlink  icon_sitestorereview_back')) ?>
		</li>
    <?php if($this->viewer_id == $this->sitestorereview->owner_id): ?>
    	<li>  
      	<?php echo $this->htmlLink(array('route' => 'sitestorereview_edit', 'review_id' => $this->sitestorereview->review_id, 'store_id' => $this->sitestorereview->store_id, 'slug' => $this->sitestore_slug, 'tab' => $this->tab_selected_id), $this->translate('Edit Review'), array('class' => 'buttonlink icon_sitestores_edit')) ?>
    	</li>
		<?php endif; ?>
		<?php if($this->viewer_id == $this->sitestorereview->owner_id || $this->level_id == 1): ?>
    	<li>
    		<?php echo $this->htmlLink(array('route' => 'sitestorereview_delete', 'review_id' => $this->sitestorereview->review_id, 'store_id' => $this->sitestorereview->store_id, 'slug' => $this->sitestore_slug, 'tab' => $this->tab_selected_id), $this->translate('Delete Review'), array('class'=>'buttonlink  icon_sitestores_delete')) ?>
    	</li>
    <?php endif; ?>

		<?php if($this->review_report == 1 && !empty($this->viewer_id)): ?>
			<li>
				<?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->report->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox buttonlink seaocore_icon_report')); ?>
			</li>
		<?php endif;?>	

		<!-- Suggestion work start from here -->
		<?php if( !empty($this->reviewSuggLink) ): ?>	
			<li>
				<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->sitestorereview->review_id, 'sugg_type' => 'store_review'), $this->translate('Suggest to Friends'), array(
					'class'=>'buttonlink  icon_store_friend_suggestion smoothbox')) ?>
			</li>
		<?php endif; ?>
		<!-- Suggestion work end from here -->
	</ul>
</div>
<div class='layout_middle'>
	<ul class="sitestorereviews_view">
    <li>
      <h3> 
      	<?php echo $this->sitestorereview->title; ?> 
      </h3>
      <div class="sitestorereviews_view_stats">
      
       
      	<?php echo $this->translate('Posted by %s %s', $this->sitestorereview->getOwner()->toString(), $this->timestamp($this->sitestorereview->creation_date)) ?>
      </div>
      <div class="sitestorereviews_view_stats"> 
      	<?php echo $this->translate(array('%s comment', '%s comments', $this->sitestorereview->comment_count), $this->locale()->toNumber($this->sitestorereview->comment_count)) ?>,
      	<?php echo $this->translate(array('%s view', '%s views', $this->sitestorereview->view_count), $this->locale()->toNumber($this->sitestorereview->view_count)) ?>,
				<?php echo $this->translate(array('%s like', '%s likes', $this->sitestorereview->like_count), $this->locale()->toNumber($this->sitestorereview->like_count)) ?>
      </div>
        <!--FACEBOOK LIKE BUTTON START HERE-->
       <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="mtop10">
                  <script type="text/javascript">
                    var fblike_moduletype = 'sitestorereview_review';
		                var fblike_moduletype_id = '<?php echo $this->sitestorereview->review_id ?>';
                  </script>
                  <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
                </div>
            
            <?php } ?>
          <?php endif; ?>
     <?php endif; ?>
       
			<div class="sitestorereviews_view_body">
      	<?php echo nl2br($this->sitestorereview->body) ?>
      </div>
        <div class="tip">
					<span>
					<?php echo $this->translate("Like this review if you find it useful."); ?>
					</span>
				</div>	
		</li>
  </ul>

	<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>
</div>

<style type="text/css">
.rating{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/show-star-matrix.png);}
</style>
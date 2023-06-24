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
?>
<!--<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitestore->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Reviews')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->sitestorereview->title ?>
	</h2>
</div>-->
<div class='layout_left'>
  <div class='sitestorereviews_gutter'>
  	<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
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
<div class='layout_right'>
	<div class="generic_layout_container">
		<h3><?php echo $this->translate("Review Details"); ?></h3>
		<ul class="sitestore_sidebar_list sitestorereview_sidebar mbot15">
			<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($this->sitestorereview->review_id); ?>
			<?php foreach($ratingData as $reviewcat): ?>
				<li class="sitestorereview_overall_rating">
					<?php if(!empty($reviewcat['reviewcat_name'])): ?>
						<?php 
							$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcat['rating'], 'box');
							$rating_value = $showRatingImage['rating_value'];
						?>
					<?php else:?>
						<?php
							$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcat['rating'], 'star');
							$rating_value = $showRatingImage['rating_value'];
							$rating_valueTitle = $showRatingImage['rating_valueTitle'];
						?>
					<?php endif; ?>
					<?php if(!empty($reviewcat['reviewcat_name'])): ?>
						<div class="review_cat_rating">
							<ul class='rating-box-small <?php echo $rating_value; ?>'>
								<li id="1" class="rate one">1</li>
								<li id="2" class="rate two">2</li>
								<li id="3" class="rate three">3</li>
								<li id="4" class="rate four">4</li>
								<li id="5" class="rate five">5</li>
							</ul>
						</div>
					<?php else:?>
						<div class="review_cat_rating">
							<ul title="<?php echo $rating_valueTitle.$this->translate(" rating"); ?>" class='rating <?php echo $rating_value; ?>'>
								<li id="1" class="rate one">1</li>
								<li id="2" class="rate two">2</li>
								<li id="3" class="rate three">3</li>
								<li id="4" class="rate four">4</li>
								<li id="5" class="rate five">5</li>
							</ul>
						</div>
					<?php endif;?>
					<?php if(!empty($reviewcat['reviewcat_name'])): ?>
						<div class="review_cat_title">
							<?php echo $this->translate($reviewcat['reviewcat_name']); ?>
						</div>
					<?php else:?>
						<div class="review_cat_title">
							<?php echo $this->translate("Overall Rating");?>
						</div>	
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1)):?>
				<li>
					<?php echo "<b>".$this->translate("Pros: ")."</b>".$this->viewMore($this->sitestorereview->pros) ?>
				</li>
				<li>	
					<?php echo "<b>".$this->translate("Cons: ")."</b>".$this->viewMore($this->sitestorereview->cons) ?>
				</li>	
			<?php endif;?>
			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
				<li>
				<?php if($this->sitestorereview->recommend):?>
					<?php echo $this->translate("Member's Recommendation: <b>Yes</b>"); ?>
				<?php else: ?>
					<?php echo $this->translate("Member's Recommendation: <b>No</b>"); ?>
				<?php endif;?>
			<?php endif;?>
			</li>
		</ul>
	</div>	

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
                  <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
                </div>
            
            <?php } ?>
          <?php endif; ?>
     <?php endif; ?>
       
			<div class="sitestorereviews_view_body">
      	<?php echo nl2br($this->sitestorereview->body) ?>
      </div>
		</li>
  </ul>
  <div class="tip">
  	<span>
  	<?php echo $this->translate("Like this review if you find it useful."); ?>
  	</span>
  </div>	

	<?php echo $this->action("list", "comment", "core", array("type"=>"sitestorereview_review", "id"=> $this->sitestorereview->review_id)) ?>

</div>

<style type="text/css">
.rating{background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/show-star-matrix.png);}
</style>
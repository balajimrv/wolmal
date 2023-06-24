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
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css')
?>


<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideTabs.js');
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestorediscussion/externals/styles/style_sitestorediscussion.css')
?>
<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">
	  var storeDiscussionStore = <?php echo sprintf('%d', $this->paginators->getCurrentPageNumber()) ?>;
		var paginateStoreDiscussions = function(store) {
			var url = en4.core.baseUrl + 'widget/index/mod/sitestore/name/discussion-sitestore';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'store' : store,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}
			}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
			});
		}
	</script>
<?php endif;?>


<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_discussion">
			<?php echo $this->translate($this->sitestore->getTitle());?><?php echo $this->translate("'s Discussions");?>
		</div>
	<?php endif; ?>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addicussionwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
		<div class="layout_right" id="communityad_discussion">
			<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addicussionwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_discussion'))?>
		</div>
		<div class="layout_middle">
	<?php endif; ?>
	<?php if( $this->canPost): ?>
	  <div class="seaocore_add">
	      <?php echo $this->htmlLink(array(
	        'route' => 'sitestore_extended',
	        'controller' => 'topic',
	        'action' => 'create',
	        'subject' => $this->subject()->getGuid(),
	        'store_id' => $this->store_id,
	        'tab'=> $this->identity_temp
	      ), $this->translate('Post New Topic'), array(
	        'class' => 'buttonlink icon_sitestore_post_new'
	      )) ?>
	  </div>
	<?php endif; ?>
	<?php if( $this->paginators->getTotalItemCount() > 0 ): ?>
	  <div class="sitestore_profile_discussion">
	    <ul class="sitestore_sitestores">
	      <?php foreach( $this->paginators as $topic ):
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
                  <span class="fright">
                    <span><?php echo $this->translate("In ".$resource->getMediaType().":") ?></span>                    
                    
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
	  </div>
	  <?php if( $this->paginators->count() > 1 ): ?>
    <div>
      <?php if( $this->paginators->getCurrentPageNumber() > 1 ): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateStoreDiscussions(storeDiscussionStore - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->paginators->getCurrentPageNumber() < $this->paginators->count() ): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateStoreDiscussions(storeDiscussionStore + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

	<?php else: ?>
	  <div class="tip">
	    <span>
		    <?php echo $this->translate('No discussion topics have been posted in this Store yet.'); ?>
				<?php if($this->canPost):
		      $show_link = $this->htmlLink(array('route' => 'sitestore_extended', 'controller' => 'topic', 'action' => 'create','subject' => $this->subject()->getGuid(),'store_id' => $this->store_id, 'tab'=> $this->identity_temp),$this->translate('here'));
					$show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to start a discussion.');
					$show_label = sprintf($show_label, $show_link);
					echo $show_label;
		       endif;?>
	    </span>
	  </div>
	<?php endif;?>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addicussionwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
		</div>
	<?php endif;?>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
  var discussion_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addicussionwidget', 3);?>';
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore) ?>';
	var execute_Request_Discusssion = '<?php echo $this->show_content;?>';
  var is_ajax_divhide = '<?php echo $this->isajax;?>';
  var show_widgets = '<?php echo $this->widgets ?>';
  var DiscusssiontabId = '<?php echo $this->module_tabid;?>';
  var DiscusssionTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  var store_communityad_integration = '<?php echo $store_communityad_integration; ?>';
   if (DiscusssionTabIdCurrent == DiscusssiontabId) {
   	 if(store_showtitle != 0)  {
   	 	 if($('profile_status') && show_widgets == 1) {
		     $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Discussions ');?></h2>";
   	 	 }
   	 	 if($('layout_discussion')) {
			   $('layout_discussion').style.display = 'block';
			 }
   	 }
		 hideWidgetsForModule('sitestorediscussion');
 	   prev_tab_id = '<?php echo $this->content_id; ?>';
 	   prev_tab_class = 'layout_sitestore_discussion_sitestore';
 	   execute_Request_Discusssion = true;
 	   hideLeftContainer (discussion_ads_display, store_communityad_integration, adwithoutpackage);
   }
  else if (is_ajax_divhide != 1) {
  	if($('global_content').getElement('.layout_sitestore_discussion_sitestore')) {
			$('global_content').getElement('.layout_sitestore_discussion_sitestore').style.display = 'none';
	 }

  }
$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
	$('global_content').getElement('.layout_sitestore_discussion_sitestore').style.display = 'block';
  if(store_showtitle != 0) {
  	if($('profile_status') && show_widgets == 1) {
		  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Discussions ');?></h2>";
  	}
 	}
	hideWidgetsForModule('sitestorediscussion');
	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
  if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
    $$('.'+ prev_tab_class).setStyle('display', 'none');
  }

	if (prev_tab_id != '<?php echo $this->content_id; ?>') {
		execute_Request_Discusssion = false;
		prev_tab_id = '<?php echo $this->content_id; ?>';
		prev_tab_class = 'layout_sitestore_discussion_sitestore';
	}

	if(execute_Request_Discusssion == false) {
		ShowContent('<?php echo $this->content_id; ?>', execute_Request_Discusssion, '<?php echo $this->identity_temp?>', 'discussion', 'sitestore', 'discussion-sitestore', store_showtitle, 'null', discussion_ads_display, store_communityad_integration, adwithoutpackage);
		execute_Request_Discusssion = true;    		
	}   	

	if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1);?>' && discussion_ads_display == 0)
{setLeftLayoutForStore();}
 }); 
</script>
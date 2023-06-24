<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html id="smoothbox_window" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
	<base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />
	<title></title>
	<?php	 $this->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
			->appendHttpEquiv('Content-Language', 'en-US');  ?>
	<?php echo $this->headMeta()->toString()."\n" ?>
	<?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>



	<?php if($this->coreModuleVersion < '4.1.7'):  ?>
		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.4-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.4.4-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
	<?php elseif ($this->coreModuleVersion < '4.2.5'):  ?>
 		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-more-1.4.0.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
	<?php else: ?>
		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.5-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
		<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.5.1-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
	<?php endif; ?>



	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/core.js?c=1'; ?>"></script>
	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl.'externals/smoothbox/smoothbox4.js?c=1'; ?>"></script>
	<!-- For update tab is show then css file include. -->
	<?php if($this->value['streamupdatefeed']=="true"): ?>
	<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Activity/externals/scripts/core.js'?>"></script>
	<?php endif ;?>
</head>

<body>
<?php if ( empty($this->display) ): ?>
	<?php return; ?>
<?php endif; ?>

<script type="text/javascript">
var current_active = '<?php echo $this->current_active; ?>';
var previous_active = '<?php echo $this->current_active; ?>';
var current_id = '<?php echo $this->current_activetab; ?>';
var previous_id = '<?php echo $this->current_activetab; ?>';
var flag_div_id = '';

var showmoduleResult = function(type, active) {
	current_id = type;
	current_active = active;
	$(current_active ).addClass('active');
	$(current_id).style.display = 'block';
	if (current_id != previous_id)	{
		$(previous_id).style.display = 'none';
	}
	if (current_active != previous_active)	{
		$(previous_active ).removeClass('active');
	}
	if( (flag_div_id != '') && (current_active != flag_div_id) ){
		$(flag_div_id).removeClass('active');
	}
	previous_id = type;
	previous_active = active;
}
</script>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/sitestorelikebox.css');
//$this->headLink()->appendStylesheet( $this->baseUrl( '/application/modules/Sitestorelikebox/externals/styles/sitestorelikebox.css' )) ; ?>

<!-- According to color scheme condition light and dark css will append. -->
<?php if (!Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.colorschme' , 1 )): ?>
	<?php	$colorScheme = Engine_Api::_()->getApi('settings', 'core')->getSetting('likebox.default.colorschme', 'light'); ?>
	<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . "application/modules/Sitestorelikebox/externals/styles/$colorScheme.css");

//$this->headLink()->appendStylesheet( $this->baseUrl( "/application/modules/Sitestorelikebox/externals/styles/$colorScheme.css" )) ;	?>
<?php else: ?>
	<?php // CHECK FOR WHAT CSS APPLY ON LIKE BOX ?>
	<?php if($this->value['colorscheme']=="dark"):  ?>
		<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/dark.css');
//$this->headLink()->appendStylesheet( $this->baseUrl( '/application/modules/Sitestorelikebox/externals/styles/dark.css' )) ;	?>
	<?php else:?>
		<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestorelikebox/externals/styles/light.css');

//$this->headLink()->appendStylesheet( $this->baseUrl( '/application/modules/Sitestorelikebox/externals/styles/light.css' )) ;	?>
	<?php endif;?>
<?php endif;?>

<?php echo $this->headLink()->toString()."\n" ?>

<div id="splb_wrapper" style="<?php if($this->value['border_color']): ?> border-color: <?php echo $this->value['border_color'];?>;  <?php endif;?> width: <?php echo  ($this->value['width'] - 2)?>px;height:<?php echo  ($this->value['height'] - 2)?>px;">

<?php if($this->sitestore):?>
	<!-- Header dispaly according to condition -->
	<?php if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.header' , 1 )):	?>
		<?php if($this->value['header']=="true" ):?>
			<div class="splb_header" id="like_box_header"><?php echo $this->translate('Find us on'); ?>
				<a href="<?php echo "http://" . $_SERVER['HTTP_HOST'] .$this->baseUrl(); ?>" target="_blank " ><?php echo $this->translate(' %s', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')); ?></a>
			</div>
		<?php endif;?>
	<?php else: ?>
		<?php $headerDisplay  = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.header' , 'display' );  ?>
		<?php if ($headerDisplay == 'display') : ?>
			<div class="splb_header" id="like_box_header"><?php echo $this->translate('Find us on'); ?>
				<a href="<?php echo "http://" . $_SERVER['HTTP_HOST'] .$this->baseUrl(); ?>" target="_blank " ><?php echo $this->translate(' %s', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')); ?></a>
			</div>
		<?php endif; ?>
	<?php endif; ?>


	<div class="splb_content">
    <div class="splb_content_top">
    	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon'), array('target'=>"_blank"), array('title'=> $this->sitestore->getTitle())) ?>

			<div class="splb_badge">
				<?php //BADGBE WORK
				if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.badge')) : ?>
					<?php if ($this->sitestorebadges_value == 1 || $this->sitestorebadges_value == 2): ?>
						<?php
							if (!empty($this->sitestorebadge->badge_main_id)) {
								$main_path = 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->storage()->get($this->sitestorebadge->badge_main_id, '')->getHref();
								if (!empty($main_path)) {
									echo '<img src="' . $main_path . '" title="' . $this->sitestorebadge->title . '" />';
								}
							}
						?>
					<?php endif; ?>
				<?php endif;?>
			</div>

      <div class="splb_act">
    		<a href='<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id)), 'sitestore_entry_view', true) ?>' title= '<?php echo $this->sitestore->getTitle(); ?>' target="_blank;"  >
    			<?php echo Engine_Api::_()->sitestore()->truncation($this->sitestore->getTitle(),$this->value['titleturncation']) ?>
    		</a>
	      <div class="sitestore_like_button">
					<?php if(Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.likebutton' , 1 )): ?>
						<?php if(!empty($this->edit)):?>
							<a href="javascript:void(0);">
								<i class="sitestore_like_thumbup_icon"></i>
								<span><?php echo $this->translate('Like') ?></span>
							</a>
						<?php else: ?>
							<a id="unlike_link" href="javascript:void(0);" onclick="unlike('<?php echo $this->sitestore->getType()?>', '<?php echo $this->sitestore->getIdentity() ?>')"  <?php if( !$this->sitestore->likes()->isLike($this->viewer()) ): ?> style="display: none;" <?php endif;?> ><i class="sitestore_like_thumbdown_icon"></i>
								<span><?php echo $this->translate('Unlike') ?></span></a>
							<a id="like_link" href="javascript:void(0);" onclick="like('<?php echo $this->sitestore->getType()?>', '<?php echo $this->sitestore->getIdentity() ?>')"  <?php if( $this->sitestore->likes()->isLike($this->viewer()) ): ?> style="display: none;" <?php endif;?>  ><i class="sitestore_like_thumbup_icon"></i>
								<span><?php echo $this->translate('Like') ?></span></a>
						<?php endif;?>
					<?php endif;?>
	        <?php if($this->value['faces']=="false"):?>
						<span style="margin-left: 30px;">
						<?php //echo $this->locale()->toNumber($this->sitestore->like_count) ?>
	          </span>
	        <?php endif; ?>
	      </div>
	    </div>
    </div>

    <?php //START TAB SHOW WORK ?>
		<?php if($this->value['stream']=="true"):?>
		<div class="splb_stream">
			<div class='splb_tabs_alt'>
				<ul class="splb_tabs">
					<?php $paramaName = Engine_Api::_()->sitestorelikebox()->getWidgteParams();
								$activeClass = 'active';
								$flag = 0;
								$flagDivId = '';

						foreach( $paramaName as $order => $infoArray ) {
							if( !empty($flag) ) { $activeClass = ''; }
							switch($infoArray['name']) {
								case 'activity.feed':
								case 'advancedactivity.home-feeds':
								case 'seaocore.feed':
										if($this->value['streamupdatefeed']=="true"): ?>
											<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('updatefeed_likebox_result', 'updatefeed_likebox_active' )" id= "updatefeed_likebox_active" >
												<a><?php  echo $this->translate($infoArray['title']); ?></a>
											</li><?php if( empty($flag) ){ $flagDivId = "updatefeed_likebox_active"; } ?>
										<?php endif ;
								break;

								case 'sitestore.info-sitestore':	?>

									<?php if( Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.info' , 1 ) ): ?>
										<?php if($this->value['streaminfo']=="true"): ?>
											<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('info_likebox_result', 'info_likebox_active' )" id= "info_likebox_active" >
												<a><?php  echo $this->translate($infoArray['title']); ?></a>
											</li><?php if( empty($flag) ){ $flagDivId = "info_likebox_active"; } ?>
										<?php endif ;?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestore.location-sitestore': ?>

										<?php if( Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.map' , 1 ) ): ?>
											<?php if($this->value['streammap'] =="true" && !empty ($this->sitestore->location)): ?>
												<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('map_likebox_result', 'map_likebox_active' )" id= "map_likebox_active" >
													<a><?php echo $this->translate($infoArray['title']); ?></a>
												</li><?php if( empty($flag) ){ $flagDivId = "map_likebox_active"; } ?>
											<?php endif ;?>
										<?php endif ;?>
										<?php
								break;

								case 'sitestore.discussion-sitestore': ?>
									<?php
										if($this->value['streamdiscussion']=="true"  && !empty($this->discussionTotalResult)): ?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('discussion_likebox_result', 'discussion_likebox_active' )" id= "discussion_likebox_active" >
											<a><?php  echo $this->translate($infoArray['title']);?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "discussion_likebox_active"; } ?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestore.photos-sitestore': ?>
									<?php if($this->value['streamalbum']=="true" && !empty ($this->albumTotalResult)): ?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('album_likebox_result', 'album_likebox_active' )" id= "album_likebox_active" >
											<a><?php  echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "album_likebox_active"; } ?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestoreevent.profile-sitestoreevents': ?>

									<?php if(($this->value['streamevent']=="true") && !empty ($this->eventTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('event_likebox_result', 'event_likebox_active')" id= "event_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "event_likebox_active"; } ?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestorepoll.profile-sitestorepolls': ?>

									<?php if($this->value['streampoll']=="true" && !empty ($this->pollTotalResult) ): ?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('poll_likebox_result', 'poll_likebox_active')" id= "poll_likebox_active"  >
											<a><?php  echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "poll_likebox_active"; } ?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestorenote.profile-sitestorenotes': ?>

									<?php if($this->value['streamnote']=="true" && !empty ($this->notesTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('note_likebox_result', 'note_likebox_active')" id= "note_likebox_active"  >
											<a><?php  echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "note_likebox_active"; } ?>
									<?php endif ;?>
									<?php
								break;

								case 'sitestoreoffer.profile-sitestoreoffers': ?>

									<?php if($this->value['streamoffer']=="true" && !empty ($this->offersTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('offer_likebox_result', 'offer_likebox_active')" id= "offer_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "offer_likebox_active"; } ?>
									<?php endif ;?><?php
								break;

								case 'sitestorevideo.profile-sitestorevideos': ?>

									<?php if($this->value['streamvideo']=="true" && !empty ($this->videosTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('video_likebox_result', 'video_likebox_active')" id= "video_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "video_likebox_active"; } ?>
									<?php endif ;?><?php
								break;

								case 'sitestoremusic.profile-sitestoremusic': ?>

									<?php if($this->value['streammusic']=="true" && !empty ($this->playlistsTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('music_likebox_result', 'music_likebox_active')" id= "music_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "music_likebox_active"; } ?>
									<?php endif ;?><?php
								break;

								case 'sitestorereview.profile-sitestorereviews': ?>

									<?php if($this->value['streamreview']=="true" && !empty($this->totalReviews) ): ?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('review_likebox_result', 'review_likebox_active')" id= "review_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "review_likebox_active"; } ?>
									<?php endif ;?><?php
								break;

								case 'sitestoredocument.profile-sitestoredocuments': ?>
									<?php if($this->value['streamdocument']=="true" && !empty ($this->documentTotalResult) ):?>
										<li class="<?php echo $activeClass; ?>" onclick= "showmoduleResult('document_likebox_result', 'document_likebox_active')" id= "document_likebox_active"  >
											<a><?php echo $this->translate($infoArray['title']); ?></a>
										</li><?php if( empty($flag) ){ $flagDivId = "document_likebox_active"; } ?>
									<?php endif ;?><?php
								break;
							}
							?><?php $flag ++;
						}
					?>
				</ul>
			</div>
			<div class="splb_stream_cont">
				<?php //HERE  INFO CONTENT SHOW ?>
				<?php if($this->value['streamupdatefeed']=="true"):?>
					<div class=''  id = "updatefeed_likebox_result" <?php if ($this->current_activetab == "updatefeed_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?> >

						<?php //BADGBE WORK
						echo $this->content()->renderWidget("sitestorelikebox.feeds", array()); ?>
						</div>
						<?php endif ;?>

			      <?php //HERE  INFO CONTENT SHOW ?>
						<?php if($this->value['streaminfo']=="true"):?>
							<div class=''  id = "info_likebox_result" <?php if ($this->current_activetab == "info_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?> >
								<div class='splb_info_tab'>
									<h4>
										<?php echo $this->translate('Basic Information'); ?>
									</h4>
									<ul>
										<li>
											<span><?php echo $this->translate('Posted By:'); ?> </span>
											<span><?php echo $this->htmlLink($this->sitestore->getParent(), $this->sitestore->getParent()->getTitle(), array('target'=>"_blank")) ?></span>
										</li>
										<li>
											<span><?php echo $this->translate('Posted:'); ?></span>
											<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitestore->creation_date))) ?></span>
										</li>
										<?php if(!empty($this->sitestore->comment_count)): ?>
											<li>
												<span><?php echo $this->translate('Comments:'); ?></span>
												<span><?php echo $this->translate( $this->sitestore->comment_count) ?></span>
											</li>
										<?php endif; ?>
										<?php if(!empty($this->sitestore->like_count)): ?>
											<li>
												<span><?php echo $this->translate('Likes:'); ?></span>
												<span><?php echo $this->translate( $this->sitestore->like_count) ?></span>
											</li>
										<?php endif; ?>
											<?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
										<?php if($this->sitestore->price && $enablePrice):?>
										<li>
											<span><?php echo $this->translate('Price:'); ?></span>
											<span><?php echo $this->locale()->toCurrency($this->sitestore->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></span>
										</li>
										<?php endif; ?>
										<?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
										<?php if($this->sitestore->location && $enableLocation):?>
										<li>
											<span><?php echo $this->translate('Location:'); ?></span>
											<span><?php echo $this->sitestore->location ?></span>
										</li>
										<?php endif; ?>
										<li>
											<span><?php echo $this->translate('Description:'); ?></span>
											<span><?php echo $this->viewMore(nl2br($this->sitestore->body),300,5000) ?></span>
										</li>
									</ul>
									<?php
										$user = Engine_Api::_()->user()->getUser($this->sitestore->owner_id);
										$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact_detail');
										$availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');
										$options_create = array_intersect_key($availableLabels, array_flip($view_options));
									?>
									<?php if(!empty($this->contactPrivacy) &&!empty($options_create) && (!empty($this->sitestore->email) || !empty($this->sitestore->website) || !empty($this->sitestore->phone))):?>
										<h4>
											<?php echo $this->translate('Contact Details');  ?>
										</h4>
										<ul>
											<?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone' && !empty($this->sitestore->phone)):?>
												<li>
													<span><?php echo $this->translate('Phone:'); ?></span>
													<span><?php echo $this->translate(''); ?> <?php echo $this->sitestore->phone ?></span>
												</li>
											<?php endif; ?>
											<?php if(isset($options_create['email']) && $options_create['email'] == 'Email' && !empty($this->sitestore->email)):?>
												<li>
													<span><?php echo $this->translate('Email:'); ?></span>
													<span><?php echo $this->translate(''); ?>
													<a href='mailto:<?php echo $this->sitestore->email ?>'><?php echo $this->sitestore->email ?></a></span>
												</li>
											<?php endif; ?>
											<?php if( isset($options_create['website']) && $options_create['website'] == 'Website' && !empty($this->sitestore->website)):?>
												<li>
													<span><?php echo $this->translate('Website:'); ?></span>
													<?php if(strstr($this->sitestore->website, 'http://') || strstr($this->sitestore->website, 'https://')):?>
													<span><a href='<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
													<?php else:?>
													<span><a href='http://<?php echo $this->sitestore->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitestore->website ?></a></span>
													<?php endif;?>
												</li>
											<?php endif; ?>
										</ul>
									<?php endif; ?>
									<?php if(!empty ($this->profileTypePrivacy)):
									$str =  $this->profileFieldValueLoop($this->sitestore, $this->fieldStructure)?>
										<?php if($str): ?>
											<h4>
												<?php  echo $this->translate('Profile Information');  ?>
											</h4>
											<?php echo $this->profileFieldValueLoop($this->sitestore, $this->fieldStructure) ?>
										<?php endif; ?>
									<?php endif; ?>
									<br />
								</div>
						</div>
					<?php endif; ?>

          <?php // THIS IS FOR MAP ?>
					<?php if($this->value['streammap']=="true"):?>
						<div class=''  id = "map_likebox_result" <?php if ($this->current_activetab == "map_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?>>
							<?php echo $this->content()->renderWidget("sitestorelikebox.location-sitestore", array()); ?>
						</div>
					<?php endif; ?>

					<?php // THIS IS FOR Discussion ?>
					<?php if($this->value['streamdiscussion']=="true"):?>
						<div class=''  id="discussion_likebox_result" <?php if ($this->current_activetab == "discussion_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach( $this->discussionresult as $topic ):
									$lastpost = $topic->getLastPost();
									$lastposter = $topic->getLastPoster();
									?>
									<li>
										<div class="splb_tab_cont_list_img">
											<?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon'), array('target'=>"_blank")) ?>
										</div>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink($topic->getHref(), Engine_Api::_()->sitestore()->truncation($topic->getTitle(),$this->titleturncation), array('target'=>"_blank")) ?>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->discussionTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $topic->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($topic->store_id), 'tab' => $tab_selected_id), $this->translate("View More &raquo;"), array("target" => "_blank" , "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php // THIS IS FOR Reviews ?>
					<?php if($this->value['streamreview']=="true"):?>
						<div class=''  id="review_likebox_result" <?php if ($this->current_activetab == "review_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?> >
							<ul class="splb_tab_cont_list">

								<!--start top box code-->
								<?php if(!empty($this->ratingDataTopbox) && !empty($this->noReviewCheck)):?>
									<?php $iteration = 1;?>
									<?php foreach($this->ratingDataTopbox as $reviewcatTopbox): ?>
												<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
													<?php
														$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcatTopbox['avg_rating'], 'box');
														$rating_valueTopbox = $showRatingImage['rating_value'];
													?>
												<?php else:?>
													<?php
														$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcatTopbox['avg_rating'], 'star');
														$rating_valueTopbox = $showRatingImage['rating_value'];
														$rating_valueTitle = $showRatingImage['rating_valueTitle'];
													?>
												<?php endif; ?>
										<li class="splb_ror">
											<div class="splb_rorl">
												<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
													<?php echo $this->translate($reviewcatTopbox['reviewcat_name'])?>
												<?php else:?>
													<b><?php echo $this->translate("Overall Rating");?></b>
												<?php endif; ?>
											</div>
											<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
												<div class="splb_rorr">
													<ul class='rating-box-small <?php echo $rating_valueTopbox; ?>' style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/rating-img-small.png);">
														<li id="1" class="rate one">1</li>
														<li id="2" class="rate two">2</li>
														<li id="3" class="rate three">3</li>
														<li id="4" class="rate four">4</li>
														<li id="5" class="rate five">5</li>
													</ul>
												</div>
											<?php else:?>
												<div class="splb_rorr">
													<ul title="<?php echo $rating_valueTitle.$this->translate(" rating"); ?>" class='rating <?php echo $rating_valueTopbox; ?>' style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/show-star-matrix.png);">
														<li id="1" class="rate one">1</li>
														<li id="2" class="rate two">2</li>
														<li id="3" class="rate three">3</li>
														<li id="4" class="rate four">4</li>
														<li id="5" class="rate five">5</li>
													</ul>
												</div>
										<?php endif;?>

										<?php if($iteration == 1):?>
											<span>
												<?php if($this->totalReviews == 1): ?>
													<?php echo $this->translate("Total ").'<b>'.$this->totalReviews.'</b>'.$this->translate(" Review");?>
												<?php else: ?>
													<?php echo $this->translate("Total ").'<b>'.$this->totalReviews.'</b>'.$this->translate(" Reviews");?>
												<?php endif; ?>
											</span>
										<?php endif; ?>

										</li>

										<?php if($iteration == 1):?>
											<li class="sitestorereview_overall_recommended">
												<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
													<?php echo $this->translate("Recommended by ") .'<b>' .$this->recommend_percentage .'%</b>'. $this->translate(" members");?>
												<?php endif;?>
											</li>
										<?php endif; ?>

										<?php $iteration++;?>
									<?php endforeach; ?>
								<?php endif; ?>
								<!--end top box code-->

								<!--Start for content show-->
								<?php foreach ($this->paginator as $review): ?>
									<li class="splb_ror">
										<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($review->review_id); ?>
										<b><?php echo $this->translate('Latest Review'); ?></b>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->sitestorereview()->truncateText($review->title, 50), array('title' => $review->title, 'target' => "_blank")) ?>
										</div>

										<div class="splb_tab_cont_list_stats">
											<?php echo $this->timestamp(strtotime($review->modified_date)) ?>
												-
												<?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle(), array('target' => "_blank")) ?>
										</div>

										<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1)):?>
											<div class="splb_tab_cont_list_stats">
												<?php echo '<b>' .$this->translate("Pros: "). '</b>' .$this->viewMore($review->pros) ?>
											</div>

											<div class="splb_tab_cont_list_stats">
												<?php echo '<b>' .$this->translate("Cons: "). '</b>' .$this->viewMore($review->cons) ?>
											</div>
										<?php endif;?>

										<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
											<div class='splb_tab_cont_list_stats'>
												<?php if($review->recommend):?>
													<?php echo $this->translate("<b>Member's Recommendation:</b> Yes"); ?>
												<?php else: ?>
													<?php echo $this->translate("<b>Member's Recommendation:</b> No"); ?>
												<?php endif;?>
											</div>
										<?php endif;?>

										<div class='splb_tab_cont_list_stats'>
											<?php
												if(strlen($review->body) > 300) {
												$read_complete_review = $this->htmlLink($review->getHref(), 'Read complete review', array('target' => '_blank'));
												$truncation_limit = 300;
												$tmpBody = strip_tags($review->body);
												$item_body = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... $read_complete_review" : $tmpBody );
												}	else {
													$item_body = $review->body;
												}
											?>
											<?php echo $item_body; ?>
										</div>
									</li>
								<?php endforeach; ?>
								<!--End for content show-->

								<?php $likebox_contentshow = 1;
									if ($likebox_contentshow < count($this->paginator)): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $review->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($review->store_id), 'tab' => $tab_selected_id), $this->translate("View More &raquo;"), array("target" => "_blank" , "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>


		      <?php // THIS IS FOR ALBUM ?>
					<?php if($this->value['streamalbum']=="true"):?>
						<div class=''  id="album_likebox_result" <?php if ($this->current_activetab == "album_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;"<?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->albumresult as $albums):  ?>
									<li>
										<div class="splb_tab_cont_list_img">
											<?php if ($albums->photo_id != 0):  ?>
												<a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $albums->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title; ?>" target= "_blank">
													<?php echo $this->itemPhoto($albums, 'thumb.icon') ?>
												</a>
											<?php else: ?>
												<a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $albums->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title; ?>" target= "_blank" >
													<?php echo $this->itemPhoto($albums, 'thumb.icon') ?>
												</a>
											<?php endif; ?>
										</div>
										<div class="splb_tab_cont_list_title">
											<a href="<?php echo $this->url(array('action' => 'view', 'store_id' => $albums->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title; ?>" target= "_blank" ><?php echo Engine_Api::_()->sitestore()->truncation($albums->title ,$this->titleturncation); ?></a>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->albumTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $albums->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($albums->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

		      <?php // THIS IS FOR EVENTS ?>
					<?php if($this->value['streamevent'] == "true"):?>
						<div class='' id = "event_likebox_result" <?php if ($this->current_activetab == "event_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->eventresult as $sitestoreevent): ?>
									<li id="sitestoreevent-item-<?php echo $sitestoreevent->event_id ?>">
										<div class="splb_tab_cont_list_img">
											<?php echo  $this->htmlLink($sitestoreevent->getHref(),$this->itemPhoto($sitestoreevent, 'thumb.icon', $sitestoreevent->getTitle()), array('target'=>"_blank")) ?>
										</div>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink($sitestoreevent->getHref(), Engine_Api::_()->sitestore()->truncation($sitestoreevent->title ,$this->titleturncation) , array('target'=>"_blank")) ?>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->eventTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreevent.profile-sitestoreevents', $sitestoreevent->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestoreevent->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php // THIS IS FOR POLLS ?>
					<?php if($this->value['streampoll']=="true"):?>
						<div class='' id = "poll_likebox_result" <?php if ($this->current_activetab == "poll_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->pollresult as $sitestorepoll): ?>
									<li id="sitestorepoll-item-<?php echo $sitestorepoll->poll_id ?>">
										<div class="splb_tab_cont_list_img">
											<?php echo  $this->htmlLink($sitestorepoll->getHref(),$this->itemPhoto($sitestorepoll, 'thumb.icon', $sitestorepoll->getTitle()), array('target'=>"_blank")) ?>
										</div>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink(array('route' => 'sitestorepoll_detail_view', 'user_id' => $sitestorepoll->owner_id, 'poll_id' => $sitestorepoll->poll_id,'slug'=> $sitestorepoll->getSlug(),'tab'=>$this->identity_temp), Engine_Api::_()->sitestore()->truncation($sitestorepoll->title,$this->titleturncation) , array('target'=>"_blank")) ?>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->pollTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorepoll.profile-sitestorepolls', $sitestorepoll->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorepoll->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

		      <?php // THIS IS FOR NOTES ?>
					<?php if($this->value['streamnote']=="true"):?>
						<div id = "note_likebox_result" <?php if ($this->current_activetab == "note_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->notesresult as $sitestorenote): ?>
									<li id="sitestorenote-item-<?php echo $sitestorenote->note_id ?>">
										<div class="splb_tab_cont_list_img">
											<?php if($sitestorenote->photo_id == 0):?>
												<?php //if($this->sitestoreSubject->photo_id == 0):?>
													<?php echo $this->htmlLink($sitestorenote->getHref(),$this->itemPhoto($sitestorenote, 'thumb.icon', $sitestorenote->getTitle()), array('target'=>"_blank")) ?>
												<?php //else:?>
													<?php //echo $this->htmlLink($sitestorenote->getHref(),$this->itemPhoto($this->sitestoreSubject, 'thumb.icon', $sitestorenote->getTitle()), array('target'=>"_blank")) ?>
												<?php //endif;?>
											<?php else:?>
												<?php echo $this->htmlLink($sitestorenote->getHref(),$this->itemPhoto($sitestorenote, 'thumb.icon', $sitestorenote->getTitle()), array('target'=>"_blank")) ?>
											<?php endif;?>
										</div>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink($sitestorenote->getHref(), Engine_Api::_()->sitestore()->truncation($sitestorenote->title ,$this->titleturncation) , array('target'=>"_blank")) ?>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->notesTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorenote.profile-sitestorenotes', $sitestorenote->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorenote->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php // THIS IS FOR OFFERS ?>
					<?php if($this->value['streamoffer']=="true"):?>
						<div class='' id = "offer_likebox_result" <?php if ($this->current_activetab == "offer_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->offersresult as $item): ?>
							    <li>
							    	<div class="splb_tab_cont_list_img">
							   			<?php if(!empty($item->photo_id)):?>
												<?php echo $this->htmlLink($item->getHref(),$this->itemPhoto($item, 'thumb.icon', $item->getTitle()), array('target'=>"_blank")) ?>
								      <?php else:?>
								        <?php echo "<img src='".$this->layout()->staticBaseUrl."application/modules/Sitestoreoffer/externals/images/offer_thumb.png' alt='' />" ?>
								      <?php endif;?>
							      </div>
								    <div class='splb_tab_cont_list_title'>
										<?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->sitestore()->truncation($item->title ,$this->titleturncation)  , array('target'=>"_blank")) ?>
							     	</div>
							      <div class="splb_tab_cont_list_stats">
								      <?php echo $this->translate('End date:'); ?>
								      <?php if($item->end_settings == 1):?>
								       <?php echo $this->translate( gmdate('M d, Y', strtotime($item->end_time))) ?>
								      <?php else:?>
								       <?php echo $this->translate('Never Expires') ?>
								      <?php endif;?>
		           		  </div>
								  </li>
							  <?php  endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->offersTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $item->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($item->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>


					<?php // THIS IS FOR VIDEOS ?>
			    <?php if($this->value['streamvideo']=="true"):?>
						<div class='' id = "video_likebox_result" <?php if ($this->current_activetab == "video_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
					      <?php foreach( $this->videosresult as $item ): ?>
									<li id="sitestorevideo-item-<?php echo $item->video_id ?>">
										<div class="splb_tab_cont_list_img">
											<a id="sitestorevideo_video_thumb ">
													<?php  if ($item->photo_id): ?>
							              <?php echo $this->htmlLink($item->getHref(),$this->itemPhoto($item, 'thumb.icon', $item->getTitle()), array('target'=>"_blank")) ?>
													<?php else: ?>
														<img src= "<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorevideo/externals/images/video.png" class="thumb_normal item_photo_video  thumb_normal" />
													<?php endif;?>
											</a>
										</div>
										<div class="splb_tab_cont_list_title">
					          	<?php echo $this->htmlLink(array('route' => 'sitestorevideo_view', 'user_id' => $item->owner_id, 'video_id' =>  $item->video_id,'tab' => $this->identity_temp,'slug' => $item->getSlug()), Engine_Api::_()->sitestore()->truncation($item->title,$this->titleturncation), array('target'=>"_blank")) ?>

											<?php if($item->status == 0):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
													</span>
												</div>
											<?php elseif($item->status == 2):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
													</span>
												</div>
											<?php elseif($item->status == 3):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 4):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 5):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 7):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
													</span>
												</div>
											<?php endif;?>
										</div>
									</li>
						    <?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->videosTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $item->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($item->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
						  </ul>
				   	</div>
			    <?php endif; ?>


					<?php // THIS IS FOR MUSICS ?>
					<?php if($this->value['streammusic']=="true"):?>
						<div class='' id = "music_likebox_result" <?php if ($this->current_activetab == "music_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->playlistsresult as $playlist): ?>
									<li id="sitestoremusic-item-<?php echo $playlist->playlist_id ?>">
										<div class="splb_tab_cont_list_img">
											<?php echo  $this->htmlLink($playlist->getHref(),$this->itemPhoto($playlist, 'thumb.icon', $playlist->getTitle()), array('target'=>"_blank")) ?>
										</div>
										<div class="splb_tab_cont_list_title">
											<?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle(),array('target'=>"_blank")) ?>
										</div>
									</li>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->playlistsTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoremusic.profile-sitestoremusic', $playlist->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($playlist->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					<?php endif; ?>

			    <?php // THIS IS FOR DOCUMENTS ?>
					<?php if($this->value['streamdocument']=="true"):?>
						<div class='' id = "document_likebox_result" <?php if ($this->current_activetab == "document_likebox_result"): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?> >
							<ul class="splb_tab_cont_list">
								<?php foreach ($this->documentsresult as $sitestoredocument): ?>
								<?php $flag = 1; ?>
								<?php if($sitestoredocument->draft == 0 && $sitestoredocument->approved == 1 && $sitestoredocument->status == 1):?>
									<?php $flag = 1; ?>
								<?php elseif($this->level_id == 1 || $this->viewer_id == $this->sitestore->owner_id || $this->viewer_id == $sitestoredocument->owner_id || $this->can_edit == 1): ?>
									<?php $flag = 1; ?>
								<?php endif; ?>
							<?php if($flag == 1): ?>
								<li>
									<div class="splb_tab_cont_list_img">
										<?php if(!empty($sitestoredocument->thumbnail)): ?>
											<?php echo $this->htmlLink($sitestoredocument->getHref(), '<img src="'. $sitestoredocument->thumbnail .'" />', array('target'=>"_blank"), array('title' => $sitestoredocument->sitestoredocument_title) ) ?>
										<?php else: ?>
											<?php echo $this->htmlLink($sitestoredocument->getHref(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoredocument/externals/images/sitestoredocument_thumb.png" />', array('target'=>"_blank") , array('title' => $sitestoredocument->sitestoredocument_title)) ?>
										<?php endif;?>
									</div>
									<div class='splb_tab_cont_list_title'>
										<?php echo $this->htmlLink($sitestoredocument->getHref(), Engine_Api::_()->sitestore()->truncation($sitestoredocument->sitestoredocument_title,$this->titleturncation), array('target'=>"_blank"), array('title' => $sitestoredocument->sitestoredocument_title)) ?>
									</div>
								</li>
								<?php endif; ?>
								<?php endforeach; ?>
								<?php if ($this->likebox_contentshow < $this->documentsTotalResult): ?>
									<li>
										<?php $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoredocument.profile-sitestoredocuments', $sitestoredocument->store_id, $this->storelayout); ?>
										<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestoredocument->store_id), 'tab' => $tab_selected_id), $this->translate('View More &raquo;'), array('target' => "_blank", "class" => "splb_viewmore")); ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
			    <?php endif; ?>
				</div>
			</div>
	  <?php endif; ?>

	<?php $likeCount = $this->sitestore->like_count;
		if (!empty($likeCount)):  ?>
		<?php if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.faces' , 1 )):  ?>
			<?php if($this->value['faces']=="true"):?>
				<div class="splb_fanbox">
					<div class="splb_fanbox_heading">
						<?php echo $this->translate(array('%s person likes', '%s people like', $this->sitestore->like_count), $this->locale()->toNumber($this->sitestore->like_count)) ?>
						<b><?php echo $this->sitestore->getTitle() ?></b>
					</div>
					<div class="splb_fanbox_items_list">
						<?php $countLikes = $this->sitestore->like_count;?>
						<?php if(!empty($countLikes)):?>
							<?php foreach ($this->userLikes as $user): ?>
								<div class="splb_fanbox_items">
									<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(), 'target'=>"_blank"))?>
									<div>
										<?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle(), 'target'=>"_blank")) ?>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php else:  ?>
				<?php $photoDisplay  = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.faces' , 'display' ); ?>
				<?php if ($photoDisplay == 'display'):  ?>
					<div class="splb_fanbox">
						<div class="splb_fanbox_heading">
							<?php echo $this->translate(array('%s person likes', '%s people like', $this->sitestore->like_count), $this->locale()->toNumber($this->sitestore->like_count)) ?>
							<b><?php echo $this->sitestore->getTitle() ?></b>
						</div>
						<div class="splb_fanbox_items_list">
							<?php $countLikes=$this->sitestore->like_count;?>
							<?php if(!empty($countLikes)):?>
								<?php foreach ($this->userLikes as $user):?>
									<div class="splb_fanbox_items">
										<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(),  'target'=>"_blank"))?>
										<div>
											<?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle(),'target'=>"_blank")) ?>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
	<?php endif; ?>


		<?php if (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.powred')): ?>
			<div class="splb_btm">
        <?php if (empty ($this->photo_name) || (!Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title'))): ?><?php echo $this->translate('Powered By :'); ?>
					<a href="<?php echo "http://" . $_SERVER['HTTP_HOST'] .$this->baseUrl(); ?>" target="_blank" linkindex="32" title="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'); ?>"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));  ?></a>
				<?php else: ?><?php echo $this->translate('Powered By :'); ?>
					<a href="<?php echo "http://" . $_SERVER['HTTP_HOST'] .$this->baseUrl(); ?>" target="_blank" linkindex="32" title="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'); ?>"><?php //echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')); ?>
						<span><img src="<?php echo  $this->photo_name ?>" /></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>


<script type="text/javascript">
  function like(type, id) {
    var hasLogin=  '<?php echo $this->hasLogin; ?>';
    if(hasLogin==1){
      var req = new Request.JSON({
        url : "<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'has-login'), 'sitestorelikebox_general', true) ?>",
        data : {
          format : 'json'
        },
        onComplete : function(responseJSON) {
          hasLogin=responseJSON.hasLogin;
          if(hasLogin==1){
            var req_like = new Request.JSON({
              url : "<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'like'), 'sitestorelikebox_general', true) ?>",
              data : {
                format : 'json',
                type : type,
                id : id           
              },
              onComplete : function(response) {
                if( $type(response) == 'object' &&
                  $type(response.status) &&
                  response.status == false ) {
                  en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                  return;
                } else if( $type(response) != 'object' ||
                  !$type(response.status) ) {
                  en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                  return;
                }

                $('like_link').style.display="none";
                $('unlike_link').style.display="block";

              }
            });
            req_like.send();
          }else{
              openPopUp("<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'login', 'function' => 'like', 'type' => $this->sitestore->getType(), 'id' => $this->sitestore->getIdentity()), 'sitestorelikebox_general', true) ?>");
          }
        }
      });
      req.send();
    }else{
      openPopUp("<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'login', 'function' => 'like', 'type' => $this->sitestore->getType(), 'id' => $this->sitestore->getIdentity()), 'sitestorelikebox_general', true) ?>");
    }
  }

  function openPopUp(url){
    var child_window = window.open (url,'mywindow','width=600,height=600,left=444, top=24');
    if(window.focus)
      child_window.focus();
  }


  function unlike(type, id) {
    var hasLogin=  <?php echo $this->hasLogin; ?>;
    if(hasLogin==1){
      var req = new Request.JSON({
        url : "<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'has-login'), 'sitestorelikebox_general', true) ?>",
        data : {
          format : 'json'
        },
        onComplete : function(responseJSON) {
          hasLogin=responseJSON.hasLogin;
          if(hasLogin==1){
            var req_like = new Request.JSON({
              url : "<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'unlike'), 'sitestorelikebox_general', true) ?>",
              data : {
                format : 'json',
                type : type,
                id : id               
              },
              onComplete : function(response) {
                if( $type(response) == 'object' &&
                  $type(response.status) &&
                  response.status == false ) {
                  en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                  return;
                } else if( $type(response) != 'object' ||
                  !$type(response.status) ) {
                  en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                  return;
                }

                $('unlike_link').style.display="none";
                $('like_link').style.display="block";

              }
            });
            req_like.send();        
          }else{
          openPopUp("<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'login', 'function' => 'unlike', 'type' => $this->sitestore->getType(), 'id' => $this->sitestore->getIdentity()), 'sitestorelikebox_general', true) ?>");
          }

        }
      });
      req.send();
    }else{
      openPopUp("<?php echo "http://" . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'login', 'function' => 'unlike', 'type' => $this->sitestore->getType(), 'id' => $this->sitestore->getIdentity()), 'sitestorelikebox_general', true) ?>");
    }
  }

</script>
<?php else: ?>
	<div class="tip">
	  <span>
	    <?php echo $this->translate("Object is not found"); ?>
	  </span>
	</div>
	<?php endif; ?>
</div>
</body>
</html>
<style type="text/css">
#smoothbox_window,
#global_page_sitealbum-badge-index{
	overflow-y: hidden !important;
  width: 100%;
	padding:0px !important;
}
</style>
<script type="text/javascript">
	flag_div_id = '<?php echo $flagDivId; ?>';
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _aafcomposer.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headTranslate(array('Publish this on Facebook', 'Publish this on Twitter', 'Publish this on LinkedIn', 'Use Webcam', 'OR', 'Say something about this photo...'));
?>

<script type='text/javascript'>
    var showVariousTabs = '<?php echo $this->showTabs; ?>';

<?php if (Engine_Api::_()->hasModuleBootstrap('sitealbum') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.open.lightbox.upload', 1)): ?>
        var showAddPhotoInLightbox = "<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.open.lightbox.upload', 1); ?>";
<?php else: ?>
        var showAddPhotoInLightbox = 0;
<?php endif; ?>
</script>

<?php if ($this->enableComposer): ?>
    <?php
    $this->headLink()
            ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/styles/style_statusbar.css');
    $this->headTranslate(array('ADVADV_SHARE', 'Who are you with?'));
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js')
    ;
    ?>
    <div class="adv_post_container nolinks" id="activity-post-container" style="display: none;" >
        <?php $composerOptions = $this->settingsApi->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
        ?>
        <form method="post" action="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" enctype="application/x-www-form-urlencoded" id="activity-form" <?php if ($this->showTabs && Engine_Api::_()->hasModuleBootstrap('sitealbum')): ?>class="adv-active"<?php endif; ?>>
            <?php if ($this->showTabs && Engine_Api::_()->hasModuleBootstrap('sitealbum')): ?> 
                <?php $showAlbumLink = false; ?>
                <?php $showVideoLink = false; ?>
                <?php foreach ($this->composePartials as $partial): ?>
                    <?php if (isset($partial[1]) && $partial[1] == 'sitealbum'): ?>
                        <?php $showAlbumLink = true; ?>
                    <?php endif; ?>
                    <?php if (isset($partial[1]) && ($partial[1] == 'video' || $partial[1] == 'sitevideo')): ?>
                        <?php $showVideoLink = true; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($showAlbumLink || $showVideoLink): ?>
                    <div class="compose-tray adv_post_container_attachment adv_post_container_tabs">
                        <div class="compose-menu active" id="compose-status-menu-link"><a href="javascript:void(0);" title="<?php echo $this->translate('Update Status'); ?>" onclick='showDefaultActivator();' class="item_icon_update"><?php echo $this->translate("Update Status"); ?></a>
                        </div>

                        <?php if ($showAlbumLink): ?>
                            <div class="compose-menu" id="compose-photo-menu-link">
                                <span class="aaf_media_sep seaocore_txt_light"><?php echo $this->translate('|'); ?></span>
                                <a href="javascript:void(0);" title="<?php echo $this->translate('Add Photo'); ?>" onclick='showPhotoActivator();' class="item_icon_photo"><?php echo $this->translate('Add Photo'); ?></a>
                            </div>
                            <div class="compose-menu">
                                <span class="aaf_media_sep seaocore_txt_light"><?php echo $this->translate('|'); ?></span>
                                <?php if (Engine_Api::_()->hasModuleBootstrap('sitealbum') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.open.lightbox.upload', 1)): ?>
                                    <a title="<?php echo $this->translate('Create Photo Album'); ?>" href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true); ?>" class="seao_smoothbox item_icon_album" data-SmoothboxSEAOClass="seao_add_photo_lightbox"><?php echo $this->translate('Create Photo Album'); ?></a>
                                <?php else: ?>
                                    <a title="<?php echo $this->translate('Create Photo Album'); ?>" href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true); ?>" class="item_icon_album" ><?php echo $this->translate('Create Photo Album'); ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Engine_Api::_()->hasModuleBootstrap('sitevideo') && $showVideoLink): ?>

                            <?php $channel_id = 0; ?>
                            <?php if (Engine_Api::_()->core()->hasSubject('sitevideo_channel')): ?>
                                <?php $channel_id = Engine_Api::_()->core()->getSubject('sitevideo_channel')->getIdentity();
                                ?>
                            <?php endif; ?>
                            <div class="compose-menu" id="compose-video-menu-link">
                                <span class="aaf_media_sep seaocore_txt_light"><?php echo $this->translate('|'); ?></span>
                                <?php if ($channel_id): ?>
                                        <a title="<?php echo $this->translate('Add Video'); ?>" href="<?php echo $this->url(array('action' => 'create', 'channel_id' => $channel_id), 'sitevideo_video_general', true); ?>" class="seao_smoothbox item_icon_video" data-SmoothboxSEAOClass="seao_add_video_lightbox"><?php echo $this->translate('Add Video'); ?></a>
                                <?php else: ?>
                                    <?php if (empty($this->parentType)): ?>
                                        <a title="<?php echo $this->translate('Add Video'); ?>" href="<?php echo $this->url(array('action' => 'create'), 'sitevideo_video_general', true); ?>" class="seao_smoothbox item_icon_video" data-SmoothboxSEAOClass="seao_add_video_lightbox"><?php echo $this->translate('Add Video'); ?></a>
                                    <?php else : ?>
                                        <a title="<?php echo $this->translate('Add Video'); ?>" href="<?php echo $this->url(array('action' => 'create', 'parent_type' => $this->parentType, 'parent_id' => $this->parentId), 'sitevideo_video_general', true); ?>" class="seao_smoothbox item_icon_video" data-SmoothboxSEAOClass="seao_add_video_lightbox"><?php echo $this->translate('Add Video'); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif (Engine_Api::_()->hasModuleBootstrap('video') && $showVideoLink): ?>
                            <div class="compose-menu" id="compose-video-menu-link">
                                <span class="aaf_media_sep seaocore_txt_light"><?php echo $this->translate('|'); ?></span>
                                <a title="<?php echo $this->translate('Add Video'); ?>" href="javascript:void(0)" class="seao_smoothbox item_icon_video" onclick='showVideoActivator();'><?php echo $this->translate('Add Video'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="adv_post_container_box">
                <textarea id="advanced_activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Post Something...')) ?>" ></textarea>
                <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
                <?php if ($this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
                    <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
                <?php endif; ?>
                <?php if ($this->formToken): ?>
                    <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
                <?php endif ?>
                <?php if (!$this->alwaysOpen && !$this->showTabs): ?> 
                    <a href="javascript:void(0);" onclick="hidestatusbox();" class="adv_post_close" title="<?php echo $this->translate("Close"); ?>"></a>
                <?php endif; ?>
                <div class="adv_post_compose_menu" id="adv_post_container_icons">
                    <span class="aaf_activaor_end" style="display:none;"></span>
                    <?php if (in_array("withtags", $composerOptions)): ?>
                        <span class="adv_post_add_user" onclick="toogleTagWith()">
                            <p class="adv_post_compose_menu_show_tip adv_composer_tip">
                                <?php echo $this->translate("Add People") ?>
                                <img alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />
                            </p>
                        </span>	
                    <?php endif; ?>
                    <?php
                    $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                    if (in_array("emotions", $composerOptions) && $SEA_EMOTIONS_TAG && isset($SEA_EMOTIONS_TAG[0])):
                        ?>
                        <span id="emoticons-button"  class="adv_post_smile"  onclick="setEmoticonsBoard()">
                            <p class="adv_post_compose_menu_show_tip adv_composer_tip">
        <?php echo $this->translate("Insert Emoticons") ?>
                                <img alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />
                            </p>

                            <span id="emoticons-board"  class="seaocore_embox seaocore_embox_closed" >
                                <span class="seaocore_embox_arrow"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tooltip_arrow_top.png" alt="" /></span>
                                <span class="seaocore_embox_title">
                                    <span class="fleft" id="emotion_lable"></span>
                                    <span class="fright"id="emotion_symbol" ></span>
                                </span>
                                    <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                                    <span class="seaocore_embox_icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key) ?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
                                        echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                                        ?></span>
        <?php endforeach; ?>
                            </span>					
                        </span>
    <?php endif; ?>
                </div>
                <div class="adv_post_container_tagged_cont">
                    <span class="aaf_mdash"></span>  
                    <span id="friendas_tag_body_aaf_content"></span>
                    <span class="aaf_dot"></span>
                </div>
            </div>    
            <div id="adv_post_container_tagging" class="adv_post_container_tagging" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>">
                <div class="form-wrapper" id="toValues-wrapper" style="height: auto;">
                    <div class="form-label" id="toValues-label"></div>
                    <div class="form-element" id="toValues-element" style="height: 0px;">
                        <input type="hidden" id="toValues"  name="toValues" />
                    </div>
                </div>
                <input type="text" id="friendas_tag_body_aaf" class="compose-textarea"  name="friendas_tag_body_aaf" />   
            </div>
            <div id="compose-tray" class="compose-tray adv_post_container_attachment" style="display:none;"></div>

            <div class="adv-activeity-post-container-bottom adv-active" id="advanced_compose-menu">	
                <button id="compose-submit" type="submit"><?php echo $this->translate("ADVADV_SHARE") ?></button> 
                <div id="aaf_composer_loading" class="show_loading" style="display:none;"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" /></div>
                <div id="show_loading_main" class="show_loading" style="display:none;">140</div>
                <?php $content = (isset($this->availableLabels[$this->showDefaultInPrivacyDropdown]) || !empty($this->privacylists) ) ? $this->showDefaultInPrivacyDropdown : $this->settingsApi->getSetting('activity.content', 'everyone'); ?>
                <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $content ?>" />
                <?php if ($this->showPrivacyDropdown): ?>      
                    <?php $availableLabels = $this->availableLabels; ?>    
                    <?php if (empty($this->privacylists)): ?>
                        <?php
                        $showDefaultTip = $showDefault = $availableLabels[$content];
                        $showdefaulclass = "aaf_icon_feed_" . $content;
                    else:
                        $showDefault = $adSeprator = null;
                        foreach ($this->privacylists as $klist => $plist):
                            $showDefault.=$adSeprator . $plist;
                            if (empty($adSeprator)):
                                $adSeprator = ", ";
                            endif;
                        endforeach;
                        $showDefaultTip = $showDefault;
                        $showdefaulclass = "aaf_icon_feed_list";
                        if (count($this->privacylists) > 2):
                            if ($this->enableNetworkList <= 1):
                                $showDefault = "Custom";
                            else:
                                $showDefault = strpos($this->showDefaultInPrivacyDropdown, "network_") !== false ? "Multiple Networks" : "Multiple Friend Lists";
                            endif;
                            $showdefaulclass = "aaf_icon_feed_custom";
                        endif;

                    endif;
                    ?>
                    <div class='advancedactivity_privacy_list' id='advancedactivity_friend_list'>            
                        <span class="aaf_privacy_pulldown" id="pulldown_privacy_list" onClick="togglePrivacyPulldown(event, this)">
                            <p class="adv_privacy_list_tip adv_composer_tip">
                                <span id="adv_custom_list_privacy_lable_tip"> <?php echo $this->translate("Share with %s", $this->translate($showDefaultTip)) ?></span>
                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                            </p>
                            <a href="javascript:void(0);" id="show_default" class="aaf_privacy_pulldown_button">
                                <i class="aaf_privacy_pulldown_icon <?php echo $this->translate($showdefaulclass) ?>"></i>
                                <span><?php echo $this->translate($showDefault) ?></span>
                                <i class="aaf_privacy_pulldown_arrow"></i>
                            </a>
                            <div class="aaf_pulldown_contents_wrapper">
                                <div class="aaf_pulldown_contents">
                                    <ul> 
        <?php // if($content !='friends' || $this->enableList):  ?> 
        <?php foreach ($availableLabels as $key => $value): ?>
                                            <li class="<?php echo ( $key == $content ? 'aaf_tab_active' : 'aaf_tab_unactive' ) ?> user_profile_friend_list_<?php echo $key ?> aaf_custom_list" id="privacy_list_<?php echo $key ?>" onclick="setAuthViewValue('<?php echo $key ?>', '<?php echo $this->string()->escapeJavascript($this->translate($value)); ?>', 'aaf_icon_feed_<?php echo $key ?>')" title="<?php echo $this->translate("Share with %s", $this->translate($value)); ?>" >
                                                <i class="aaf_privacy_pulldown_icon aaf_icon_feed_<?php echo $key ?>"></i>             
                                                <div>
                                            <?php echo $this->translate($value); ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php // endif;  ?>
                                        <?php if ($this->enableList && $this->countList): ?> 
                                            <li class="sep"></li>
                                            <?php
                                            $keyId = 0;
                                            foreach ($this->lists as $list):
                                                ?>
                                                <?php
                                                if (empty($showDefault)):
                                                    $showDefault = $list->title;
                                                    $keyId = $list->list_id;
                                                endif;
                                                ?>
                                                <li class="<?php echo ( (!empty($this->privacylists) && isset($this->privacylists[$list->list_id])) ? 'aaf_tab_active' : 'aaf_tab_unactive' ) ?> user_profile_friend_list_<?php echo $list->list_id ?> aaf_custom_list" id="privacy_list_<?php echo $list->list_id ?>" onclick="setAuthViewValue('<?php echo $list->list_id ?>', '<?php echo $this->string()->escapeJavascript($this->translate($list->title)) ?>', 'aaf_icon_feed_list')" title="<?php echo $this->translate("Share with %s", $list->title); ?>">
                                                    <i class="aaf_privacy_pulldown_icon aaf_icon_feed_list"></i>                         
                                                    <div>
                                                <?php echo $this->translate($list->title) ?>
                                                    </div>
                                                </li>                   
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if ($this->enableNetworkList): ?> 
                                            <li class="sep"></li>
                                            <?php
                                            $keyId = 0;
                                            foreach ($this->network_lists as $list):
                                                if (empty($showDefault)):
                                                    $showDefault = $list->getTitle();
                                                    $keyId = $list->getIdentity();
                                                endif;
                                                ?>
                                                <li class="<?php echo ( (!empty($this->privacylists) && isset($this->privacylists["network_" . $list->getIdentity()])) ? 'aaf_tab_active' : 'aaf_tab_unactive' ) ?> user_profile_network_list_<?php echo $list->getIdentity() ?> aaf_custom_list" id="privacy_list_<?php echo "network_" . $list->getIdentity() ?>" onclick="setAuthViewValue('<?php echo "network_" . $list->getIdentity() ?>', '<?php echo $this->string()->escapeJavascript($this->translate($list->getTitle())) ?>', 'aaf_icon_feed_network_list')" title="<?php echo $this->translate("Share with %s", $list->getTitle()); ?>">
                                                    <i class="aaf_privacy_pulldown_icon aaf_icon_feed_network_list"></i>                         
                                                    <div>
                <?php echo $this->translate($list->getTitle()) ?>
                                                    </div>
                                                </li>                   
                                                <?php endforeach; ?>
                                                <?php if ($this->enableNetworkList > 1): ?> 
                                                <li class="sep"></li>
                                                <li onclick="addMoreListNetwork();" class="aaf_custom_list"
                                                    id="user_profile_network_list_custom" title="<?php echo $this->translate("Choose multiple Networks to share with."); ?>"><i class="aaf_privacy_pulldown_icon aaf_icon_feed_custom"></i><div
                                                        id="user_profile_network_list_custom_div"><?php echo $this->translate("Multiple Networks"); ?></div></li>  
                                            <?php endif; ?>
                                        <?php endif; ?> 

                                            <?php if ($this->enableList): ?>   
                                                <?php if ($this->countList > 1): ?>
                                                    <?php if ($this->enableNetworkList <= 1): ?> 
                                                    <li class="sep"></li>
                <?php endif; ?>
                                                <li onclick="addMoreList();" class="aaf_custom_list"
                                                    id="user_profile_friend_list_custom" title="<?php echo $this->translate("Choose
multiple Friend Lists to share with."); ?>"><i class="aaf_privacy_pulldown_icon aaf_icon_feed_custom"></i><div
                                                        id="user_profile_friend_list_custom_div"><?php echo $this->enableNetworkList <= 1 ? $this->translate("Custom") : $this->translate("Multiple Friend Lists"); ?></div></li>
                                            <?php else: ?>                    
                                                <li onclick="OpenPrivacySmoothBox(<?php echo $this->countList ?>);"
                                                    class="aaf_custom_list" title="<?php echo $this->translate("Choose multiple Friend Lists to share with.");
                                ?>"><i class="aaf_privacy_pulldown_icon aaf_icon_feed_custom"></i><div>
                <?php echo $this->enableNetworkList <= 1 ? $this->translate("Custom") : $this->translate("Multiple Friend Lists"); ?></div></li>
                        <?php endif; ?>
                    <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </span>            
                    </div>        
        <?php //endif; // END LIST CODE  ?>
    <?php else: ?>
                    <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $this->settingsApi->getSetting('activity.content', 'everyone'); ?>" />
    <?php endif; ?>

    <?php if (count($this->categoriesList)): ?>
                    <input type="hidden" id="category_id" name="category_id" value="0" />
                    <div class='advancedactivity_privacy_list' id='advancedactivity_categories_list'>            
                        <span class="aaf_privacy_pulldown" id="pulldown_categories_list" onClick="toggleCatPulldown(event, this)">
                            <p class="adv_privacy_list_tip adv_composer_tip">
                                <span id="adv_category_lable_tip"> <?php echo $this->translate("Select Category") ?></span>
                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                            </p>
                            <a href="javascript:void(0);" id="show_category_default" class="aaf_privacy_pulldown_button">
                                <span><?php echo $this->translate('Select Category') ?></span>
                                <i class="aaf_privacy_pulldown_arrow"></i>
                            </a>
                            <div class="aaf_pulldown_contents_wrapper">
                                <div class="aaf_pulldown_contents">
                                    <ul>
                                        <li class="aaf_tab_active user_profile_friend_list_<?php echo 0 ?> aaf_custom_list" id="category_list_<?php echo 0 ?>" onclick="setAAFCategoyValue('0', '<?php echo $this->string()->escapeJavascript($this->translate('Select Category')); ?>', 'aaf_icon_feed_0')" title="<?php echo $this->translate("Post in No Category"); ?>" >

                                            <div>
                                                <?php echo $this->translate("No Category"); ?>
                                            </div>
                                        </li>
                                        <?php foreach ($this->categoriesList as $key => $value): ?>
                                            <li class="aaf_tab_unactive user_profile_friend_list_<?php echo $value->category_id ?> aaf_custom_list" id="category_list_<?php echo $value->category_id ?>" onclick="setAAFCategoyValue('<?php echo $value->category_id ?>', '<?php echo $this->string()->escapeJavascript($this->translate($value->category_name)); ?>', 'aaf_icon_feed_<?php echo $value->category_id ?>')" title="<?php echo $this->translate("Post in %s", $this->translate($value->category_name)); ?>" >

                                                <div>
            <?php echo $this->translate($value->category_name); ?>
                                                </div>
                                            </li>
                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </span>            
                    </div> 
    <?php elseif ($this->category_id): ?>
                    <input type="hidden" id="category_id" name="category_id" value="<?php echo $this->category_id ?>" />
        <?php endif; ?>
            </div>	


        </form>

        <?php
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/composer.js');
        ?>
        <?php
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
        if (in_array("withtags", $composerOptions)):
            $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/feed-tags.js');
        endif;
        ?> 
        <script type="text/javascript">

            var lastOldTweet = 0;
            var lastOldFB = 0;
            var feedContentURL = en4.core.baseUrl + 'widget/index/name/advancedactivity.feed';
            var composeInstance;
            var active_submitrequest = 1;
            var formhtml;
            var Share_Translate = "<?php echo $this->string()->escapeJavascript($this->translate("ADVADV_SHARE")); ?>";
            var Who_Are_You_Text = "<?php echo $this->string()->escapeJavascript($this->translate("Who are you with?")); ?>";

            en4.core.runonce.add(function () {
                formhtml = $('activity-form').innerHTML;
                previousActionFilter = 'all';
                // @todo integrate this into the composer

                //  if( !DetectMobileQuick()) {
                composeInstance = new Composer('advanced_activity_body', {
                    menuElement: 'advanced_compose-menu',
                    activatorContent: "adv_post_container_icons",
                    trayElement: "compose-tray",
                    baseHref: '<?php echo $this->baseUrl() ?>',
                    userPhoto: '<?php echo $this->viewer() && (!$this->subject() || $this->subject() instanceof User_Model_User) ? $this->htmlLink($this->viewer()->getHref(), preg_replace(array('/onclick=\'(.*?)\'/is'), '', $this->itemPhoto($this->viewer(), 'thumb.icon'))) : '' ?>',
                    lang: {
                        'Post Something...': '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
                    },
                    hideSubmitOnBlur: true,
                });
                //   }

                // start for aumatic link detection.
                composeInstance.getForm().addEvent('keyup', function (e) {
                    if (e.key != 'space' || DetectMobileQuick()) {
                        return;
                    }

                    getLinkContent();
                }.bind(composeInstance));
                // end for aumatic link detection.

                var postbyAjax = "<?php echo $this->settingsApi->getSetting('advancedactivity.post.byajax', 1) ?>";
    <?php if ($this->postbyWithoutAjax): ?>
                    postbyAjax = 0;
    <?php endif; ?>
                composeInstance.getForm().addEvent('submit', function (e) {
                    composeInstance.fireEvent('editorSubmit');
                    if (DetectMobileQuick() || (activity_type == 1 && postbyAjax == 0)) {
                        return;
                    }
                    e.stop();

                    if (composeInstance.pluginReady) {
                        if (!composeInstance.options.allowEmptyWithAttachment && composeInstance.getContent().trim() == '') {
                            e.stop();
                            return;
                        }
                    } else {
                        if (!composeInstance.options.allowEmptyWithoutAttachment && composeInstance.getContent().trim() == '') {
                            e.stop();
                            return;
                        }
                    }
                    //composeInstance.saveContent();

                    if (active_submitrequest == 1) {
                        active_submitrequest = 2;
                        var submitUri = "<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>";
                        submitFormAjax(submitUri);
                    }
                }.bind(composeInstance));


            });


            var togglePrivacyPulldownEventEnable = false;
            var togglePrivacyPulldown = function (event, element) {
                event = new Event(event);

                togglePrivacyPulldownEventEnable = true;
                $$('.advancedactivity_privacy_list').each(function (otherElement) {
                    if (otherElement.id == 'advancedactivity_friend_list') {
                        return;
                    }
                    var pulldownElement = otherElement.getElement('aaf_privacy_pulldown_active');
                    if (pulldownElement) {
                        pulldownElement.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                    }
                });
                if ($(element).hasClass('aaf_privacy_pulldown')) {
                    element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
                } else {
                    element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                }
                OverText.update();
            }

            //hide on body click
            var togglePrivacyPulldownClickEvent = function () {
                var element = $('pulldown_privacy_list');

                if (!togglePrivacyPulldownEventEnable && element && $(element).hasClass('aaf_privacy_pulldown_active')) {
                    element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                }
                togglePrivacyPulldownEventEnable = false;
            }
    <?php if (count($this->categoriesList)): ?>
                var toggleCatPulldownEventEnable = false;
                var toggleCatPulldown = function (event, element) {
                    event = new Event(event);

                    toggleCatPulldownEventEnable = true;
                    $$('.advancedactivity_privacy_list').each(function (otherElement) {
                        if (otherElement.id == 'advancedactivity_friend_list') {
                            return;
                        }
                        var pulldownElement = otherElement.getElement('aaf_privacy_pulldown_active');
                        if (pulldownElement) {
                            pulldownElement.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                        }
                    });
                    if ($(element).hasClass('aaf_privacy_pulldown')) {
                        element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
                    } else {
                        element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                    }
                    OverText.update();
                }
                var toggleCategoryulldownClickEvent = function () {
                    var element = $('pulldown_categories_list');

                    if (!toggleCatPulldownEventEnable && element && $(element).hasClass('aaf_privacy_pulldown_active')) {
                        element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                    }
                    toggleCatPulldownEventEnable = false;
                }
                window.addEvent('domready', function () {
                    $(document.body).addEvent('click', toggleCategoryulldownClickEvent.bind());
                });
                var setAAFCategoyValue = function (value, label, classicon) {
                    var oldValue = $('category_id').value;
                    var oldValueArray = oldValue.split(",");
                    for (var i = 0; i < oldValueArray.length; i++) {
                        var tempListElement = $('category_list_' + oldValueArray[i]);
                        if (tempListElement)
                            tempListElement.removeClass('aaf_tab_active').addClass('aaf_tab_unactive');
                    }
                    var tempListElement = $('category_list_' + value);
                    tempListElement.addClass('aaf_tab_active').removeClass('aaf_tab_unactive');

                    $('category_id').value = value;
                    $('show_category_default').innerHTML = '<i class="aaf_privacy_pulldown_icon ' + classicon + ' "></i><span>' + label + '</span><i class="aaf_privacy_pulldown_arrow"></i>';
                    if (value == 0) {
                        $("adv_category_lable_tip").innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('Select Category')) ?>";
                    } else {
                        $("adv_category_lable_tip").innerHTML = en4.core.language.translate("<?php echo $this->string()->escapeJavascript($this->translate('Post in %s')) ?>", label);
                    }
                };

    <?php endif; ?>


            window.addEvent('domready', function () {
                $(document.body).addEvent('click', togglePrivacyPulldownClickEvent.bind());
            });

            var setAuthViewValue = function (value, label, classicon) {
                var oldValue = $('auth_view').value;
                var oldValueArray = oldValue.split(",");
                for (var i = 0; i < oldValueArray.length; i++) {
                    var tempListElement = $('privacy_list_' + oldValueArray[i]);
                    tempListElement.removeClass('aaf_tab_active').addClass('aaf_tab_unactive');
                }
                var tempListElement = $('privacy_list_' + value);
                tempListElement.addClass('aaf_tab_active').removeClass('aaf_tab_unactive');

                $('auth_view').value = value;
                $('show_default').innerHTML = '<i class="aaf_privacy_pulldown_icon ' + classicon + ' "></i><span>' + label + '</span><i class="aaf_privacy_pulldown_arrow"></i>';



                $("adv_custom_list_privacy_lable_tip").innerHTML = en4.core.language.translate("<?php echo $this->string()->escapeJavascript($this->translate('Share with %s')) ?>", label);
            }

            function addMoreList() {
                Smoothbox.open('<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'add-more-list'), 'default', true) ?>');
                var element = $('pulldown_privacy_list');
                if ($(element).hasClass('aaf_privacy_pulldown')) {
                    element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
                } else {
                    element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                }
            }

            function addMoreListNetwork() {
                Smoothbox.open('<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'add-more-list-network'), 'default', true) ?>');
                var element = $('pulldown_privacy_list');
                if ($(element).hasClass('aaf_privacy_pulldown')) {
                    element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
                } else {
                    element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
                }
            }
    <?php if (in_array("emotions", $composerOptions)) : ?>
                var hideEmotionIconClickEnable = false;
                function setEmoticonsBoard() {
                    if (composeInstance)
                        composeInstance.focus();
                    $('emotion_lable').innerHTML = "";
                    $('emotion_symbol').innerHTML = "";
                    hideEmotionIconClickEnable = true;
                    var a = $('emoticons-button');
                    a.toggleClass('emoticons_active');
                    a.toggleClass('');
                    var el = $('emoticons-board');
                    el.toggleClass('seaocore_embox_open');
                    el.toggleClass('seaocore_embox_closed');
                }

                function addEmotionIcon(iconCode) {
                    return  composeInstance.attachEmotionIcon(iconCode);
                }
                //hide on body click
                window.addEvent('domready', function () {
                    $(document.body).addEvent('click', hideEmotionIconClickEvent.bind());
                });

                function hideEmotionIconClickEvent() {
                    if (!hideEmotionIconClickEnable && $('emoticons-board')) {
                        $('emoticons-board').removeClass('seaocore_embox_open').addClass('seaocore_embox_closed');
                    }
                    hideEmotionIconClickEnable = false;
                }
                function setEmotionLabelPlate(lable, symbol) {
                    $('emotion_lable').innerHTML = lable;
                    $('emotion_symbol').innerHTML = symbol;
                }
    <?php endif; ?>
        </script>

    <?php foreach ($this->composePartials as $partial): ?>
        <?php echo $this->partial($partial[0], $partial[1], array("isAFFWIDGET" => 1)) ?>
    <?php endforeach; ?>

    </div>

    <?php
    $webcam_type = 0; // Default
    $subject = $this->subject();
    $subject_id = 0;
    if (!empty($subject)) {
        $subjectType = $subject->getType();
        $subject_id = $subject->getIdentity();
        if (!empty($subject) && strstr($subjectType, 'sitepage_page')) {
            $webcam_type = 1; // Only for Page Plugin
        } else if (!empty($subject) && strstr($subjectType, 'sitebusiness_business')) {
            $webcam_type = 2; // Only for Business Plugin
        } else if (!empty($subject) && strstr($subjectType, 'sitegroup_group')) {
            $webcam_type = 3; // Only for Site Group Plugin
        } else if (!empty($subject) && strstr($subjectType, 'sitestore_store')) {
            $webcam_type = 4; // Only for Site Group Plugin
        }
    }

    $is_webcam_enable = false;
    $getSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy", "webcam"));
    if (in_array('webcam', $getSettings)) {
        $is_webcam_enable = true;
    }
    ?>
    <script type="text/javascript">
        if (!DetectMobileQuick()) {
            var _is_webcam_enable = '<?php echo $is_webcam_enable; ?>';
            var _aaf_webcam_type = '<?php echo $webcam_type; ?>';
            var _subject_id = '<?php echo $subject_id; ?>';
        }

        var OpenPrivacySmoothBox = function (count) {
            var msg = "";
            if (count == 0) {
                msg = "<div class='aaf_show_popup'><div class='tip'><span>" +
                        "<?php echo $this->string()->escapeJavascript($this->translate('You have currently not organized your friends into lists. To create new friend lists, go to the "Friends" section of ')); ?>" +
                        "<a href='<?php echo $this->viewer()->getHref() ?>' ><?php echo
    $this->string()->escapeJavascript($this->translate("your profile"))
    ?></a><?php echo
    $this->string()->escapeJavascript($this->translate("."))
    ?>" +
                        "</span></div><div><button href=\"javascript:void(0);\" onclick=\"javascript:parent.Smoothbox.close()\"><?php echo $this->translate("Close"); ?></button></div>" +
                        "</div></div>";
            } else {
                msg = "<div class='aaf_show_popup'><div class='tip'><span>" +
                        "<?php echo $this->string()->escapeJavascript($this->translate('You have currently created only one list to organize your friends. Create more friend lists from the "Friends" section of ')); ?>" +
                        "<a href='<?php echo $this->viewer()->getHref() ?>' ><?php echo
    $this->string()->escapeJavascript($this->translate("your profile"))
    ?></a><?php echo
    $this->string()->escapeJavascript($this->translate("."))
    ?>" +
                        "</span></div><div><button href=\"javascript:void(0);\" onclick=\"javascript:parent.Smoothbox.close()\"><?php echo $this->translate("Close"); ?></button></div>" +
                        "</div></div>";
            }
            Smoothbox.open(msg);
        }
        en4.core.runonce.add(function () {
            en4.core.language.addData({
                "with": "<?php echo $this->string()->escapeJavascript($this->translate("with")); ?>",
                "and": "<?php echo $this->string()->escapeJavascript($this->translate("and")); ?>",
                "others": "<?php echo $this->string()->escapeJavascript($this->translate("others")); ?>",
                "Tweet": "<?php echo $this->string()->escapeJavascript($this->translate("Tweet")); ?>"
            });
            label = $("compose-container").getElement('label').setStyle('top', "3px");
            //if (activity_type == 1) {
            if (window.checkFB) {
                checkFB();
            }

            if (window.checkTwitter) {
                checkTwitter();
            }

            if (window.checkLinkedin) {
                checkLinkedin();
            }
            // }
            doAttachment();
        });
    </script>
<?php endif; ?>

<script type="text/javascript">

    function showPhotoActivator() {

        if (composeInstance.getPlugin('video')) {
            composeInstance.getPlugin('video').deactivate();
        }

        if (composeInstance.getPlugin('photo')) {
            composeInstance.getPlugin('photo').deactivate();
        }

        composeInstance.getPlugin('photo').activate();

        if ($('compose-video-menu-link')) {
            $('compose-video-menu-link').removeClass('active');
        }
        if ($('compose-status-menu-link')) {
            $('compose-status-menu-link').removeClass('active');
        }
        if ($('compose-photo-menu-link')) {
            $('compose-photo-menu-link').addClass('active');
        }

        if (document.getElementById('compose-webcam-body')) {
            document.getElementById('compose-webcam-body').destroy();
        }
        setTimeout("aafWebcam('compose-tray', 0)", 100);
    }

    function showVideoActivator() {

        if (composeInstance.getPlugin('video')) {
            composeInstance.getPlugin('video').deactivate();
        }

        if (composeInstance.getPlugin('photo')) {
            composeInstance.getPlugin('photo').deactivate();
        }

        if ($('compose-video-menu-link')) {
            $('compose-video-menu-link').addClass('active');
        }
        if ($('compose-status-menu-link')) {
            $('compose-status-menu-link').removeClass('active');
        }
        if ($('compose-photo-menu-link')) {
            $('compose-photo-menu-link').removeClass('active');
        }

        composeInstance.getPlugin('video').activate();
        if (document.getElementById('compose-webcam-body')) {
            document.getElementById('compose-webcam-body').destroy();
        }
    }

    function showDefaultActivator() {
        if (composeInstance.getPlugin('photo')) {
            composeInstance.getPlugin('photo').deactivate();
        }
        if (composeInstance.getPlugin('video')) {
            composeInstance.getPlugin('video').deactivate();
        }

        if ($('compose-video-menu-link')) {
            $('compose-video-menu-link').removeClass('active');
        }
        if ($('compose-status-menu-link')) {
            $('compose-status-menu-link').addClass('active');
        }
        if ($('compose-photo-menu-link')) {
            $('compose-photo-menu-link').removeClass('active');
        }
        if ($$('.compose-container')[0].getElement('.overTxtLabel'))
            $$('.compose-container')[0].getElement('.overTxtLabel').innerHTML = en4.core.language.translate('Post Something...');
    }
</script>

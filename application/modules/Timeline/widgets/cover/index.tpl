<?php 
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/core.js'); 
    //$this->headTranslate(array("To Top"));
?>
<script type="text/javascript">
    en4.core.runonce.add(function()
        {
            new TimeLineScroller('timeline_cover_box', 'timeline_scroll_box');
        });
</script>
<div class="cover_user_profile_<?php echo $this->avatar_position?>" id="timeline_cover_box">
    <?php if ($this->is_owner): ?>
        <div class="manage_cover">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'timeline', 'controller' => 'smoothbox', 'action' => 'upload-cover'),  $this->translate('Change Cover'), array('class' => 'smoothbox add_cover_icon')) ?>
            <?php
                $params = array('class' => 'smoothbox delete_cover_icon',
                                'id' => 'timeline_delete_cover');
                if (!Engine_Api::_()->timeline()->hasTimeLineCover($this->owner_user))
                    $params['style'] = 'display:none;';
                    echo $this->htmlLink(array('route' => 'default', 'module' => 'timeline', 'controller' => 'smoothbox', 'action' => 'delete-cover'),  $this->translate('Delete Cover'), $params) ?>
        </div>
    <?php endif;?>
    
    <div class="cover_image" >
        <img style="max-height: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_height', '250') ?>px !important;" alt="Timeline Cover"  src="<?php echo $this->cover ?>" id="timeline_cover" />
    </div>
    
    <div class="user_info">
        <div class="user_profile_photo">
            <?php if ($this->is_owner):?>
                <div class="manage_prof_photo">
                    <a href="javascript:void(0)" class="manage_prof_photo_btn"></a>
                    <div class="pulldown_contents_wrapper">
                        <div class="pulldown_contents">
                            <ul>
                                <?php if ($this->web_cam_on):?>
                                    <li>
                                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'avatar', 'controller' => 'edit', 'action' => 'getwebcam'), $this->translate('Take a WebCam Photo'), array('class' => 'smoothbox webcam_button_link' )) ?>
                                    </li>
                                <?php endif;?>
                                <?php if ($this->avatar_collection_on):?>
                                    <li>
                                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'avatar', 'controller' => 'edit', 'action' => 'getavatarcollection'), $this->translate('Select Avatar'), array('class' => 'smoothbox webcam_button_link2' )) ?>
                                    </li>
                                <?php endif;?>
                                <li>
                                    <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'photo'), $this->translate('Edit Photo'), array('class' => 'editphoto_button_link' )) ?>
                                </li>
                                <li>
                                    <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'profile'), $this->translate('Edit My Profile'), array('class' => 'edit_profile_icon buttonlink' )) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (empty($this->owner_user->photo_id))
                    echo $this->itemPhotoThumb($this->owner_user, 'thumb.profile');
                  else
                    echo $this->htmlLink("javascript:void(0);", $this->itemPhotoThumb($this->owner_user, 'thumb.profile'), array('onclick' => 'javascript:Smoothbox.open( $("thumb_profile_smooth"), {mode: "TimeLineImage"} );')) ;
            ?>
        </div>
        
        

        
        <p class="user_name">
            <?php echo $this->owner_user->getTitle() ?>
            <?php if ($this->like_profile && $this->is_enabled && $this->is_allowed): ?>
                <?php echo $this->likeButton($this->subject()); ?>
            <?php endif;?>
        </p>
        <?php if (!empty($this->owner_user->status) and $this->auth):?>
            <div class="user_status" id="user_profile_status_container">
                <span class="arrow"></span>
                <?php echo $this->viewMore($this->owner_user->status) ?>
                <?php if ($this->is_owner):?>
                    <a class="profile_status_clear" href="javascript:void(0);" onclick="javascript:en4.user.clearStatus();$('user_profile_status_container').dispose();">(<?php echo $this->translate('clear') ?>)</a>
                <?php endif;?>
            </div>
        <?php endif;?> 
        
        <div class="user_menu">

            <?php
                if( !empty($this->menu_params) ) :
                    $navigation = new Zend_Navigation();
                    $navigation->addPages($this->menu_params);
            ?>
                    <?php echo $this->navigation()->menu()->setContainer($navigation)
                                                          ->setPartial(array('_navIcons.tpl', 'core'))
                                                          ->render(); ?>
            <?php endif; ?>
            <?php
                if( !empty($this->addition_menu_params) ) :
                    $navigation = new Zend_Navigation();
                    $navigation->addPages($this->addition_menu_params);
            ?>

                    <div class="prop_menu">
                        <a href="javascript:void(0)" class="prop_menu_btn"></a>
                        <div class="pulldown_contents_wrapper">
                            <div class="pulldown_contents">
                                <?php echo $this->navigation()->menu()->setContainer($navigation)
                                                              ->setPartial(array('_navIcons.tpl', 'core'))
                                                              ->render(); ?>
                            </div>
                        </div>
                    </div>

            <?php endif; ?>

                
        </div>
        

        <div class="clr"></div>
    </div>
    
</div>
<div style="display: none;overflow:hidden;" id="thumb_profile_smooth">
    <div class="timeline-avatar"><?php echo $this->itemPhoto($this->owner_user, null);?></div>
    <div class="timeline-close-bn"><button value="Close" name="close_button" onclick="parent.Smoothbox.close();return false;" type="submit"><?php echo $this->translate("Close"); ?></button></div>
</div>
<div id="timeline_scroll_box" style="display: none;">
    <div class="scroll_box_photo"><?php echo $this->itemPhotoThumb($this->owner_user, 'thumb.icon');?></div>
    
        <div class="user_name">
            <?php echo $this->owner_user->getTitle() ?>
        </div>    
        

    <div class="user_menu">

            <?php
                if( !empty($this->menu_params) ) :
                    $navigation = new Zend_Navigation();
                    $navigation->addPages($this->menu_params);
            ?>
                    <?php echo $this->navigation()->menu()->setContainer($navigation)
                                                          ->setPartial(array('_navIcons.tpl', 'core'))
                                                          ->render(); ?>
            <?php endif; ?>
            


        
            <?php
                if( !empty($this->addition_menu_params) ) :
                    $navigation = new Zend_Navigation();
                    $navigation->addPages($this->addition_menu_params);
            ?>

                    <div class="prop_menu">
                        <div class="pulldown_contents_wrapper">
                            <div class="pulldown_contents">
                                <?php echo $this->navigation()->menu()->setContainer($navigation)
                                                              ->setPartial(array('_navIcons.tpl', 'core'))
                                                              ->render(); ?>
                            </div>
                        </div>
                    </div>

            <?php endif; ?>

                
        </div>
</div>
<?php if( !$this->auth ): ?>
    <br />  
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.');?>
    </span>
  </div>
  <br />
<?php endif; ?>
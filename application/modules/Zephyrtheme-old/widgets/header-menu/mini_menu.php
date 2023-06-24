    <!-- START - Mini Menu -->  
    <div class="generic_layout_container layout_core_menu_mini"> 
      <div id="core_menu_mini_menu">   
        <ul>
        <?php if( !$this->viewer->getIdentity() ) { ?>
          <li class="hide_on_login">
            <a href="<?php echo ($this->header_loginbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'login'), 'default', true); ?>" class="button_login"><?php echo $this->translate("Login"); ?></a>
          </li>
          <li class="hide_on_signup">
            <a href="<?php echo ($this->header_signupbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'signup'), 'default', true); ?>" class="button_signup"><?php echo $this->translate("Sign Up"); ?></a>
          </li>
          <?php } ?>
        
          <?php if( $this->viewer->getIdentity() ) { ?>
          
          <?php if(in_array('0', $header_iconsshow)) { ?>
          <li class="minimenu_profile_button">
            <?php echo $this->htmlLink($this->viewer()->getHref(), 
                      $this->itemPhoto($this->viewer(), 'thumb.icon'), array('title'=>$this->translate('My Profile'))); ?>
          </li>
          <?php } ?>
          
          <!-- START - Notifications Box -->  
          <li class="updates_pulldown icon_type_pulldown" id="core_menu_mini_menu_update" >
              <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ) { ?> class="new_updates"<?php } ?> title="<?php echo $this->translate('My Notifications') ?>">
                <span class="pulldown_toggle_icon updates_toggle_icon"></span><span id="updates_span"><?php echo $this->notificationCount; ?></span>
              </a>
              <div class="pulldown_contents_wrapper" id="pulldown_updates_wrapper">
                <div class="pulldown_contents">
                  <h3>
					<?php echo $this->translate("My Notifications") ?>
                    <ul class="pulldown_options_header">
                      <li><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                         $this->translate('View All'), array('id' => 'notifications_viewall_link')) ?></li>
                      <li><?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                        'id' => 'notifications_markread_link')) ?></li>
                    </ul>
                  </h3>
                  <ul class="notifications_menu" id="notifications_menu">
                    <div class="notifications_loading" id="notifications_loading">
                      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                      <?php echo $this->translate("Loading ..."); ?>
                    </div>
                  </ul>
                </div>
              </div>
          </li>
          <!-- END - Notifications Box --> 
          
          <!-- START - Messages -->
          <li class="messages_pulldown icon_type_pulldown" id="messages_pulldown">
              <a href="javascript:void(0);" id="messages_toggle" <?php if( $this->messageCount ) { ?>class="new_updates"<?php } ?> title="<?php echo $this->translate('Messages') ?>">
                <span class="pulldown_toggle_icon messages_toggle_icon"></span><span id="messages_span"><?php echo $this->messageCount; ?></span>
              </a>
              <div class="pulldown_contents_wrapper" id="messages_pulldown_wrapper">
                <div class="pulldown_contents">
                  <h3>
					<?php echo $this->translate("Messages") ?>
                    <ul class="pulldown_options_header">
                      <li><a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general') ?>"><?php echo $this->translate("View All") ?></a></li>
                      <li><a href="<?php echo $this->url(array('action' => 'compose'), 'messages_general') ?>" class="smoothbox"><?php echo $this->translate('Compose'); ?></a></li>
                    </ul>
                  </h3>
                  <ul class="notifications_menu" id="messages_menu">
                    <div class="notifications_loading" id="messages_loading">
                      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                      <?php echo $this->translate("Loading ..."); ?>
                    </div>
                  </ul>
                </div>
              </div>
          </li>
          <!-- END - Messages -->
          
          <!-- START - Friends Requests -->
          <li class="requests_pulldown icon_type_pulldown" id="requests_pulldown">
              <a href="javascript:void(0);" id="requests_toggle" <?php if( $this->requestCount ) { ?> class="new_updates"<?php } ?> title="<?php echo $this->translate('Friend Requests') ?>">
                <span class="pulldown_toggle_icon requests_toggle_icon"></span><span id="requests_span"><?php echo $this->requestCount; ?></span>
              </a>
              <div class="pulldown_contents_wrapper" id="requests_pulldown_wrapper">
                <div class="pulldown_contents">
                  <h3>
					<?php echo $this->translate("Friend Requests") ?>
                    <ul class="pulldown_options_header">
                      <li><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                         $this->translate('View All'), array('id' => 'notifications_viewall_link')) ?></li>
                    </ul>
                  </h3>
                  <ul class="notifications_menu" id="requests_menu">
                    <div class="notifications_loading" id="requests_loading">
                      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                      <?php echo $this->translate("Loading ..."); ?>
                    </div>
                  </ul>
                </div>
              </div>
          </li>
          <!-- END - Friends Requests -->
          
          <!-- START - Mini Menu Links Box -->  
          <li class="minimenu_pulldown icon_type_pulldown" id="minimenu_pulldown">
              <a id="minimenu_toggle" href="javascript:void(0);" title="<?php echo $this->translate('My Account') ?>">
                <span class="pulldown_toggle_icon minimenu_toggle_icon"></span><span><?php echo $this->translate('My Account'); ?></span>
              </a>
              <div class="pulldown_contents_wrapper" id="minimenu_pulldown_wrapper"><div class="pulldown_contents">
                <h3><?php echo $this->translate("My Account") ?></h3>
                <ul>
                  <?php foreach( $this->profilenavigation as $item ) { ?>
                    <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
                      'class' => ( !empty($item->class) ? $item->class : null ),
                      'alt' => ( !empty($item->alt) ? $item->alt : null ),
                      'target' => ( !empty($item->target) ? $item->target : null ),
                    ))) ?></li>
                  <?php } ?>
                </ul>
                <div class="sep"></div>
                <?php
                  $count = count($this->mininavigation);
                  foreach( $this->mininavigation->getPages() as $item ) $item->setOrder(--$count);
                ?>
                <ul>
                  <?php foreach( $this->mininavigation as $item ) { ?>
                    <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
                      'class' => ( !empty($item->class) ? $item->class : null ),
                      'alt' => ( !empty($item->alt) ? $item->alt : null ),
                      'target' => ( !empty($item->target) ? $item->target : null ),
                    ))) ?></li>
                  <?php } ?>
                </ul>
              </div></div>
          </li>
          <!-- END - Mini Menu Links Box -->
          
          <?php if(in_array('1', $header_iconsshow)) { ?>
          <li class="minimenu_logout icon_type_simple">
            <a href="<?php echo $this->url(array('module'=>'','controller' => '','action'=>'logout'), 'default', true); ?>" title="<?php echo $this->translate("Logout"); ?>"><span><?php echo $this->translate("Logout"); ?></span></a>
          </li>
          <?php } ?> 
          <?php } ?> 
        
		  <?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) { ?>  
          <li class="minimenu_mobile">
            <a href="javascript:void(0);" id="minimenu_mobile_toggle">
              <span class="minimenu_mobile_icon"></span>
              <span><?php echo $this->translate("Menu"); ?></span>
            </a>
          </li>
		  <?php } ?>
        </ul>
      </div>
    </div>
    <!-- END - Mini Menu -->
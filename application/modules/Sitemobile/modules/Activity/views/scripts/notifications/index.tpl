<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php  $itemCount = $this->requests->getTotalItemCount(); ?>

<?php if (empty($this->showrequest)): ?>
    <script type="text/javascript">
        var autoScrollNotificationEnable =<?php echo ($this->autoScrollNotificationEnable ? 'true' : 'false') ?>;
        var loadNotificationCall = <?php echo ( $this->notifications->getTotalItemCount() > 10 ? 'true' : 'false' ) ?>;
        var notificationPageCount = <?php echo sprintf('%d', $this->notifications->count()); ?>;
        var notificationPage = <?php echo sprintf('%d', $this->notifications->getCurrentPageNumber()); ?>;
        var loadMoreNotifications = function() {
            $('#notifications_viewmore').css('display', 'none');
            $('#notifications_loading_main').css('display', 'block');
            window.onscroll = '';
            notificationPage++;
            $.ajax({
                type: "GET",
                dataType: "html",
                url: sm4.core.baseUrl + 'activity/notifications/pulldown',
                data: {'page': notificationPage, 'format': 'html'},
                success: function(responseHTML, textStatus, xhr) {
                    $(document).data('loaded', true);
                    $('#notifications_loading_main').css('display', 'none');
                    if ('' != responseHTML.trim() && notificationPageCount > notificationPage) {
                        $('#notifications_viewmore').css('display', 'block');
                        if (autoScrollNotificationEnable)
                            window.onscroll = doOnScrollLoadNotifications;
                    }
                    $('#notifications_main').append(responseHTML).listview().listview('refresh');
                    sm4.core.runonce.trigger();
                    sm4.core.refreshPage();
                }
            })
        };

        sm4.core.runonce.add(function() {
            if ($('#notifications_viewmore_link')) {
                $('#notifications_viewmore_link').bind('click', function() {
                    $('#notifications_viewmore').css('display', 'none');
                    $('#notifications_loading_main').css('display', '');
                    loadMoreNotifications();
                });
            }

            if ($('#notifications_markread_link_main')) {
                $('#notifications_markread_link_main').bind('click', function() {
                    $('#notifications_markread_main').css('display', 'none');
                    sm4.activity.hideNotifications('<?php echo $this->translate("0 Updates"); ?>');
                    $('.sm-mini-menu-icon').find('a:first-child').find('span:last-child').remove();
                });
            }

            $('#notifications_main').bind('click', function(event) {
              //  $.mobile.showPageLoadingMsg();
                 $.mobile.loading().loader("show");
                event.preventDefault(); //Prevents the browser from following the link.

                var current_link = $(event.target);

                var notification_li = $(current_link).parents('li');

                var forward_link;
                if (current_link.attr('href')) {
                    forward_link = current_link.attr('href');
                } else {
                    forward_link = notification_li.find('a:last-child').attr('href');
                }

                if (forward_link) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: sm4.core.baseUrl + 'activity/notifications/markread',
                        data: {
                            format: 'json',
                            'actionid': notification_li.attr('value')
                        },
                        success: function(response) {
                            notification_li.removeClass('sm-ui-lists-highlighted');
                            if(sm4.core.isApp())
                          $.mobile.changePage(sm4.core.baseUrl+forward_link);
    			else
    		      $.mobile.changePage(forward_link);
                        }});
                }
            });

        });

        if (autoScrollNotificationEnable) {
            if (loadNotificationCall && notificationPageCount > notificationPage) {
                window.onscroll = doOnScrollLoadNotifications;
                $('#notification_loading_main').css('display', 'block');
            } else {
                window.onscroll = "";
                $('#notification_loading').css('display', 'none');
            }
        }

        function doOnScrollLoadNotifications() {
            if (loadNotificationCall == false && (notificationPageCount < notificationPage))
                return;
            if (($.type($('#notifications_viewmore').get(0)) != 'undefined')) {
                if ($.type($('#notifications_viewmore').get(0).offsetParent) != 'undefined') {
                    var elementPostionY = $('#notifications_viewmore').get(0).offsetTop;
                } else {
                    var elementPostionY = $('#notifications_viewmore').get(0).y;
                }

                if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
                    loadMoreNotifications();
                }
            }
        }
    </script>
<?php endif; ?>

<?php if (empty($this->isajax)): ?>
  <div class="tabs">
    <div data-role="navbar">
        <ul>
            <li>
                <a href="<?php echo $this->url(array(''), 'recent_activity', true); ?>" <?php if (empty($this->showrequest)): ?> class="ui-btn-active ui-state-persist" <?php endif; ?> >
                    <?php echo $this->translate("Recent Updates") ?>
                </a>
            </li>

            <li>
                <a href="<?php echo $this->url(array(), 'recent_request', true); ?>" <?php if (!empty($this->showrequest)): ?> class="ui-btn-active ui-state-persist" <?php endif; ?>>
                    <?php echo $this->translate(array("My Request (%d)", "My Requests (%d)", $itemCount), $itemCount) ?>
                </a>
            </li>
        </ul>
    </div>
  </div>
<?php endif; ?>

<?php if (empty($this->showrequest)): ?>
    <div class="sm-content-list">	

        <?php if ($this->notifications->getTotalItemCount() > 0): ?>
            <?php
            foreach ($this->notifications as $notification):
                if (!$notification->read): $this->hasunread = true;
                endif;
            endforeach;
            ?>
        <?php endif; ?>
        <?php if ($this->hasunread): ?>
            <div class="notifications_markread" id="notifications_markread_main">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                    'id' => 'notifications_markread_link_main',
                    'class' => 'buttonlink notifications_markread_link',
                    'data-role' => 'button', 'data-mini' => 'true', 'data-inline' => 'true', 'data-icon' => 'ok', 'data-theme' => 'd'
                ))
                ?>
            </div>
        <?php endif; ?>

        <ul class='sm-ui-lists' id="notifications_main" data-role="listview" data-icon="flase">
            <?php if ($this->notifications->getTotalItemCount() > 0): ?>
                <?php
                foreach ($this->notifications as $notification):
                    /* Check for display notifications of only enabled sitemobile modules, If Module is Suggestion then display only 
                      suggestions of enabled modules.
                     */
                    $notification_object_type = explode("_", $notification->object_type);

                    if (!in_array($notification_object_type[0], $this->enabledModuleNames)) {
                        continue;
                    } elseif ($notification_object_type[0] == 'suggestion') {

                        $suggObj = Engine_Api::_()->getItem('suggestion', $notification->object_id);
                        $suggestionModule = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
                        foreach ($suggestionModule as $sugModName) {
                            $sugModNameEnabled = $sugModName['pluginName'];
                            break;
                        }
                        if (!in_array($sugModNameEnabled, $this->enabledModuleNames))
                            continue;
                    }/* End Checks */
                    ob_start();
                    try {
                        ?>
                        <li class="<?php if (!$notification->read): ?> sm-ui-lists-highlighted<?php $this->hasunread = true; ?> <?php endif; ?> notification_link" value="<?php echo $notification->getIdentity(); ?>">
                            <div class="ui-btn">
                                <?php if ($notification->subject_type == 'user'): ?>
                                    <?php $item = Engine_Api::_()->getItem('user', $notification->subject_id); ?>
                                    <?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon') ?>
                                <?php elseif ($notification->subject_type == 'sitepage_page' || $notification->subject_type == 'sitebusiness_business' || $notification->subject_type == 'sitegroup_group'): ?>
                                    <?php if ($notification->subject_type == 'sitepage_page'): ?>
                                        <?php $item = Engine_Api::_()->getItem('sitepage_page', $notification->subject_id); ?> 
                                    <?php elseif ($notification->subject_type == 'sitebusiness_business'): ?>
                                        <?php $item = Engine_Api::_()->getItem('sitebusiness_business', $notification->subject_id); ?> 
                                    <?php elseif ($notification->subject_type == 'sitegroup_group'): ?>
                                        <?php $item = Engine_Api::_()->getItem('sitegroup_group', $notification->subject_id); ?> 
                                    <?php endif; ?>
                                    <?php echo $this->itemPhoto($item, 'thumb.icon') ?>
                                <?php endif; ?>
                                <h3><?php echo $notification->__toString() ?></h3>
                                <p class="sm-ui-notification-date">
                                    <i class="sm-ui-notification-icon notification_type_<?php echo $notification->type ?>"></i>
                                    <?php echo $this->timestamp($notification->date) ?>
                                </p>
                            </div>
                        </li>
                        <?php
                    } catch (Exception $e) {
                        ob_end_clean();
                        if (APPLICATION_ENV === 'development') {
                            echo $e->__toString();
                        }
                        continue;
                    }
                    ob_end_flush();
                endforeach;
                ?>
            <?php else: ?>
                <li>
                    <div class="tip">
                        <span><?php echo $this->translate("You have no notifications.") ?></span>
                    </div>	
                </li>
            <?php endif; ?>
        </ul>

        <div class="sm-ui-lists-options">

            <div class="feed_viewmore" id="notifications_viewmore"  <?php if ($this->notifications->getTotalItemCount() > 10): ?> style="display:block" <?php else: ?> style="display:none" <?php endif; ?>>
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                    'class' => 'ui-btn-default icon_viewmore',
                    'id' => 'notifications_viewmore_link'
                ))
                ?>
            </div>

            <div class="feeds_loading" id="notifications_loading_main" style="display: none;">
                <i class="icon_loading"></i>
            </div>
        </div>

    </div>
<?php else: ?>
    <div class="sm-content-list">
        <ul class='sm-ui-lists requests_main'  data-role="listview" data-icon="flase">   
            <?php $this->requests->setItemCountPerPage(30); ?>
           <?php  if ($this->requests->getTotalItemCount() > 0 ): ?>
                <?php foreach ($this->requests as $notification): ?>
                    <?php
                    /* Check for display notifications of only enabled sitemobile modules, If Module is Suggestion then display only 
                      suggestions of enabled modules.
                     */
                    $notification_object_type = explode("_", $notification->object_type);

                    if (!in_array($notification_object_type[0], $this->enabledModuleNames)) {
                        continue;
                    } elseif ($notification_object_type[0] == 'suggestion') {

                        $suggObj = Engine_Api::_()->getItem('suggestion', $notification->object_id);
                        $suggestionModule = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
                        foreach ($suggestionModule as $sugModName) {
                            $sugModNameEnabled = $sugModName['pluginName'];
                            break;
                        }
                        if (!in_array($sugModNameEnabled, $this->enabledModuleNames))
                            continue;
                    }/* End Checks */

                    try {
                        $parts = explode('.', $notification->getTypeInfo()->handler);
                        echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
                    } catch (Exception $e) {
                        if (APPLICATION_ENV === 'development') {
                            echo $e->__toString();
                        }
                        continue;
                    }
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li>
                    <div class="tip">
                        <span><?php echo $this->translate("You have no requests.") ?></span>
                    </div>	
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
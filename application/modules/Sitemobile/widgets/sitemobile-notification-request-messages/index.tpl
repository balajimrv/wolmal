<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$filter = rand(10000, 99999);
?>
<?php if ($this->showOnlyCountLeft): ?>
    <?php if ($this->totalCount): ?>
        <span class="count-bubble total-count-left-fix-bubble" ><?php echo $this->totalCount ?></span>
    <?php endif; ?>
<?php else: ?>
    <?php if (!empty($this->loadingViaAjax)) : ?>
        <div class="sm-mini-menu">
            <?php
            $session = new Zend_Session_Namespace();
            if (!isset($session->hideHeaderAndFooter) || (isset($session->hideHeaderAndFooter) && empty($session->hideHeaderAndFooter))):
                ?>
                <a id="recentRequestId" data-rel="#recent_requests"  href="javascript://" onclick='showRecentRequestContent();'  class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">        <span class="ui-icon ui-icon-user"></span>
                    <?php if (!empty($this->requestsCount)): ?>
                        <span class="count-bubble"><?php echo "$this->requestsCount" ?></span>
                    <?php endif; ?>
                </a>
                <a id="recentMessageId" href="javascript://" data-rel="#messages_popup" onclick='showMessagesContent();'  class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">
                    <span class="ui-icon ui-icon-envelope"></span>
                    <?php if (!empty($this->messageCount)): ?>
                        <span class="count-bubble"><?php echo "$this->messageCount" ?></span>
                    <?php endif; ?>
                </a>
                <a id="recentActivityId" href="javascript://" data-rel="#recent_activity" data-content="recent_activity" onclick='showUpdatesContent();' class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">
                    <span class="ui-icon ui-icon-globe"></span>
                    <?php if (!empty($this->notificationCount)): ?>
                        <span class="count-bubble"><?php echo "$this->notificationCount" ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <?php if ($this->showCartIcon): ?>
                <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>"  class="sm-mini-menu-icon" style="z-index: 9999">
                    <span class="ui-icon ui-icon-shopping-cart"></span>
                    <?php if (!empty($this->cartProductCounts)): ?>
                        <span class="count-bubble"><?php echo "$this->cartProductCounts" ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if ($this->location): ?>
                <?php if ($this->locationSpecific): ?>
                    <a class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999" id="locationPopup"href="#locationPopup_<?php echo $filter ?>" data-rel="popup" class="ui-btn ui-btn-inline ui-corner-all">
                        <span class="ui-icon ui-icon-map-marker"></span>
                    </a>
                <?php else: ?>
                    <a class="sm-mini-menu-icon popup_attach_notification" href="#popupBasic_<?php echo $filter ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="change_location_link f_small">
                        <span class="ui-icon ui-icon-map-marker"></span>
                    </a> 
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="sm-mini-menu">
            <a href="<?php echo $this->url(array(), 'recent_request', true); ?>" class="sm-mini-menu-icon">
                <span class="ui-icon ui-icon-user"></span>
                <?php if (!empty($this->requestsCount)): ?>
                    <span class="count-bubble"><?php echo "$this->requestsCount" ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general', true); ?>" class="sm-mini-menu-icon">
                <span class="ui-icon ui-icon-envelope"></span>
                <?php if (!empty($this->messageCount)): ?>
                    <span class="count-bubble"><?php echo "$this->messageCount" ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo $this->url(array(''), 'recent_activity', true); ?>" data-content="recent_activity"  class="sm-mini-menu-icon">
                <span class="ui-icon ui-icon-globe"></span>
                <?php if (!empty($this->notificationCount)): ?>
                    <span class="count-bubble"><?php echo "$this->notificationCount" ?></span>
                <?php endif; ?>
            </a>
            <?php if ($this->showCartIcon): ?>
                <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>"  class="sm-mini-menu-icon" style="z-index: 9999">
                    <span class="ui-icon ui-icon-shopping-cart"></span>
                    <?php if (!empty($this->cartProductCounts)): ?>
                        <span class="count-bubble"><?php echo "$this->cartProductCounts" ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if ($this->location): ?>
                <?php if ($this->locationSpecific): ?>
                    <a class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999" id="locationPopup"href="#locationPopup_<?php echo $filter ?>" data-rel="popup" class="ui-btn ui-btn-inline ui-corner-all">
                        <span class="ui-icon ui-icon-map-marker"></span>
                    </a>
                <?php else: ?>
                    <a class="sm-mini-menu-icon popup_attach_notification" href="#popupBasic_<?php echo $filter ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="change_location_link f_small">
                        <span class="ui-icon ui-icon-map-marker"></span>
                    </a> 
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->loadingViaAjax)) : ?>
        <div data-role="popup" id="recent_activity" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-theme="none">
            <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
            <div class="sm-ui-popup-top sm-pulldown-header">
                <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'settings', "action" => "notifications"), $this->translate(''), array('id' => '', 'class' => 'ui-icon ui-icon-cog')) ?>
                <span class="sm-pulldown-heading"><?php echo $this->translate("Notifications"); ?> </span>
            </div>
            <div class="sm-ui-popup-container-wrapper ui-body-c">
                <div class="sm-ui-popup-container sm-content-list" style="overflow:auto">	
                    <ul class="notifications_menu sm-ui-lists" id="notifications_menu">
                        <div class="sm-ui-popup-loading" id="notifications_loading"></div>
                    </ul>
                </div>
                <div class="sm-ui-popup-notification-footer">
                    <center> <?php
        echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'), $this->translate('View All Updates'), array('id' => ''))
                ?></center>
                </div>
            </div>
        </div>
        <div data-role="popup" id="recent_requests" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-theme="none">
            <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
            <div class="sm-ui-popup-top sm-pulldown-header">
                <a href="<?php echo $this->url(array('action' => 'browse'), 'user_general', true); ?>" class="ui-icon ui-icon-plus"></a>
                <span class="sm-pulldown-heading"><?php echo $this->translate('Requests'); ?></span>
            </div>
            <div class="sm-ui-popup-container-wrapper ui-body-c">
                <div class="sm-ui-popup-container sm-content-list">
                    <ul class="notifications_menu sm-ui-lists" id="recent_request_menu">
                        <div class="sm-ui-popup-loading" id="recent_request_loading"></div>
                    </ul>
                </div>
                <div class="sm-ui-popup-notification-footer">
                    <center>
                        <a href="<?php echo $this->url(array(), 'recent_request', true); ?>"><?php echo $this->translate('View All Requests'); ?></a></center>
                </div>
            </div>
        </div>

        <div data-role="popup" id="messages_popup" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-theme="none">
            <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
            <div class="sm-ui-popup-top sm-pulldown-header">
                <a href="<?php echo $this->url(array('action' => 'compose'), 'messages_general', true); ?>" class="ui-icon ui-icon-edit"></a>
                <span class="sm-pulldown-heading"><?php echo $this->translate("Messages"); ?></span>
            </div>
            <div class="sm-ui-popup-container-wrapper ui-body-c">
                <div class="sm-ui-popup-container">
                    <ul class="notifications_menu" id="messages_popup_menu">
                        <div class="sm-ui-popup-loading" id="messages_popup_loading"></div>
                    </ul>
                </div>
                <div class="sm-ui-popup-notification-footer">
                    <center>
                        <a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general', true); ?>"><?php echo $this->translate('View All Messages'); ?></a>
                    </center>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function showUpdatesContent() {
                //  var popup=$('#recent_activity-popup');
                // resizePopup(popup,{maxwidth:400,maxheight:410});
                // sm4.activity.notificationCountUpdate($.mobile.activePage);
                $.ajax({
                    type: "GET",
                    'url': sm4.core.baseUrl + 'activity/notifications/pulldown',
                    dataType: "html",
                    'data': {
                        'format': 'html',
                        'page': 1,
                        'isajax': 1
                    },
                    'success': function (responseHTML, textStatus, xhr) {
                        sm4.activity.notificationCountUpdate($.mobile.activePage);
                        $(document).data('loaded', true);
                        $.mobile.activePage.find('#notification_loading').css('display', 'none');
                        $.mobile.activePage.find('#notifications_menu').html(responseHTML).listview().listview('refresh');
                        sm4.core.runonce.trigger();
                        $.mobile.activePage.find('#recent_activity').trigger("create");
                        //sm4.core.refreshPage();
                        $.mobile.activePage.find('#notifications_menu').bind('vclick', function (event) {
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
                                    success: function (response) {
                                        notification_li.removeClass('sm-ui-lists-highlighted');
                                        if (sm4.core.isApp())
                                            $.mobile.changePage(sm4.core.baseUrl + forward_link);
                                        else
                                            $.mobile.changePage(forward_link);
                                    }});
                            }
                        });

                    }
                })
            }

            function showRecentRequestContent() {
                $.ajax({
                    type: "GET",
                    'url': sm4.core.baseUrl + 'activity/notifications/pulldown-request',
                    dataType: "html",
                    'data': {
                        'format': 'html',
                        'page': 1,
                        'isajax': 1
                    },
                    'success': function (responseHTML, textStatus, xhr) {
                        sm4.activity.requestCountUpdate($.mobile.activePage);
                        $(document).data('loaded', true);
                        $.mobile.activePage.find('#recent_request_loading').css('display', 'none');
                        $.mobile.activePage.find('#recent_request_menu').html(responseHTML);
                        if ($.mobile.activePage.find('#recent_request_menu').find('script').length > 1)
                            $.mobile.activePage.find('#recent_request_menu').find('script').remove()
                        $.mobile.activePage.find('#recent_request_menu').listview().listview('refresh');
                        sm4.core.runonce.trigger();
                        $.mobile.activePage.find('#recent_requests').trigger("create");
                        //sm4.core.refreshPage();
                    }});
            }

            function showMessagesContent() {
                $.ajax({
                    type: "GET",
                    'url': sm4.core.baseUrl + 'messages/inbox',
                    dataType: "html",
                    'data': {
                        'format': 'html',
                        'page': 1,
                        'isajax': 1
                    },
                    'success': function (responseHTML, textStatus, xhr) {
                        $(document).data('loaded', true);
                        $.mobile.activePage.find('#messages_popup_loading').css('display', 'none');
                        $.mobile.activePage.find('#messages_popup_menu').html(responseHTML).listview().listview('refresh');
                        sm4.core.runonce.trigger();
                        $.mobile.activePage.find('#messages_popup').trigger("create");
                        //  sm4.core.refreshPage();
                    }});
            }
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php if ($this->locationSpecific): ?>
    <div data-role="popup" id="locationPopup_<?php echo $filter ?>" data-position-to="window" data-overlay-theme="a"  data-theme="c" class="ui-corner-all ui-overlay-shadow" style="min-width: 200px;">

        <div class="ui-corner-top" data-theme="a" data-role="header" role="banner"> 
            <h1 class="ui-title" role="heading"><?php echo $this->translate("Choose Location"); ?></h1>
        </div>
        <div class="ui-corner-bottom ui-content ui-body-d" data-theme="d">
            <form>
                <select name="location_sitemobile" onchange="changeSpecificMobileLocation(this.value)">
                    <?php foreach ($this->locationsArray as $key => $locationElement): ?>
                        <option  value="<?php echo $key ?>"><?php echo $locationElement; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
<?php endif; ?>

<div data-role="popup" id="popupBasic_<?php echo $filter ?>" data-position-to="window" class="ui-content" data-overlay-theme="a" class="ui-corner-all">
    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>

    <div class="change_location_form" id="dialog-form">
        <form method="post" action="" class="global_form" enctype="application/x-www-form-urlencoded" id="seaocore_change_my_location">
            <div>
                <div>
                    <h3><?php echo $this->translate("Change My Location"); ?></h3>
                    <p class="form-description"><?php echo $this->translate("Enter your location in the auto-suggest box. (e.g., CA or 94131, San Francisco)"); ?></p>
                    <div class="form-elements">
                        <div class="form-wrapper" id="changeMyLocationValue-wrapper">
                            <div class="form-label" id="changeMyLocationValue-label"><label class="required" for="changeMyLocationValue"><?php echo $this->translate("Location: "); ?></label>

                            </div>
                            <div class="form-element" id="changeMyLocationValue-element">
                                <input type="text" id="changeMyLocationValue" name="changeMyLocationValue" autocomplete="off" onkeypress="unsetLatLng();" value="<?php
if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) {
    echo $this->getMyLocationDetailsCookie['location'];
}
?>">
                                <p id="changeMyLocationValueError" style="display:none; color:red;"><?php echo $this->translate("Please enter the valid location!"); ?></p> 
                                <p id="changeMyLocationValueErrorGeo" style="display:none; color:red;"><?php echo $this->translate("Oops! Something went wrong. Please try again later."); ?></p> 
                            </div>
                        </div>

                        <div class="form-wrapper" id="removeLocation-wrapper">
                            <div id="removeLocation-label" class="form-label">&nbsp;</div>
                            <div class="form-element" id="removeLocation-element">
                                <input type="hidden" value="" name="removeLocation" >
                                <input type="checkbox" id="removeLocation" name="removeLocation" data-role="none">
<?php echo $this->translate("Remove my location."); ?>
                            </div>
                        </div>

                        <input type="hidden" name="latitude" value="" id="latitude" />

                        <input type="hidden" name="longitude" value="" id="longitude" />

                        <div class="form-wrapper" id="buttons-wrapper" style="display: block;margin-bottom: 0;">
                            <div class="form-label" id="buttons-label">&nbsp;</div>
                            <div class="form-element" id="buttons-element">
                                <button type="submit" id="execute" name="execute" data-theme="b" onclick="changeLocationSubmitForm();
                        return false;"><?php echo $this->translate("Change Location"); ?></button>
                                or <a href="#" data-rel="back" data-role="button">
<?php echo $this->translate('Cancel') ?>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if ($this->locationSpecific): ?>

    <form id='specific_location_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'location', 'action' => 'set-specific-location'), "default"); ?>' style='display: none;'>
        <input type="hidden" id="current_url" name="current_url"  value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
        <input type="hidden" id="specificLocation" name="specificLocation"  value=""/>
    </form>

    <script type="text/javascript">
        $('select[name=location_sitemobile]').val('<?php echo $this->locationValue ?>');
        function changeSpecificMobileLocation(val) {
            var specificLocation = val;
            $('#current_url').val('<?php echo $_SERVER['REQUEST_URI']; ?>');
            $('#specificLocation').val(specificLocation);
            $('#specific_location_form').submit();
            $.mobile.navigate.history.getActive().url = $.mobile.navigate.history.getActive().url.split('#')[0];
            $.mobile.changePage($.mobile.navigate.history.getActive().url, {
                reloadPage: true,
                showLoadMsg: true
            });
        }
    </script>   
<?php endif; ?>
<script type="text/javascript">
    function unsetLatLng() {
        $('#latitude').val('0');
        $('#longitude').val('0');
    }

    sm4.core.runonce.add(function () {
        if ($.mobile.activePage.find('#popupBasic_<?php echo $filter ?>').find('#changeMyLocationValue').length > 0) {
            Autocomplete($.mobile.activePage.find('#popupBasic_<?php echo $filter ?>').find('#changeMyLocationValue').get(0));
        }
    });

//AUTOCOMPLETE ON LOCATION TEXT FIELDS
    function Autocomplete(autocompleteField) {
        var autocomplete = new google.maps.places.Autocomplete(autocompleteField);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
            $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());
        });
    }

    function changeLocationSubmitForm() {

        var removeLocationValue = $.mobile.activePage.find('#removeLocation').is(':checked');
        if (removeLocationValue) {
            $.mobile.activePage.find("div.seaocore_change_location").find("#location_span").html("World");
            $.cookie('seaocore_myLocationDetails', '', {expire: -1, path: sm4.core.baseUrl});

            $.mobile.activePage.find("#popupBasic_<?php echo $filter ?>").popup("close");
            return false;
        }

        var previousLocationValue = '<?php
if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) {
    echo $this->getMyLocationDetailsCookie['location'];
}
?>'
        var newLocationValue = $.mobile.activePage.find('#popupBasic_<?php echo $filter ?>').find('#changeMyLocationValue').val();

        if (previousLocationValue == newLocationValue && (newLocationValue != '' && newLocationValue != null)) {
            $.mobile.activePage.find("#popupBasic_<?php echo $filter ?>").popup("close");
            return false;
        }
        $.ajax({
            url: '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'location', 'action' => 'change-my-location'), "default"); ?>',
            method: 'post',
            data: {
                format: 'json',
                changeMyLocationValue: newLocationValue,
                latitude: $('#latitude').val(),
                longitude: $('#longitude').val(),
            },
            success: function (responseHTML) {
                if (responseHTML.error == 2) {
                    $.mobile.activePage.find('#changeMyLocationValueErrorGeo').css('display', 'block');
                }
                else if (responseHTML.error == 1) {
                    $.mobile.activePage.find('#changeMyLocationValueError').css('display', 'block');
                }
                else {
                    if (typeof ($.cookie('seaocore_myLocationDetails')) != 'undefined' && $.cookie('seaocore_myLocationDetails') != '')
                        var myLocationDetails = jQuery.parseJSON($.cookie("seaocore_myLocationDetails"));

                    if (myLocationDetails == '') {
                        myLocationDetails = {};
                    }

                    myLocationDetails = $.extend(myLocationDetails, {
                        latitude: responseHTML.latitude,
                        longitude: responseHTML.longitude,
                        location: responseHTML.location
                    });

                    if (typeof (myLocationDetails.locationmiles) == 'undefined' || myLocationDetails.locationmiles == null) {
                        myLocationDetails = $.extend(myLocationDetails, {
                            locationmiles: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>
                        });
                    }
                    $.mobile.activePage.find("div.seaocore_change_location").find("#location_span").html(responseHTML.location);
                    sm4.core.locationBased.setLocationCookies(myLocationDetails);

                    $.mobile.activePage.find("#popupBasic_<?php echo $filter ?>").popup("close");
                }
            }
        });
    }

//SEND REQUEST FOR - AUTO DETECT LOCATION
    sm4.core.runonce.add(function () {
<?php if ($this->detactLocation): ?>
            var params = {
                'detactLocation': <?php echo $this->detactLocation; ?>
            };
            sm4.core.locationBased.startReq(params);
<?php endif; ?>
    });
</script> 
<style type="text/css">
    .pac-container{
        z-index:100000;
    }
</style>
/* $Id: hideTabs.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

var hideWidgetsForModule = function(widgetname) {
	
	if(widgetname == 'sitestoreactivityfeed') {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}
	}
	if(widgetname == 'sitestoreseaocoreactivityfeed') {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
		}
	} else {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
		}
	}
	if(widgetname == 'sitestoreadvancedactivityactivityfeed') {
		if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
			$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
		}
	} else {
		if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
			$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
		}
	}
	if(widgetname == 'sitestoreinfo') {
		if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
			$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
			$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
		}
	}
	if(widgetname == 'sitestoreoverview') {
		if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
			$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
			$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
		}
	}
	if(widgetname == 'sitestorelocation') {
		if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
			$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
			$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
		}
	}
	if(widgetname == 'sitestorelink') {
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
	}
	
}
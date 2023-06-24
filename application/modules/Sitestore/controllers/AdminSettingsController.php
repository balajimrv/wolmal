<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminSettingsController extends Core_Controller_Action_Admin {

    //ACTION FOR GLOBAL SETTINGS
    public function indexAction() {
        //GET NAVIGATION
        $this->view->navigationStoreGlobal = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main_settings', array(), 'sitestore_admin_main_global_store');

        $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestore')->hasDirectoryPermissions();

        $sitestore_global_form_content = array('sitestore_location', 'sitestore_report', 'sitestore_share', 'sitestore_socialshare', 'sitestore_printer', 'sitestore_tellafriend', 'sitestore_captcha_post', 'sitestore_proximitysearch', 'sitestore_checkcomment_widgets', 'sitestore_sponsored_image', 'sitestore_sponsored_color', 'sitestore_feature_image', 'sitestore_featured_color', 'sitestore_store', 'sitestore_proximity_search_kilometer', 'sitestore_addfavourite_show', 'sitestore_title_truncation', 'sitestore_claimlink', 'sitestore_claim_show_menu', 'sitestore_contact', 'sitestore_requried_description', 'sitestore_status_show', 'sitestore_manageadmin', 'sitestore_layoutcreate', 'sitestore_communityads', 'sitestore_profile_fields', 'sitestore_locationfield', 'sitestore_price_field', 'sitestore_category_edit', 'sitestore_package_enable', 'submit', "sitestore_payment_benefit", 'sitestore_network', "sitestore_networks_type", "sitestore_browseorder", 'sitestore_requried_photo', 'sitestore_showmore', 'sitestore_show_menu', "sitestore_default_show", 'sitestore_map_sponsored', "sitestore_photolightbox_show", 'sitestore_photolightbox_bgcolor', 'sitestore_photolightbox_fontcolor', "sitestore_map_city", "sitestore_addfavourite_show", "sitestore_map_zoom", "sitestore_feed_type", "sitestore_manifestUrlP", "sitestore_manifestUrlS", "sitestore_mylike_show", "sitestore_categorywithslug", "sitestoreshow_navigation_tabs", "sitestore_postedby", "sitestore_fs_markers", "sitestore_claim_email", "sitestore_css", "sitestore_code_share", "sitestore_postfbstore", "translation_file", "sitestore_description_allow", "sitestore_multiple_location", "sitestore_automatically_like","sitestore_allowed_payment_escrow_gateway","sitestore_allowed_payment_split_gateway", "language_phrases_stores", "language_phrases_store", "sitestore_tinymceditor", "sitestore_default_show", "seaocore_common_css", "sitestore_network", "send_cheque_to", "sitestoreproduct_weight_unit", "sitestoreproduct_navigationtabs", "is_sitestore_admin_driven", "sitestore_hide_left_container", "sitestore_show_tabs_without_content", "sitestore_payment_for_orders", "sitestore_allowed_payment_gateway", "sitestore_admin_gateway", "is_section_allowed","sitestore_payment_method", "sitestore_shipping_extra_content", "sitestore_virtual_product_shipping", "sitestore_publish_facebook", "sitestore_allow_printingtag", "sitestore_fixed_text", "sitestore_checkout_fixed_text_value", "sitestore_terms_conditions", "sitestore_slding_effect", "sitestore_minimum_shipping_cost", "sitestore_defaultpagecreate_email", "sitestore_package_information", "sitestore_package_view", "sitestoreproduct_paymentmethod");

        $pluginName = 'sitestore';
        if (!empty($_POST[$pluginName . '_lsettings']))
            $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
        if ($this->getRequest()->isPost()) {
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitestore_allowed_payment_escrow_gateway')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitestore_allowed_payment_escrow_gateway');
            }
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitestore_allowed_payment_split_gateway')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitestore_allowed_payment_split_gateway');
            }
        }
        include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license1.php';

        if ($this->getRequest()->isPost()) {
            if (!empty($_POST['sitestore_package_information'])) {
                if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitestore_package_information')) {
                    Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitestore_package_information');
                }
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.package.information', $_POST['sitestore_package_information']);
            }

             $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');

             if($paymentMethod != 'normal')
             {
               Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.payment.for.orders',0); 
             }

            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.city', "World");
        if (!empty($oldLocation) && !empty($newLocation))
            $this->setDefaultMapCenterPoint($oldLocation, $newLocation);
    }

    // STORE FAQ'S
    public function faqAction() {

        //TABS CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_faq');

        $this->view->faq = 1;
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR GETTING THE CATGEORIES AND SUBCATEGORIES
    public function sitestorecategoriesAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorecategories');

        //GET TASK
        if (isset($_POST['task'])) {
            $task = $_POST['task'];
        } elseif (isset($_GET['task'])) {
            $task = $_GET['task'];
        } else {
            $task = "main";
        }

        //GET CATEGORIES TABLE
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestore');

        //GET CATEGORIES TABLE NAME
        $tableCategoriesName = $tableCategories->info('name');

        //GET STORE TABLE
        $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');

        if ($task == "savecat") {
            //GET CATEGORY ID
            $cat_id = $_GET['cat_id'];

            $cat_title_withoutparse = $_GET['cat_title'];

            //GET CATEGORY TITLE
            $cat_title = Engine_Api::_()->sitestore()->parseString($_GET['cat_title']);

            //GET CATEGORY DEPENDANCY
            $cat_dependency = $_GET['cat_dependency'];
            $subcat_dependency = $_GET['subcat_dependency'];
            if ($cat_title == "") {
                if ($cat_id != "new") {
                    if ($cat_dependency == 0) {
                        $row_ids = Engine_Api::_()->getDbtable('categories', 'sitestore')->getSubCategories($cat_id);
                        foreach ($row_ids as $values) {
                            $tableCategories->delete(array('subcat_dependency = ?' => $values->category_id, 'cat_dependency = ?' => $values->category_id));
                            $tableCategories->delete(array('category_id = ?' => $values->category_id));
                        }

                        $tableStore->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
                        $tableCategories->delete(array('category_id = ?' => $cat_id));

                        //START SITESTOREREVIEW CODE
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
                            Engine_Api::_()->sitestorereview()->deleteCategory($cat_id);
                        }
                        //END SITESTOREREVIEW CODE
                    } else {
                        $tableCategories->update(array('category_name' => $cat_title), array('category_id = ?' => $cat_id, 'cat_dependency = ?' => $cat_dependency));
                        $tableStore->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
                        $tableCategories->delete(array('cat_dependency = ?' => $cat_id, 'subcat_dependency = ?' => $cat_id));
                        $tableCategories->delete(array('category_id = ?' => $cat_id));
                    }
                }
                //SEND AJAX CONFIRMATION
                echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
                echo "window.parent.removecat('$cat_id');";
                echo "</script></head><body></body></html>";
                exit();
            } else {
                if ($cat_id == 'new') {
                    $row_info = $tableCategories->fetchRow($tableCategories->select()->from($tableCategoriesName, 'max(cat_order) AS cat_order'));
                    $cat_order = $row_info['cat_order'] + 1;
                    $row = $tableCategories->createRow();
                    $row->category_name = $cat_title_withoutparse;
                    $row->cat_order = $cat_order;
                    $row->cat_dependency = $cat_dependency;
                    $row->subcat_dependency = $subcat_dependency;
                    $newcat_id = $row->save();
                } else {
                    $tableCategories->update(array('category_name' => $cat_title_withoutparse), array('category_id = ?' => $cat_id));
                    $newcat_id = $cat_id;
                }

                //SEND AJAX CONFIRMATION
                echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
                echo "window.parent.savecat_result('$cat_id', '$newcat_id', '$cat_title', '$cat_dependency', '$subcat_dependency');";
                echo "</script></head><body></body></html>";
                exit();
            }
        } elseif ($task == "changeorder") {
            $divId = $_GET['divId'];
            $sitestoreOrder = explode(",", $_GET['sitestoreorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($sitestoreOrder); $i++) {
                    $cat_id = substr($sitestoreOrder[$i], 4);
                    $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($sitestoreOrder); $i++) {
                    $cat_id = substr($sitestoreOrder[$i], 4);
                    $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
                }
            } elseif (substr($divId, 0, 11) == "treesubcats") {
                for ($i = 0; $i < count($sitestoreOrder); $i++) {
                    $cat_id = substr($sitestoreOrder[$i], 4);
                    $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
                }
            }
        }

        $categories = array();
        $category_info = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories(1);
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories($value->category_id, 'subcategory_id', 0, 'subcategory_id', null, 0, 0);
            foreach ($subcategories as $subresults) {
                $subsubcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories($subresults->category_id, 'subsubcategory_id', 0, 'subsubcategory_id', null, 0, 0);
                $treesubarrays[$subresults->category_id] = array();
                foreach ($subsubcategories as $subsubcategoriesvalues) {
                    $treesubarray = array('tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'order' => $subsubcategoriesvalues->cat_order,
                        'count' => $subsubcategoriesvalues->count,);
                    $treesubarrays[$subresults->category_id][] = $treesubarray;
                }

                $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subresults->count,
                    'order' => $subresults->cat_order);
                $sub_cat_array[] = $tmp_array;
            }

            $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $value->count,
                'sub_categories' => $sub_cat_array);
            $categories[] = $category_array;
        }

        include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    }

    public function readmeAction() {

        $this->view->faq = 0;
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR SHOWING THE STORE STATISTICS
    public function statisticAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_statistic');

        //GET STORE TABLE
        $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');

        //GET TOTAL STORES    
        $this->view->totalSitestore = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalstore'))->totalstore;

        //GET PUBLISH STORES
        $this->view->totalPublish = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalpublish')->where('draft =?', 1))->totalpublish;

        //GET DRAFTED STORES
        $this->view->totalDrafted = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totaldrafted')->where('draft =?', 0))->totaldrafted;

        //Get CLOSED STORES
        $this->view->totalClosed = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalclosed')->where('closed =?', 1))->totalclosed;

        //Get OPEN STORES
        $this->view->totalopen = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalopen')->where('closed =?', 0))->totalopen;

        //GET APPROVED STORES
        $this->view->totalapproved = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalapproved')->where('approved =?', 1))->totalapproved;

        //GET DISAPPROVED STORES
        $this->view->totaldisapproved = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totaldisapproved')->where('approved =?', 0))->totaldisapproved;

        //GET FEATURED STORES
        $this->view->totalfeatured = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalfeatured')->where('featured =?', 1))->totalfeatured;

        //GET SPONSORED STORES	
        $this->view->totalsponsored = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'count(*) AS totalsponsored')->where('sponsored =?', 1))->totalsponsored;

        //GET TOTAL COMMENTS IN STORES	
        $this->view->totalcommentpost = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'sum(comment_count) AS totalcomments'))->totalcomments;

        //GET TOTAL LIKES IN STORES	
        $this->view->totallikepost = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'sum(like_count) AS totallikes'))->totallikes;

        //GET TOTAL VIEWS IN STORES	
        $this->view->totalviewpost = $tableStore->fetchRow($tableStore->select()->from($tableStore->info('name'), 'sum(view_count) AS totalviews'))->totalviews;

        //CHECK THAT SITESTORE REVIEW IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
            //GET REVIEW TABLE TABLE
            $tableReview = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');

            //GET TOTAL REVIEWS IN STORES	
            $this->view->totalreview = $tableReview->fetchRow(Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->select()->from($tableReview->info('name'), 'count(*) AS totalreview'))->totalreview;
        }

        //CHECK THAT SITESTORE DISCUSSION IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
            //GET DISCUSSION TABLE
            $tableDiscussion = Engine_Api::_()->getDbtable('topics', 'sitestore');

            //GET TOTAL DISCUSSION IN STORES
            $this->view->totaldiscussion = $tableDiscussion->fetchRow($tableDiscussion->select()->from($tableDiscussion->info('name'), 'count(*) AS totaldiscussion'))->totaldiscussion;

            //GET DISCUSSION POST TABLE
            $tableDiscussionPost = Engine_Api::_()->getDbtable('posts', 'sitestore');

            //GET TOTAL DISCUSSION POST (REPLY)IN STORES       
            $this->view->totaldiscussionpost = $tableDiscussionPost->fetchRow($tableDiscussionPost->select()->from($tableDiscussionPost->info('name'), 'count(*) AS totalpost'))->totalpost;
        }

        //GET PHOTO TABLE
        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');

        //GET THE TOTAL PHOTO IN STORES
        $this->view->totalphotopost = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'count(*) AS totalphoto')->where('collection_id <>?', 0))->totalphoto;

        //CHECK THAT SITESTORE ALBUM IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
            //GET ALBUM TABLE
            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');

            //GET THE TOTAL ALBUM IN STORES
            $this->view->totalalbumpost = $tableAlbum->fetchRow($tableAlbum->select()->from($tableAlbum->info('name'), 'count(*) AS totalalbum'))->totalalbum;
        }

        //CHECK THAT SITESTORE NOTE IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
            //GET NOTE TABLE
            $tableNote = Engine_Api::_()->getDbtable('notes', 'sitestorenote');

            //GET THE TOTAL NOTE IN STORES
            $this->view->totalnotepost = $tableNote->fetchRow($tableNote->select()->from($tableNote->info('name'), 'count(*) AS totalnotes'))->totalnotes;
        }

        //CHECK THAT SITESTORE VIDEO IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
            //GET VIDEO TABLE
            $tableVideo = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');

            //GET THE TOTAL VIDEO IN STORES
            $this->view->totalvideopost = $tableVideo->fetchRow($tableVideo->select()->from($tableVideo->info('name'), 'count(*) AS totalvideos'))->totalvideos;
        }

        //CHECK THAT SITESTORE DOCUMENT IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
            //GET DOCUMENT TABLE
            $tableDocument = Engine_Api::_()->getDbtable('documents', 'sitestoredocument');

            //GET THE TOTAL DOCUMENT IN STORES
            $this->view->totaldocumentpost = $tableDocument->fetchRow($tableDocument->select()->from($tableDocument->info('name'), 'count(*) AS totaldocuments'))->totaldocuments;
        }

        //CHECK THAT SITESTORE EVENT IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
            //GET EVENT TABLE
            $tableEvent = Engine_Api::_()->getDbtable('events', 'sitestoreevent');

            //GET THE TOTAL EVENT IN STORES
            $this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents'))->totalevents;
        }

        //CHECK THAT SITEPAGE EVENT IS ENABLED OR NOT
        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
                //GET EVENT TABLE
                $tableEvent = Engine_Api::_()->getDbtable('events', 'sitestoreevent');

                //GET THE TOTAL EVENT IN PAGES
                $this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents'))->totalevents;
            } else {
                //GET EVENT TABLE
                $tableEvent = Engine_Api::_()->getDbtable('events', 'siteevent');

                //GET THE TOTAL EVENT IN PAGES
                $this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents')->where('parent_type =?', 'sitestore_store'))->totalevents;
            }
        }
        //CHECK THAT SITESTORE VIDEO IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
            //GET PLAYLIST TABLE
            $tablePlaylist = Engine_Api::_()->getDbtable('playlists', 'sitestoremusic');

            //GET THE TOTAL PLAYLIST IN STORES
            $this->view->totalplaylists = $tablePlaylist->fetchRow($tablePlaylist->select()->from($tablePlaylist->info('name'), 'count(*) AS totalplaylists'))->totalplaylists;

            //GET PLAYLIST TABLE
            $tableSongs = Engine_Api::_()->getDbtable('playlistSongs', 'sitestoremusic');

            //GET THE TOTAL PLAYLIST IN STORES
            $this->view->totalsongs = $tableSongs->fetchRow($tableSongs->select()->from($tableSongs->info('name'), 'count(*) AS totalsongs'))->totalsongs;
        }

        //CHECK THAT SITESTORE POLL IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
            //GET POLL TABLE
            $tablePoll = Engine_Api::_()->getDbtable('polls', 'sitestorepoll');

            //GET THE TOTAL POLL IN STORES
            $this->view->totalpollpost = $tablePoll->fetchRow($tablePoll->select()->from($tablePoll->info('name'), 'count(*) AS totalpolls'))->totalpolls;
        }

        //CHECK THAT SITESTORE OFFER IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
            //GET OFFER TABLE
            $tableOffer = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');

            //GET THE TOTAL OFFER IN STORES
            $this->view->totalofferpost = $tableOffer->fetchRow($tableOffer->select()->from($tableOffer->info('name'), 'count(*) AS totaloffers'))->totaloffers;
        }
    }

    public function graphAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_graph');
        $this->view->form = $form = new Sitestore_Form_Admin_Settings_Graph();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
        }
    }

    //ACTION FOR EMAIL THE DETAIL
    public function emailAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_email');
        $this->view->form = $form = new Sitestore_Form_Admin_Settings_Email();

        //check if comments should be displayed or not
        $show_comments = Engine_Api::_()->sitestore()->displayCommentInsights();

        //check if Sitemailtemplates Plugin is enabled
        $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

        $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
        $rtasksName = $taskstable->info('name');
        $taskstable_result = $taskstable->select()
                ->from($rtasksName, array('processes', 'timeout'))
                ->where('title = ?', 'Sitestore Insight Mail')
                ->where('plugin = ?', 'Sitestore_Plugin_Task_InsightNotification')
                ->limit(1);
        $prefields = $taskstable->fetchRow($taskstable_result);

        //populate form
//     $form->populate(array(
//         'sitestore_insightemail' => $prefields->processes,
//     ));

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore_insightemail', $values['sitestore_insightemail']);

            //check if Sitemailtemplates Plugin is enabled
            $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

            if (empty($sitemailtemplates)) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore_bg_color', $values['sitestore_bg_color']);
            }
            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
            if ($values['sitestore_demo'] == 1 && $values['sitestore_insightemail'] == 1) {

                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

                //check if Sitemailtemplates Plugin is enabled
                $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
                $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

                $insights_string = '';
                $template_header = "";
                $template_footer = "";
                if (!$sitemailtemplates) {
                    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', "#ffffff");
                    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.header.color', "#79b4d4");

                    //GET SITE "Email Body Outer Background" COLOR
                    $site_bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.bg.color', "#f7f7f7");
                    $insights_string.= "<table cellpadding='2'><tr><td><table cellpadding='2'><tr><td><span style='font-size: 14px; font-weight: bold;'>" . $view->translate("Sample Store") . "</span></td></tr>";

                    $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='$site_bg_color' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
                    $template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";

                    $template_footer.= "</td></tr></table></td></tr></td></table></td></tr></table>";
                }

                if ($values['sitestore_insightmail_options'] == 1) {
                    $vals['days_string'] = $view->translate('week');
                } elseif ($values['sitestore_insightmail_options'] == 2) {
                    $vals['days_string'] = $view->translate('month');
                }
                $path = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl();
                $insight_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Visit your Insights Store') . "</a>";
                $update_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Send an update to people who like this') . "</a>";

                //check if Communityad Plugin is enabled
                $sitestorecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
                $adversion = null;
                if ($sitestorecommunityadEnabled) {
                    $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
                    $adversion = $communityadmodulemodule->version;
                    if ($adversion >= '4.1.5') {
                        $promote_Ad_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Promote with %s Ads', $site_title) . "</a>";
                    }
                }

                $insights_string.= "<table><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $vals['days_string'] . $view->translate(array('ly active user', 'ly active users', 2), 2) . "</span></td></tr><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('person likes this', 'people like this', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
                if (!empty($show_comments)) {
                    $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('comment', 'comments', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
                }
                $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '10' . "</span>\t <span style='color: rgb(85, 85, 85);'>" . $view->translate(array('visit', 'visits', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '5' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr></table><table><tr><td>" . "<ul style=' padding-left: 5px;'><li>" . $insight_link . "</li><li>" . $update_link;

                //check if Communityad Plugin is enabled
                if ($sitestorecommunityadEnabled && $adversion >= '4.1.5') {
                    $insights_string.= "</li><li>" . $promote_Ad_link;
                }
                $insights_string.= "</li></ul></td></tr></table>";
                $days_string = ucfirst($vals['days_string']);
                $owner_name = Engine_Api::_()->user()->getViewer()->getTitle();
                $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['sitestore_admin'], 'SITESTORE_INSIGHTS_EMAIL_NOTIFICATION', array(
                    'recipient_title' => $owner_name,
                    'template_header' => $template_header,
                    'message' => $insights_string,
                    'template_footer' => $template_footer,
                    'site_title' => $site_title,
                    'days' => $days_string,
                    'email' => $email,
                    'queue' => true));
            }
        }
    }

    //ACTION FOR AD SHOULD BE DISPLAY OR NOT ON STORES
    public function adsettingsAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_adsettings');

        //FORM
        $this->view->form = $form = new Sitestore_Form_Admin_Adsettings();

        //CHECK THAT COMMUNITY AD PLUGIN IS ENABLED OR NOT
        $communityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        if ($communityadEnabled) {
            $this->view->ismoduleenabled = $ismoduleenabled = 1;
        } else {
            $this->view->ismoduleenabled = $ismoduleenabled = 0;
        }

        //CHECK THAT SITESTORE DOCUMENT PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument') && $ismoduleenabled) {
            $this->view->isdocumentenabled = 1;
        } else {
            $this->view->isdocumentenabled = 0;
        }

        //CHECK THAT SITESTORE NOTE PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote') && $ismoduleenabled) {
            $this->view->isnoteenabled = 1;
        } else {
            $this->view->isnoteenabled = 0;
        }

        //CHECK THAT SITESTORE ALBUM PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum') && $ismoduleenabled) {
            $this->view->isalbumenabled = 1;
        } else {
            $this->view->isalbumenabled = 0;
        }

        //CHECK THAT SITESTORE VIDEO PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo') && $ismoduleenabled) {
            $this->view->isvideoenabled = 1;
        } else {
            $this->view->isvideoenabled = 0;
        }

        //CHECK THAT SITESTORE EVENT PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent') && $ismoduleenabled) {
            $this->view->iseventenabled = 1;
        } else {
            $this->view->iseventenabled = 0;
        }

        //CHECK THAT SITESTORE DISCUSSION PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion') && $ismoduleenabled) {
            $this->view->isdiscussionenabled = 1;
        } else {
            $this->view->isdiscussionenabled = 0;
        }

        //CHECK THAT SITESTORE POLL PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll') && $ismoduleenabled) {
            $this->view->ispollenabled = 1;
        } else {
            $this->view->ispollenabled = 0;
        }

        //CHECK THAT SITESTORE REVIEW PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && $ismoduleenabled) {
            $this->view->isreviewenabled = 1;
        } else {
            $this->view->isreviewenabled = 0;
        }

        //CHECK THAT SITESTORE OFFER PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer') && $ismoduleenabled) {
            $this->view->isofferenabled = 1;
        } else {
            $this->view->isofferenabled = 0;
        }

        //CHECK THAT SITESTORE FORM PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform') && $ismoduleenabled) {
            $this->view->isformenabled = 1;
        } else {
            $this->view->isformenabled = 0;
        }

        //CHECK THAT SITESTORE INVITE PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite') && $ismoduleenabled) {
            $this->view->isinviteenabled = 1;
        } else {
            $this->view->isinviteenabled = 0;
        }

        //CHECK THAT SITESTORE BADGE PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge') && $ismoduleenabled) {
            $this->view->isbadgeenabled = 1;
        } else {
            $this->view->isbadgeenabled = 0;
        }

        //CHECK THAT SITESTORE NOTE PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic') && $ismoduleenabled) {
            $this->view->ismusicenabled = 1;
        } else {
            $this->view->ismusicenabled = 0;
        }

        //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration') &&
                $ismoduleenabled) {
            $this->view->mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getIntegrationItems();
            $this->view->issitestoreintegrationenabled = 1;
        } else {
            $this->view->issitestoreintegrationenabled = 0;
        }
        //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
        //CHECK THAT SITESTORE TWITTER PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter') && $ismoduleenabled) {
            $this->view->istwitterenabled = 1;
        } else {
            $this->view->istwitterenabled = 0;
        }

        //CHECK THAT SITESTORE TWITTER PLUGIN IS ENABLED OR NOT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember') && $ismoduleenabled) {
            $this->view->ismemberenabled = 1;
        } else {
            $this->view->ismemberenabled = 0;
        }

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
        }
    }

    //ACTION FOR SET THE DEFAULT MAP CENTER POINT
    public function setDefaultMapCenterPoint($oldLocation, $newLocation) {

        if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Stores / Marketplace - Ecommerce'));
            if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                $latitude = $locationResults['latitude'];
                $longitude = $locationResults['longitude'];
            }

            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.map.latitude', $latitude);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.map.longitude', $longitude);
        }
    }

    //ACTINO FOR SEARCH
    public function formSearchAction() {

        // GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_form_search');

        $table = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            $row = $table->getFieldsOptions('sitestore', 'profile_type');
            $defaultAddition = 0;
            $rowCategory = $table->getFieldsOptions('sitestore', 'category_id');
            $defaultCategory = 0;
            try {
                foreach ($values['order'] as $key => $value) {
                    $table->update(array('order' => $defaultAddition + $defaultCategory + $key + 1), array('module = ?' => 'sitestore', 'searchformsetting_id =?' => (int) $value));
                    if (!empty($row) && $value == $row->searchformsetting_id)
                        $defaultAddition = 10000000;

                    if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id)
                        $defaultCategory = 1;
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        $this->view->enableBadgePlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge');
        $this->view->enableReviewPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
        $this->view->enableGeoLocationPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoregeolocation');
        $this->view->searchForm = $table->fetchAll($table->select()->where('module = ?', 'sitestore')->order('order'));
    }

    //ACTINO FOR ACTIVITY FEED
    public function activityFeedAction() {
        // GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_activity_feed');
        $aafmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
        if (!empty($aafmodule))
            $this->view->isAAFModule = true;
        //FILTER FORM
        $this->view->form = $form = new Sitestore_Form_Admin_Settings_ActivityFeed();
        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $api = Engine_Api::_()->getApi("settings", "core");
            foreach ($values as $key => $value) {
                $api->setSetting($key, $value);
            }
            $enable = $form->sitestore_feed_type->getValue();
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $activityfeed_array = array("sitestorealbum_admin_photo_new", "sitestoredocument_admin_new", "sitestoreevent_admin_new", "sitestoremusic_admin_new", "sitestorenote_admin_new", "sitestoreoffer_admin_new", "sitestorepoll_admin_new", "sitestorevideo_admin_new", "sitestore_admin_topic_create", "sitestore_admin_topic_reply");
            foreach ($activityfeed_array as $value) {
                $activit_type_sql = "UPDATE `engine4_activity_actiontypes` SET `enabled` = $enable WHERE `engine4_activity_actiontypes`.`type` = '$value' LIMIT 1";
                $db->query($activit_type_sql);
            }
        }
    }

    public function overwriteAction() {
        $type = $this->_getParam('type');
        $this->view->error = null;
        $this->view->status = false;
        if (empty($type))
            return;

        $moduleActivity = Engine_Api::_()->getDbtable('modules', 'core')->getModule('activity');
        $activityVersion = $moduleActivity->version;
        $dirName = 'activity-' . $activityVersion;
        $sourcePath = $dirName;
        $destinationPath = null;
        $api = Engine_Api::_()->getApi("settings", "core");

        $api->setSetting('sitestore_feed_type', 1);

        $destinationPath = APPLICATION_PATH
                . '/application/modules/Activity/views/scripts/_activityText.tpl';
        $sourcePath .='/views/scripts/_activityText.tpl';

        $api->setSetting('sitestorefeed_likestore_dummy', 'b');


        if (is_file($destinationPath)) {
            @chmod($destinationPath, 0777);
        } else {
            $this->view->error = 'Target File does not exist.';
        }

        if (!is_writeable($destinationPath)) {
            $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
        }

        $serverPath = 'http://www.socialengineaddons.com/SocialEngine/SocialengineModules/index.php?path=';
        $sourcePath = $serverPath . @urlencode($sourcePath);
        $ch = curl_init();
        $timeout = 0;
        @curl_setopt($ch, CURLOPT_URL, $sourcePath);
        @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        @ob_start();
        @curl_exec($ch);
        @curl_close($ch);
        if (empty($exe_status)) {
            $get_value = @ob_get_contents();
        }
        if (empty($get_value)) {
            $get_value = @file_get_contents($oposit_url);
        }
        @ob_end_clean();

        if (!empty($get_value)) {
            if (!@file_put_contents($destinationPath, $get_value)) {
                $this->view->status = false;
                $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
                return;
            }
        } else {
            $this->view->error = 'It seems that you do not have any internet connection that\'s why you are not able to overwrite this file.';
        }

        @chmod($activityTextPath_Original, 0755);
        if (empty($this->view->error))
            $this->view->status = true;
    }

    //ACTION FOR MAPPING OF LISTINGS
    Public function mappingCategoryAction() {
        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->catid = $catid = $this->_getParam('catid');

        //GET CATEGORY TITLE
        $this->view->oldcat_title = $oldcat_title = $this->_getParam('oldcat_title');

        //GET CATEGORY DEPENDANCY
        $this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

        //CREATE FORM
        $this->view->form = $form = new Sitestore_Form_Admin_Settings_Mapping();

        $this->view->close_smoothbox = 0;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if ($this->getRequest()->isPost()) {

            //GET FORM VALUES
            $values = $form->getValues();

            //GET STORES TABLE
            $tableSitestore = Engine_Api::_()->getDbtable('stores', 'sitestore');

            //GET CATEGORY TABLE
            $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestore');

            //ON CATEGORY DELETE
            $rows = $tableCategory->getSubCategories($catid);
            foreach ($rows as $row) {
                $tableCategory->delete(array('subcat_dependency = ?' => $row->category_id, 'cat_dependency = ?' => $row->category_id));
                $tableCategory->delete(array('category_id = ?' => $row->category_id));
            }

            $previous_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitestore')->getProfileType($catid);
            $new_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitestore')->getProfileType($values['new_category_id']);

            //SELECT STORES WHICH HAVE THIS CATEGORY
            if ($previous_cat_profile_type != $new_cat_profile_type) {
                $rows = $tableSitestore->getCategorySitestore($catid);
                if (!empty($rows)) {
                    foreach ($rows as $key => $store_ids) {
                        $store_id = $store_ids['store_id'];

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('sitestore_store', 'values')->delete(array('item_id = ?' => $store_id));
                        Engine_Api::_()->fields()->getTable('sitestore_store', 'search')->delete(array('item_id = ?' => $store_id));

                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED STORES
                        $tableSitestore->update(array('profile_type' => $new_cat_profile_type), array('store_id = ?' => $store_id));
                    }
                }
            }

            //STORE TABLE CATEGORY DELETE WORK
            if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                $tableSitestore->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
            } else {
                $tableSitestore->update(array('category_id' => 0), array('category_id = ?' => $catid));
            }

            $tableCategory->delete(array('category_id = ?' => $catid));
        }

        $this->view->close_smoothbox = 1;
    }

    public function documentWidgetSettingAction() {
        $isStoreActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoredocument.isFileExe', null);
        if (empty($isStoreActivate)) {
            include APPLICATION_PATH . '/application/modules/Sitestoredocument/controllers/license/widgetSettings.php';
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoredocument.isFileExe', 1);

            $redirect = $this->_getParam('redirect', false);
            if ($redirect == 'install') {
                $this->_redirect('install/manage');
            } elseif ($redirect == 'query') {
                $this->_redirect('install/manage/complete');
            }
        }
    }

    //ACTION FOR THE LANGUAGE FILE CHANGE DURING THE UPGRADE.
    public function languageAction() {

        //START LANGUAGE WORK
        Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
        //END LANGUAGE WORK
        $redirect = $this->_getParam('redirect', false);
        if ($redirect == 'install') {
            $this->_redirect('install/manage');
        } elseif ($redirect == 'query') {
            $this->_redirect('install/manage/complete');
        }
    }

}

?>

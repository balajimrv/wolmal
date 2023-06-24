<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_Siteapi_Core extends Core_Api_Abstract {

    public $_profileFieldsArray;

    public function getStorePaginator($values = array()) {
        return Engine_Api::_()->sitestore()->getSitestoresPaginator($values);
    }

    /*
     * Get browse/manage page search api
     * 
     * @return array
     */

    public function getSearchForm() {
        $searchForm = array();
        $storesearchsettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        // Set "Category" form element in the search form.
        $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
        $subcategoryArray = array();
        if (count($categories) != 0) {
            $categories_prepared[0] = '';
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $this->translate($category->category_name);
                $subcategoryArray[$category->category_id]['form'] = array(
                    'name' => 'subcategory_id',
                    'type' => 'Select',
                    'label' => $this->translate('Subcategory'),
                    'multiOptions' => array(0 => ''),
                );
            }
        }

        $subcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getCategoriesByLevel('subcategory');

        if (!empty($subcategories)) {
            foreach ($subcategories as $row => $value) {
                $subcategoryArray[$value->cat_dependency]['form']['multiOptions'][$value->category_id] = $this->translate($value->category_name);
                $subcategoryArray[$value->cat_dependency]['subsubcategories'][$value->category_id] = array(
                    'name' => 'subsubcategory_id',
                    'type' => 'Select',
                    'label' => $this->translate('3rd Level category'),
                    'multiOptions' => array(0 => ''),
                );

                $subsubcategories = Engine_Api::_()->getDbtable('categories', 'sitestore')->getCategoriesByLevel('subsubcategory');

                if (!empty($subsubcategories)) {
                    foreach ($subsubcategories as $row => $subvalue) {
                        if ($subvalue->cat_dependency == $value->category_id)
                            $subcategoryArray[$value->cat_dependency]['subsubcategories'][$subvalue->cat_dependency]['multiOptions'][$subvalue->category_id] = $this->translate($subvalue->category_name);
                    }

                    foreach ($subcategoryArray[$value->cat_dependency]['subsubcategories'] as $catkey => $catvalue) {
                        if (count($catvalue['multiOptions']) == 1)
                            unset($subcategoryArray[$value->cat_dependency]['subsubcategories']);
                    }
                }
            }
            foreach ($subcategoryArray as $key => $value) {
                if (count($value['form']['multiOptions']) == 1)
                    unset($subcategoryArray[$key]);
            }
        }

        $searchForm[] = array(
            'label' => $this->translate('Category'),
            'type' => 'Select',
            'name' => 'category_id',
            'multiOptions' => $categories_prepared,
        );

        // Set "Show" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'show');
        if (!empty($row) && !empty($row->display)) {
            if (isset($multiOptionsArray))
                unset($multiOptionsArray);

            $multiOptionsArray = array();
            $multiOptionsArray['1'] = $this->translate('Everyone\'s Stores');
            $multiOptionsArray['2'] = $this->translate('Only My Friends\' Stores');
            $multiOptionsArray['4'] = $this->translate('Stores I Like');
            $multiOptionsArray['5'] = $this->translate('Featured Stores');

            $value_deault = 1;
            $enableNetwork = $coreSettings->getSetting('sitestore.network', 0);
            if (empty($enableNetwork)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

                if (!empty($viewerNetwork) || Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
                    $show_multiOptions["3"] = $this->translate('Only My Networks');
                    $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.default.show', 0);

                    if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
                        $value_deault = 3;
                    } elseif (isset($_REQUEST['show'])) {
                        $value_deault = $_REQUEST['show'];
                    }
                }
            }

            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => $this->translate('Show'),
                'multiOptions' => $multiOptionsArray,
                'value' => $value_deault
            );
        }


        // Set "Browse By" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'orderby');
        $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
        $multiOptionsArray = array();
        $multiOptionsArray[''] = '';
        $multiOptionsArray['creation_date'] = $this->translate('Most Recent');
        $multiOptionsArray['view_count'] = $this->translate('Most Viewed');
        $multiOptionsArray['like_count'] = $this->translate('Most Liked');
        $multiOptionsArray['title'] = $this->translate('Alphabetical');
        if (!empty($row) && !empty($row->display) && !empty($sitestorereviewEnabled)) {
            if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store")) {
                $multiOptionsArray['review_count'] = $this->translate('Most Reviewed');
                $multiOptionsArray['rating'] = $this->translate('Highest Rated');
            }
        }
        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => $this->translate('Browse By'),
            'multiOptions' => $multiOptionsArray
        );


        // Set "Search" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'search');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                'label' => $this->translate('Search Stores')
            );
        }


        // Set "Price" form element in the search form.
        $pricefieldoptions = $storesearchsettings->getFieldsOptions('sitestore', 'price');
        if (!empty($pricefieldoptions)) {
            $enablePrice = $coreSettings->getSetting('sitestore.price.field', 0);
            if (!empty($enablePrice)) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'price',
                    'label' => $this->translate('Price')
                );
            }
        }


        // Set "Within Kilometers / Within Miles", "Street", "City", "State" and "Country" form element in the search form.
        $rowLocation = $storesearchsettings->getFieldsOptions('sitestore', 'location');
        if (!empty($rowLocation) && !empty($rowLocation->display)) {
            $enableLocation = $coreSettings->getSetting('sitestore.locationfield', 1);
            if (!empty($enableLocation)) {
                $row = $storesearchsettings->getFieldsOptions('sitestore', 'locationmiles');
                if (!empty($row) && !empty($row->display)) {
                    $enableProximitysearch = $coreSettings->getSetting('sitestore.proximitysearch', 1);
                    if (!empty($enableProximitysearch)) {
                        $flage = $coreSettings->getSetting('sitestore.proximity.search.kilometer', 0);
                        if ($flage) {
                            $locationLable = $this->translate("Within Kilometers");
                            $locationOption = array(
                                '0' => '',
                                '1' => $this->translate('1 Kilometer'),
                                '2' => $this->translate('2 Kilometers'),
                                '5' => $this->translate('5 Kilometers'),
                                '10' => $this->translate('10 Kilometers'),
                                '20' => $this->translate('20 Kilometers'),
                                '50' => $this->translate('50 Kilometers'),
                                '100' => $this->translate('100 Kilometers'),
                                '250' => $this->translate('250 Kilometers'),
                                '500' => $this->translate('500 Kilometers'),
                                '750' => $this->translate('750 Kilometers'),
                                '1000' => $this->translate('1000 Kilometers'),
                            );
                        } else {
                            $locationLable = $this->translate("Within Miles");
                            $locationOption = array(
                                '0' => '',
                                '1' => $this->translate('1 Mile'),
                                '2' => $this->translate('2 Miles'),
                                '5' => $this->translate('5 Miles'),
                                '10' => $this->translate('10 Miles'),
                                '20' => $this->translate('20 Miles'),
                                '50' => $this->translate('50 Miles'),
                                '100' => $this->translate('100 Miles'),
                                '250' => $this->translate('250 Miles'),
                                '500' => $this->translate('500 Miles'),
                                '750' => $this->translate('750 Miles'),
                                '1000' => $this->translate('1000 Miles'),
                            );
                        }

                        // Set "Within Kilometers / Within Miles" form element in the search form.
                        $searchForm[] = array(
                            'type' => 'Select',
                            'name' => 'locationmiles',
                            'label' => $locationLable,
                            'multiOptions' => $locationOption
                        );
                    }


                    $searchForm[] = array(
                            'type' => 'Text',
                            'name' => 'sitestore_location',
                            'label' => $this->translate("Location"),
                        );
                }


                


                // Set "Street" form element in the search form.
                $row = $storesearchsettings->getFieldsOptions('sitestore', 'street');
                if (!empty($row) && !empty($row->display)) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitestore_street',
                        'label' => $this->translate('Street')
                    );
                }


                // Set "City" form element in the search form.
                $row = $storesearchsettings->getFieldsOptions('sitestore', 'city');
                if (!empty($row) && !empty($row->display)) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitestore_city',
                        'label' => $this->translate('City')
                    );
                }


                // Set "State" form element in the search form.
                $row = $storesearchsettings->getFieldsOptions('sitestore', 'state');
                if (!empty($row) && !empty($row->display)) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitestore_state',
                        'label' => $this->translate('State')
                    );
                }


                // Set "Country" form element in the search form.
                $row = $storesearchsettings->getFieldsOptions('sitestore', 'country');
                if (!empty($row) && !empty($row->display)) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitestore_country',
                        'label' => $this->translate('Country'),
                    );
                }
            }
        }


        // Set "Badge" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'badge_id');
        if (!empty($row) && !empty($row->display)) {
            if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorebadge.seaching.bybadge', 1)) {
                $badgeData = Engine_Api::_()->getDbTable('badges', 'sitestorebadge')->getBadgesData(array('search_code' => 1));
                if (!empty($badgeData)) {
                    $badgeData = $badgeData->toArray();
                    $badgeCount = Count($badgeData);
                    if (!empty($badgeCount)) {
                        $badge_options = array();
                        $badge_options[0] = '';
                        foreach ($badgeData as $key => $name) {
                            $badge_options[$name['badge_id']] = $name['title'];
                        }

                        $searchForm[] = array(
                            'type' => 'Select',
                            'name' => 'badge_id',
                            'label' => $this->translate('Badge'),
                            'multiOptions' => $badge_options,
                        );
                    }
                }
            }
        }


        // Set "Store Profile Type" form element in the search form.
        $searchFormSettings = $storesearchsettings->getFieldsOptions('sitestore', 'profile_type');
        if (!empty($searchFormSettings)) {
            $multiOptions = array();
            $multiOptions[''] = '';
            $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias("sitestore_store", 'profile_type');
            if (count($profileTypeFields) == 1) {
                $profileTypeField = $profileTypeFields['profile_type'];
                $options = $profileTypeField->getOptions();
                if (!empty($options) && COUNT($options) >= 2) {
                    foreach ($options as $option)
                        $multiOptions[$option->option_id] = $option->label;

                    asort($multiOptions);
                    $searchForm[] = array(
                        'type' => 'Select',
                        'name' => 'profile_type',
                        'label' => $this->translate('Store Profile Type'),
                        'multiOptions' => $multiOptions,
                    );
                }
            }
        }


        // Set "Stores With Offers" form element in the search form.
        $sitestoreofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'offer_type');
        if (!empty($row) && !empty($row->display) && !empty($sitestoreofferEnabled)) {
            $multiOptionsArray = array();
            $multiOptionsArray[''] = '';
            $multiOptionsArray['all'] = $this->translate('All Offers');
            $multiOptionsArray['hot'] = $this->translate('Hot Offers');
            $multiOptionsArray['featured'] = $this->translate('Featured Offers');

            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'offer_type',
                'label' => $this->translate('Stores With Offers'),
                'multiOptions' => $multiOptionsArray,
            );
        }


        // Set "Status" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'closed');
        if (!empty($row) && !empty($row->display)) {
            $enableStatus = $coreSettings->getSetting('sitestore.status.show', 0);
            if ($enableStatus) {
                $multiOptionsArray = array();
                $multiOptionsArray[''] = $this->translate('All Stores');
                $multiOptionsArray['0'] = $this->translate('Only Open Stores');
                $multiOptionsArray['1'] = $this->translate('Only Closed Stores');

                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'closed',
                    'label' => $this->translate('Status'),
                    'multiOptions' => $multiOptionsArray,
                );
            }
        }


        // Set "Only Stores With Reviews" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'has_review');
        if (!empty($row) && !empty($row->display) && (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_review',
                'label' => $this->translate('Only Stores With Reviews')
            );
        }


        // Set "Only Stores With Photos" form element in the search form.
        $row = $storesearchsettings->getFieldsOptions('sitestore', 'has_photo');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_photo',
                'label' => $this->translate('Only Stores With Photos')
            );
        }

        // Set "Search" form button in the search form.
        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'done',
            'label' => $this->translate('Search')
        );

        $response['form'] = $searchForm;
        $response['categoriesForm'] = $subcategoryArray;

        return $response;
    }

    /*
    * Browse Order Search Form
    */
    public function getManageSearchForm()
    {
        $form = array();

        $form[] = array(
                'name' => 'search',
                'label' => $this->translate("Search"),
                'required' => true,
                'value' => 1,
            );

        $form[] = array(
                'name' => 'order_id',
                'type' => 'Text',
                'label' => $this->translate("Order Id (#)")
            );

        $form[] = array(
                'name' => 'creation_date',
                'type' => 'Text',
                'label' => $this->translate("Order Date : ex (2000-12-25)"),
            );

        $form[] = array(
                'name' => 'billing_name',
                'type' => 'Text',
                'label' => $this->translate("Billing Name"),
            );

        $form[] = array(
                'name' => 'shipping_name',
                'type' => 'Text',
                'label' => $this->translate("Shipping Name"),
            );

        $form[] = array(
                'name' => 'order_min_amount',
                'type' => 'Text',
                'label' => $this->translate("Order Total Min."),
            );

        $form[] = array(
                'name' => 'order_max_amount',
                'type' => 'Text',
                'label' => $this->translate("Order Total Max."),
            );

        $form[] = array(
                'name' => 'delivery_time',
                'type' => 'Text',
                'label' => $this->translate("Delivery Time (In Days)"),
            );
        $form[] = array(
                'name' => 'order_status',
                'type' => 'Select',
                'label' => $this->translate("Order Status"),
                'multiOptions' => array(
                        '0' => '',
                        '1' => $this->translate("Approval Pending"),
                        '2' => $this->translate("Payment Pending"),
                        '3' => $this->translate("Processing"),
                        '4' => $this->translate("On Hold"),
                        '5' => $this->translate("Fraud"),
                        '6' => $this->translate("Completed"),
                        '7' => $this->translate("Canceled"),
                    ),
            );

        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0))
        {
            $form[] = array(
                'name' => 'downpayment',
                'type' => 'Select',
                'label' => $this->translate("Downpayment"),

            );
        }

        $form[] = array(
                'name' => 'Submit',
                'label' => $this->translate("Search"),
            );

        return $form;
    }

    /**
     * Gets default profile ids
     *
     * @param sitepage object
     * return array
     */
    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }
        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    /**
     *  Gets the profile fields for the directory page based on category
     *
     *  @param array fieldsform
     *  @return array
     */
    private function _getProfileFields($fieldsForm = array()) {
        $fieldsForm = array();
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitestore_store');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            $fieldForm['heading'] = $getHeadingName;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitestore')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitestore')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$optionCategoryName][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
//                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    /**
     * Get the Profile Fields Information, which will show on product profile page.
     *
     * @param product object , setkeyasresponse boolean
     * @return array
     */
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($subject);
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);

        $fieldValues = array();
        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();
            $specificProfileFields[$getFieldId] = $_getProfileFields[$getFieldId];
            foreach ($specificProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $key => $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);
                    $heading = $value['heading'];

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            if($heading)
                                $fieldValues[$heading][$tempKey] = $tempValue;
                            else
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields($getType);

            foreach ($_getProfileFields as $value) {
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            if($heading)
                                $fieldValues[$heading][$tempKey] = $tempValue;
                            else
                                $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }


    /**
     * Gets the Profile Types of a Page Based on category
     *
     * @param array object of profilefieldmaps
     *
     * @return array
     */
    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitestore_store');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('sitestore_store');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }

        return $profileFields;
    }


    public function getInfoFields($subject) {
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        return $this->getProfileInfo($subject);
    }

    /**
     * Get information of sitepage
     *
     * @param sitepage object
     * @return array 
     */
    public function getInformation($subject) {

        $subsubcategory_name = null;
        $subcategory_name = null;
        $response = $basicinfoarray = array();
        $basicinfoarray[$this->translate('Posted')] = $this->translate(gmdate('M d, Y', strtotime($subject->creation_date)));
        $basicinfoarray[$this->translate('Last Updated')] = $this->translate(gmdate('M d, Y', strtotime($subject->modified_date)));
        $basicinfoarray[$this->translate('Views')] = $subject->view_count;
        $basicinfoarray[$this->translate('Likes')] = $subject->like_count;

        if (!empty($subject->follow_count))
            $basicinfoarray[$this->translate('Followers')] = $subject->follow_count;

        // Category 
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestore');
        if ($subject->category_id) {
            $categoriesNmae = $tableCategories->getCategory($subject->category_id);
            if (!empty($categoriesNmae->category_name)) {
                $category_name = $categoriesNmae->category_name;
            }

            if ($subject->subcategory_id) {
                $subcategory_name = $tableCategories->getCategory($subject->subcategory_id);
                if (!empty($subcategory_name->category_name)) {
                    $subcategory_name = $subcategory_name->category_name;
                }

                // Get sub-sub category
                if ($subject->subsubcategory_id) {
                    $subsubcategory_name = $tableCategories->getCategory($subject->subsubcategory_id);
                    if (!empty($subsubcategory_name->category_name)) {
                        $subsubcategory_name = $subsubcategory_name->category_name;
                    }
                }
            }
        }

        $categoryData = "";
        if ($category_name != '') {
            $categoryData = $category_name;
            if ($subcategory_name != '')
                $categoryData .= " >> " . $subcategory_name;

            if ($subsubcategory_name != '')
                $categoryData .= " >> " . $subsubcategory_name;
        }

        if (strlen($categoryData) > 1)
            $basicinfoarray[$this->translate('Category')] = $categoryData;

        $sitestoretags = $subject->tags()->getTagMaps();
        $tagsData = "";
        if (count($sitestoretags) > 0) {
            $tagcount = 0;
            foreach ($sitestoretags as $tag) {
                $tagsData .= " #" . $tag->getTag()->text;
            }
        }

        if (strlen($tagsData) > 1)
            $basicinfoarray[$this->translate('Tags')] = $tagsData;

        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
        if ($enableLocation && $subject->location)
            $basicinfoarray[$this->translate('Location')] = $subject->location;

        $basicinfoarray[$this->translate('Description')] = $subject->body;

        if (!empty($basicinfoarray))
            $response['basic_information'] = $basicinfoarray;

        // Set the "Profile Information" in the array
        try {
            $tempGetProfileInfo = $this->getInfoFields($subject);
            if (!empty($tempGetProfileInfo))
                    $response['profile_information'] = $tempGetProfileInfo;
        } catch (Exception $ex) {
            // blank exception
        }

        return $response;
    }

    /**
     * Tell a friend Form
     *
     * @return array
     */
    public function getTellAFriendForm() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        if($viewer_id)
        {
            $tell[] = array(
                'type' => 'Checkbox',
                'name' => 'send_me',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
            );
        }


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );

        $response = array();
        $response['form'] = $tell;
        return $response;
    }

    /*
    * Invite form
    */
    public function inviteForm()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $invite[] = array(
                'name' => 'emails',
                'label' => $this->translate("Emails Ids"),
                'description' => $this->translate("Enter the emails in comma separated fashion"),
            );

        $invite[] = array(
                'name' => 'message',
                'label' => $this->translate("Message"),
            );

        $invite[] = array(
                'type' => 'Submit',
                'name' => 'send',
                'label' => $this->translate('Invite'),
            );

        return $invite;

    }

    /**
     * Returns create a review form 
     *
     * @param array $widgetSettingsReviews
     * @return array
     */
    public function getReviewCreateForm($widgetSettingsReviews) {
        // Get viewer info
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $getItemPage = $widgetSettingsReviews['item'];
        $sitepagereview_proscons = $widgetSettingsReviews['settingsReview']['sitepagereview_proscons'];
        $sitepagereview_limit_proscons = $widgetSettingsReviews['settingsReview']['sitepagereview_limit_proscons'];
        $sitepagereview_recommend = $widgetSettingsReviews['settingsReview']['sitepagereview_recommend'];

        if ($sitepagereview_proscons) {
            if ($sitepagereview_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Page?"),
                    'hasValidator' => 'true'
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Page?"),
                    'hasValidator' => 'true',
                );
            }


            if ($sitepagereview_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
                    'hasValidator' => 'true',
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
                    'hasValidator' => 'true',
                );
            }
        }

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('One-line summary'),
        );

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        if ($sitepagereview_recommend) {
            $createReview[] = array(
                'type' => 'Radio',
                'name' => 'recommend',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recommended'),
                'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend this Page to a friend?")),
                'multiOptions' => array(
                    1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes'),
                    0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No')
                ),
            );
        }

        $createReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );
        return $createReview;
    }

    /*
     * Returns review update form 
     *
     * @return array
     */

    public function getReviewUpdateForm() {

        $updateReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        $updateReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add your Opinion'),
        );
        return $updateReview;
    }

    /*
     * Returns comments on review form 
     *
     * @return array
     */

    public function getcommentForm($type, $id) {
        $commentform = array();
        $commentform[] = array(
            'type' => "Text",
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment'),
        );
        return $commentform;
    }

    /*
     *   Adds photo
     *
     *
     */

    public function setPhoto($photo, $subject, $needToUplode = false, $params = array()) {
        try {

            if ($photo instanceof Zend_Form_Element_File) {
                $file = $photo->getFileName();
            } else if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else if (is_string($photo) && file_exists($photo)) {
                $file = $photo;
            } else {
                throw new Group_Model_Exception('invalid argument passed to setPhoto');
            }
        } catch (Exception $e) {
            
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        if (isset($params['album_id']) && !empty($params['album_id'])) {
            $album = Engine_Api::_()->getItem('sitepage_album', $params['album_id']);
            if (!$album->toArray())
                $album = $subject->getSingletonAlbum();
        } else
            $album = $subject->getSingletonAlbum('');
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $subject->getIdentity(),
            'album_id' => $album->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $album->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

    /**
     * Review search form
     * 
     * @return array
     */
    public function getReviewSearchForm() {

        $order = 1;
        $reviewForm = array();
        $reviewForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => $this->translate('Search'),
        );

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($viewer_id) {
            $reviewForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => $this->translate('Show'),
                'multiOptions' => array('' => $this->translate("Everyone's Reviews"),
                    'friends_reviews' => $this->translate("My Friends' Reviews"),
                    'self_reviews' => $this->translate("My Reviews"),
                    'featured' => $this->translate("Featured Reviews")),
            );
        }

        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'type',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Reviews Written By'),
            'multiOptions' => array('' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'), 'editor' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Editors'), 'user' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Users')),
        );


        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'rating_highest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Highest Rating'),
                'rating_lowest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lowest Rating'),
                'helpfull_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Helpful'),
                'replay_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Reply'),
                'view_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed')
            ),
        );
        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'rating',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Ratings'),
            'multiOptions' => array(
                '' => '',
                '5' => sprintf($this->translate('%1s Star'), 5),
                '4' => sprintf($this->translate('%1s Star'), 4),
                '3' => sprintf($this->translate('%1s Star'), 3),
                '2' => sprintf($this->translate('%1s Star'), 2),
                '1' => sprintf($this->translate('%1s Star'), 1),
            ),
        );

        $reviewForm[] = array(
            'type' => 'Checkbox',
            'name' => 'recommend',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Recommended Reviews'),
        );

//        $reviewForm[] = array(
//            'type' => 'Submit',
//            'name' => 'done',
//            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
//        );

        return $reviewForm;
    }

    /*
     *  cart product field array
     */

    public function cartProductFieldArray() {
        
    }

    /*
     * Checkout address form
     */

    public function sitestoreproduct_Form_Addresses($params) {
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $local = ($viewer_id) ? $viewer->locale : 'auto';
        $localeObject = new Zend_Locale($local);
        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $countries = Zend_Locale::getTranslationList('territory', $localeObject);
        $countryArray[''] = "Select Country";
        $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);

        foreach ($shippingCountries as $keys => $shippingCountry) {
            foreach ($countries as $keys => $country) {
                if ($shippingCountry['country'] == $keys) {
                    $localeCountryArray[$keys] = $country;
                    break;
                }
            }
        }

        asort($localeCountryArray);
        $countryArray = array_merge($countryArray, $localeCountryArray);

        $form = $billingForm = $shippingForm = $response = array();

        if(!$viewer_id)
        {
            $billingForm[] = array(
                'name' => 'email_billing',
                'label' => $this->translate('Email'),
                'type' => 'Text',
                'hasValidator' => 'true'
            );  
        }

        $billingForm[] = array(
            'name' => 'f_name_billing',
            'label' => $this->translate('First Name'),
            'type' => 'Text',
            'hasValidator' => 'true'
        );

        $billingForm[] = array(
            'name' => 'l_name_billing',
            'label' => $this->translate('Last Name'),
            'type' => 'Text',
            'hasValidator' => 'false'
        );

        $billingForm[] = array(
            'name' => 'phone_billing',
            'label' => $this->translate('Phone Number'),
            'type' => 'Text',
            'hasValidator' => 'true'
        );

        $billingForm[] = array(
            'name' => 'country_billing',
            'label' => $this->translate('Country'),
            'type' => 'Select',
            'multiOptions' => $countryArray,
            'hasValidator' => 'true'
        );

        $billingForm[] = array(
            'name' => 'state_billing',
            'label' => $this->translate('Region / State'),
            'type' => 'Select',
            'multiOptions' => array(),
            'hasValidator' => 'false'
        );

        $billingForm[] = array(
            'name' => 'city_billing',
            'type' => 'Text',
            'label' => $this->translate('City'),
            'hasValidator' => 'true'
        );

        $billingForm[] = array(
            'name' => 'locality_billing',
            'type' => 'Text',
            'label' => $this->translate('Locality'),
            'hasValidator' => 'false'
        );

        $billingForm[] = array(
            'name' => 'zip_billing',
            'type' => 'Text',
            'label' => $this->translate('Zip/Pin Code'),
            'hasValidator' => 'true'
        );

        $billingForm[] = array(
            'name' => 'address_billing',
            'type' => 'Textarea',
            'label' => $this->translate('Address'),
            'hasValidator' => 'true'
        );

        if (isset($params['showShippingAddress']) && !empty($params['showShippingAddress'])) {
            $billingForm[] = array(
                'name' => 'common',
                'type' => 'Checkbox',
                'label' => $this->translate('Same Shipping Address'),
                'value' => $_POST['common'] ? $_POST['common'] : 1 ,
            );
        }

        $billingForm[] = array(
            'name' => 'continue',
            'type' => 'Button',
            'label' => $this->translate('Continue'),
        );

        if (isset($params['showShippingAddress']) && !empty($params['showShippingAddress'])) {

            $shippingForm[] = array(
                'name' => 'f_name_shipping',
                'label' => $this->translate('First Name'),
                'type' => 'Text',
                'hasValidator' => 'true'
            );

            $shippingForm[] = array(
                'name' => 'l_name_shipping',
                'label' => $this->translate('Last Name'),
                'type' => 'Text',
                'hasValidator' => 'false'
            );

            $shippingForm[] = array(
                'name' => 'phone_shipping',
                'label' => $this->translate('Phone Number'),
                'type' => 'Text',
                'hasValidator' => 'true'
            );

            $shippingForm[] = array(
                'name' => 'country_shipping',
                'label' => $this->translate('Country'),
                'type' => 'Select',
                'multiOptions' => $countryArray,
                'hasValidator' => 'true'
            );

            $shippingForm[] = array(
                'name' => 'state_shipping',
                'label' => $this->translate('Region / State'),
                'type' => 'Select',
                'multiOptions' => array(),
                'hasValidator' => 'false'
            );

            $shippingForm[] = array(
                'name' => 'city_shipping',
                'type' => 'Text',
                'label' => $this->translate('City'),
                'hasValidator' => 'true'
            );

            $shippingForm[] = array(
                'name' => 'locality_shipping',
                'type' => 'Text',
                'label' => $this->translate('Locality'),
                'hasValidator' => 'false'
            );

            $shippingForm[] = array(
                'name' => 'zip_shipping',
                'type' => 'Text',
                'label' => $this->translate('Zip/Pin Code'),
                'hasValidator' => 'true'
            );

            $shippingForm[] = array(
                'name' => 'address_shipping',
                'type' => 'Textarea',
                'label' => $this->translate('Address'),
                'hasValidator' => 'true'
            );
            $response['shippingForm'] = $shippingForm;
        }

        $response['billingForm'] = $billingForm;

        return $response;
    }

    /**
     * Return checkout shipping methods
     *
     * @param array $info
     * @return array
     */
    public function getCheckoutShippingMethods($info = array()) {

        $index = 0;
        $shippingMethodTable = Engine_Api::_()->getDbTable('Shippingmethods', 'sitestoreproduct');
        $shippingMethodTableName = $shippingMethodTable->info('name');
        $shippingCountry = $info['shipping_country'];
        $shippingRegion = $info['shipping_region_id'];

        $shippingMethodsArray = array();
        $select = $shippingMethodTable->select()
                ->from($shippingMethodTableName)
                ->where('store_id = ? AND status = 1', $info['store_id'])
                ->where("(country LIKE 'ALL' OR (country LIKE '$shippingCountry' AND (region = 0 OR region = '$shippingRegion')))")
                ->order('creation_date ASC');

        $select = $select->query()->fetchALL();

        foreach ($select as $key => $values) {

            if ($values['dependency'] == 1) {
                if ($info['total_weight'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_weight'] <= $values['ship_end_limit'])) {
                    if ($values['ship_type'] == 0) {
                        if ($values['handling_type'] == 0) {
                            $shippingMethodsArray[$index]['name'] = $values['title'];
                            $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                            $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
                            $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                            $index++;
                        } else {
                            $shippingMethodsArray[$index]['name'] = $values['title'];
                            $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                            $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
                            $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                            $index++;
                        }
                    } else {
                        $shippingMethodsArray[$index]['name'] = $values['title'];
                        $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                        $shippingMethodsArray[$index]['charge'] = @round(ceil($info['total_weight']) * $values['handling_fee'], 2);
                        $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                        $index++;
                    }
                }
            } else {
                if ($info['total_weight'] >= $values['allow_weight_from'] && (empty($values['allow_weight_to']) || $info['total_weight'] <= $values['allow_weight_to'])) {
                    if ($values['dependency'] == 0) {
                        if ($info['total_price'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_price'] <= $values['ship_end_limit'])) {
                            if ($values['handling_type'] == 0) {
                                $shippingMethodsArray[$index]['name'] = $values['title'];
                                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                                $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
                                $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                                $index++;
                            } else {
                                $shippingMethodsArray[$index]['name'] = $values['title'];
                                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                                $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
                                $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                                $index++;
                            }
                        }
                    } else {
                        if ($info['total_quantity'] >= $values['ship_start_limit'] && (empty($values['ship_end_limit']) || $info['total_quantity'] <= $values['ship_end_limit'])) {
                            if ($values['ship_type'] == 0) {
                                $shippingMethodsArray[$index]['name'] = $values['title'];
                                $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                                $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'], 2);
                                $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                                $index++;
                            } else {
                                if ($values['handling_type'] == 0) {
                                    $shippingMethodsArray[$index]['name'] = $values['title'];
                                    $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                                    $shippingMethodsArray[$index]['charge'] = @round($values['handling_fee'] * $info['total_quantity'], 2);
                                    $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                                    $index++;
                                } else {
                                    $shippingMethodsArray[$index]['name'] = $values['title'];
                                    $shippingMethodsArray[$index]['delivery_time'] = $values['delivery_time'];
                                    $shippingMethodsArray[$index]['charge'] = @round(($values['handling_fee'] / 100) * $info['total_price'], 2);
                                    $shippingMethodsArray[$index]['shippingmethod_id'] = $values['shippingmethod_id'];
                                    $index++;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $shippingMethodsArray;
    }

    /*
     *  get combination price
     */

    public function getcombinationPrice($combination_id, $product_id) {

        if (!$combination_id || !$product_id)
            return;

        $initialPrice = 0.00;

        $cartProductFieldsMeta = Engine_Api::_()->getDbTable('CartproductFieldMeta', 'sitestoreproduct');
        $cartProductFieldsOptions = Engine_Api::_()->getDbtable('CartproductFieldOptions', 'sitestoreproduct');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('CombinationAttributes', 'sitestoreproduct');
        $combinationTable = Engine_Api::_()->getDbTable('Combinations', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('CombinationAttributeMap', 'sitestoreproduct');
        $combinationTableName = $combinationTable->info('name');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $cartProductFieldsMetaTableName = $cartProductFieldsMeta->info('name');
        $cartProductFieldsOptionsTableName = $cartProductFieldsOptions->info('name');

        $select = $combinationAttributesTable->select()
                ->from($combinationAttributesTableName, array('price_increment', 'price'))
                ->setIntegrityCheck(false)
                ->joinInner($combinationAttributeMapsTableName, $combinationAttributeMapsTableName . ".attribute_id = " . $combinationAttributesTableName . ".attribute_id", array())
                ->joinInner($combinationTableName, $combinationTableName . ".combination_id = " . $combinationAttributeMapsTableName . ".combination_id", array())
                ->where($combinationTableName . ".combination_id = ?", $combination_id)
                ->where($combinationAttributesTableName . ".product_id = ?", $product_id);

        $result = $select->query()->fetchAll();
        foreach ($result as $row => $value) {
            if ($value['price_increment'])
                $initialPrice += $value['price'];
            else
                $initialPrice -= $value['price'];
        }


        return $initialPrice;
    }


    /*
    *  Send invite email
    */
    public function sendInvites($recipients, $storeinvite_id = null, $storeinvite_userid = null, $invite_message=null) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
    $message = $this->translate(Engine_Api::_()->getApi('settings', 'core')->invite_message);
    $message = trim($message);

    $template_header = '';
    $template_footer = '';
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.site.title', $site_title);
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.font.color', "#79b4d4");
    $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
    $template_header.= "<tr><td style='background:" . $site_header_color . "; color:" . $site_title_color . ";font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
    $inviter_name = $viewer->getTitle();
    $sitestoreModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    if ($storeinvite_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $storeinvite_id);
      if ($sitestore) {
        if(!Engine_Api::_()->core()->hasSubject('sitestore_store'))
            Engine_Api::_()->core()->setSubject($sitestore);
      }
    }
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id),
                    ), 'sitestore_entry_view', true);

    //GETTING THE STORE PHOTO.
     $file = $sitestore->getPhotoUrl('thumb.icon');
    if (empty($file)) {
      $storephoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Sitestore/externals/images/nophoto_list_thumb_normal.png';
    } else {
      $storephoto_path = $file;
    }

    $inviteUrl_link = '<table><tr valign="top"><td style="color:#999999;font-size:11px;padding-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $storephoto_path . '" style="width:100px;"/>' . '</a>';
    //GETTING NO OF LIKES TO THIS STORE.
    $num_of_like = Engine_Api::_()->sitestore()->numberOfLike('sitestore_store', $sitestore->store_id);
    $body_message = $inviteUrl_link . $sitestore->title . '<br /> ' . $this->translate(array('%s Person Likes This', '%s People Like This', $num_of_like), $num_of_like);

    $recepients_array = array();

    $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
    $store_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $sitestore->title . '</a>';
    $store_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreinvite.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreinvite.utility.type', convert_uuencode($sitestoreModHostName));
    }

    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
    if (is_array($recipients) && !empty($recipients) && !empty($storeinvite_id) && !empty($storeinvite_userid)) {

      $table_message = Engine_Api::_()->getDbtable('messages', 'messages');
      $tableName_message = $table_message->info('name');

      $table_user = Engine_Api::_()->getitemtable('user');
      $tableName_user = $table_user->info('name');

      $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');
      $tableName_user_memberships = $table_user_memberships->info('name');

      foreach ($recipients as $recipient) {
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);

        // watch out for poorly formatted emails
        if (!empty($recipient)) {
          //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
          $select = $table_user->select()
                  ->setIntegrityCheck(false)
                  ->from($tableName_user, array('user_id'))
                  ->where('email = ?', $recipient);
          $is_site_members = $table_user->fetchAll($select);

          //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
          if (isset($is_site_members[0]) && !empty($is_site_members[0]->user_id) && $is_site_members[0]->user_id != $viewer->user_id) {
            $contact = Engine_Api::_()->user()->getUser($is_site_members[0]->user_id);

            // check that user has not blocked the member
            if (!$viewer->isBlocked($contact)) {
              $recepients_array[] = $is_site_members[0]->user_id;
              $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

              // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND
              if ($is_suggenabled) {
                Engine_Api::_()->sitestoreinvite()->sendSuggestion($is_site_members[0], $viewer, $sitestore->store_id);
              }
              // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND
              else {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members[0], $viewer, $sitestore, 'sitestore_suggested');
              }
            }
          }
          // BODY OF STORE COMPRISING LIKES
          $body = $inviteUrl_link . '<br/>' . $this->translate(array('%s person likes this', '%s people like this', $num_of_like), $num_of_like) . '</td><td>';

          if (!empty($invite_message)) {
            $body .= $invite_message . "<br />";
          }
          $link = '<a href="' . $base_url . '">' . $base_url . '</a>';
          $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";

          // IF THE PERSON IS NOT THE SITE MEMBER
          if (!isset($is_site_members[0])) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'Sitestoreinvite_User_Invite', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'site_title_linked' => $site_title_linked,
                'store_title_linked' => $store_title_linked,
                'store_link' => $store_link,
                'site_title' => $site_title,
                'body' => $body,
                'store_title' => $sitestore->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
          // IF THE PERSON IS A SITE MEMBER
          else {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'SITESTOREINVITE_MEMBER_INVITE', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'store_title_linked' => $store_title_linked,
                'store_link' => $store_link,
                'site_title' => $site_title,
                'body' => $body,
                'store_title' => $sitestore->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
        }
      } // end foreach
    } // end if (is_array($recipients) && !empty($recipients))
    return;
  }

    /**
     * Translate the text from english to specified language by user
     *
     * 	@param message string
     *   @return string
     */
    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }

}

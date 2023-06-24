<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Form_Search extends Engine_Form {

  protected $_searchForm;
  protected $_item;
  //Changes in onchange event function for mobile mode.
  protected $_hasMobileMode = false;

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }

  public function init() {
    // Add custom elements
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('GET')
    ;
    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    $this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setAction($view->url(array(), 'sitestoreoffer_browse', true))->getDecorator('HtmlTag')->setOption('class', '');
  }

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function getAdditionalOptionsElement() {
    $subform = new Zend_Form_SubForm(array(
        'name' => 'extra',
        'order' => 19999999,
        'decorators' => array(
            'FormElements',
        )
    ));
    Engine_Form::enableForm($subform);

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $search_column = array();
    $coreContent_table = Engine_Api::_()->getDbtable('content', 'core');
    $select_content = $coreContent_table->select()->where('name = ?', 'sitestoreoffer.search-sitestoreoffer');
    $params = $coreContent_table->fetchAll($select_content);
    foreach ($params as $widget) {
      if (isset($widget['params']['search_column'])) {
        $search_column = $widget['params']['search_column'];
      }
    }

    $showTabArray = Zend_Controller_Front::getInstance()->getRequest()->getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => "5"));


    $enabledColumns = array_intersect($search_column, $showTabArray);
    if (empty($enabledColumns)) {
      $enabledColumns = $showTabArray;
    }

    $i = -5000;

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'category_id');
    if (in_array("2", $enabledColumns)) {
      $this->addElement('Text', 'title', array(
          'label' => 'Store Title',
          'order' => $row->order - 4,
          'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
           ),
      ));
    }
    if (in_array("3", $enabledColumns)) {
      $this->addElement('Text', 'search_offer', array(
          'label' => 'Coupon Title',
          'order' => $row->order - 5,
          'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
           ),
      ));
    }

    if (in_array("1", $enabledColumns)) {
      $show_multiOptions = array();
      $show_multiOptions[0] = '';
      $show_multiOptions["hotoffer"] = 'Hot Coupons';
      $show_multiOptions["sponsored offer"] = 'Sponsored Coupons';
      $show_multiOptions["end_week"] = 'Ending this Week';
      $show_multiOptions["end_month"] = 'Ending this Month';
      $show_multiOptions["creation_date"] = 'Latest Coupons';
      $show_multiOptions["claimed"] = 'Most Popular Coupons';
      $show_multiOptions["like_count"] = 'Most Liked Coupons';
      $show_multiOptions["view_count"] = 'Most Viewed Coupons';
      $show_multiOptions["comment_count"] = 'Most Commented Coupons';
      $show_multiOptions["end_offer"] = 'Expired Coupons';
      $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.network', 0);
      if (empty($enableNetwork)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

        if (!empty($viewerNetwork) || Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
          $show_multiOptions['Networks'] = 'Only My Networks';
          $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.default.show', 0);

          if (!isset($_GET['orderby']) && !empty($browseDefaulNetwork)) {
            $value_deault = 3;
          } elseif (isset($_GET['orderby'])) {
            $value_deault = $_GET['orderby'];
          }
        }
      }


      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => $show_multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestoreoffers();',
          'order' => $row->order - 3,
      ));
    } else {
      $this->addElement('hidden', 'orderby', array(
      ));
    }

//    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
//    if (!empty($enableLocation) && in_array("5", $enabledColumns)) {
//      $row_search = $this->_searchForm->getFieldsOptions('sitestore', 'locationmiles');
//      $enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximitysearch', 1);
//      if (!empty($enableProximitysearch)) {
//        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
//        if ($flage) {
//          $locationLable = "Within Kilometers";
//          $locationOption = array(
//              '0' => '',
//              '1' => '1 Kilometer',
//              '2' => '2 Kilometers',
//              '5' => '5 Kilometers',
//              '10' => '10 Kilometers',
//              '20' => '20 Kilometers',
//              '50' => '50 Kilometers',
//              '100' => '100 Kilometers',
//              '250' => '250 Kilometers',
//              '500' => '500 Kilometers',
//              '750' => '750 Kilometers',
//              '1000' => '1000 Kilometers',
//          );
//        } else {
//          $locationLable = "Within Miles";
//          $locationOption = array(
//              '0' => '',
//              '1' => '1 Mile',
//              '2' => '2 Miles',
//              '5' => '5 Miles',
//              '10' => '10 Miles',
//              '20' => '20 Miles',
//              '50' => '50 Miles',
//              '100' => '100 Miles',
//              '250' => '250 Miles',
//              '500' => '500 Miles',
//              '750' => '750 Miles',
//              '1000' => '1000 Miles',
//          );
//        }
//        $this->addElement('Select', 'locationmiles', array(
//            'label' => $locationLable,
//            'multiOptions' => $locationOption,
//            'value' => '0',
//            'order' => $row->order - 1,
//        ));
//      }
//      $this->addElement('Text', 'sitestore_location', array(
//          'label' => 'Location',
//          'order' => $row->order - 2,
//      ));
//    }

    if (in_array("4", $enabledColumns)) {
      // prepare categories
      $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $onChangeEvent = "subcategoryies(this.value, '', '');";
          $categoryFiles = 'application/modules/Sitestore/views/scripts/_Subcategory.tpl';
        } else {
          $onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
          $categoryFiles = 'application/modules/Sitestore/views/sitemobile/scripts/_Subcategory.tpl';
        }

        // category field
        $this->addElement('Select', 'category_id', array(
            'label' => $view->translate('Store Category'),
            'order' => $row->order,
            'multiOptions' => $categories_prepared,
            'onchange' => $onChangeEvent,
        ));
      }

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => $row->order + 1,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));

      $this->addElement('Select', 'subsubcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => $row->order + 1,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));
    } else {
      $this->addElement('Hidden', 'category_id', array(
          'order' => $i--,
      ));

      $this->addElement('Hidden', 'subcategory_id', array(
          'order' => $i--,
      ));

      $this->addElement('Hidden', 'subsubcategory_id', array(
          'order' => $i--,
      ));
    }

    $this->addElement('Hidden', 'store', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'category', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategory', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'categoryname', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
        'order' => $i--,
    ));

    // init to
    $this->addElement('Hidden', 'resource_id', array());

    $subform->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
    ));

    $this->addSubForm($subform, $subform->getName());

    return $this;
  }

}

?>
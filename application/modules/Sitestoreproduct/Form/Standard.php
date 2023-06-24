<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Standard.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Form_Standard extends Engine_Form {

    protected $_item;
    protected $_processedValues = array();
    protected $_topLevelId;
    protected $_topLevelValue;
    protected $_isCreation = false;

    //ADD CUSTOM ELEMENT PATHS?
    public function __construct($options = null) {
        Engine_FOrm::enableForm($this);
        self::enableForm($this);

        parent::__construct($options);
    }

    public static function enableForm(Zend_Form $form) {
        $form
                ->addPrefixPath('Fields_Form_Element', APPLICATION_PATH . '/application/modules/Fields/Form/Element', 'element');
    }

    /* General */

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function setTopLevelId($id) {
        $this->_topLevelId = $id;
        return $this;
    }

    public function getTopLevelId() {
        return $this->_topLevelId;
    }

    public function setTopLevelValue($val) {
        $this->_topLevelValue = $val;
        return $this;
    }

    public function getTopLevelValue() {
        return $this->_topLevelValue;
    }

    public function setIsCreation($flag = true) {
        $this->_isCreation = (bool) $flag;
        return $this;
    }

    public function setProcessedValues($values) {
        $this->_processedValues = $values;
        $this->_setFieldValues($values);
        return $this;
    }

    public function getProcessedValues() {
        return $this->_processedValues;
    }

    public function getFieldMeta() {
        return Engine_Api::_()->fields()->getFieldsMeta($this->getItem());
    }

    public function getFieldStructure() {
        // Let's allow fallback for no profile type (for now at least)
        if (!$this->_topLevelId || !$this->_topLevelValue) {
            $this->_topLevelId = null;
            $this->_topLevelValue = null;
        }
        $a = Engine_Api::_()->fields()->getFieldsStructureFull('sitestoreform', $this->_topLevelId, $this->_topLevelValue);

        return Engine_Api::_()->fields()->getFieldsStructureFull('sitestoreform', $this->_topLevelId, $this->_topLevelValue);
    }

    // Main
    public function generate() {
        $page_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_url', null);

        $store_id = Engine_Api::_()->sitestore()->getStoreId($page_url);
        $itempageforms_table = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
        $select_sitestoreform = $itempageforms_table->select()->where('store_id = ?', $store_id);
        $select_form_result = $itempageforms_table->fetchRow($select_sitestoreform);

        $form_title = $select_form_result->title;
        $form_description = $select_form_result->description;

        if ($select_form_result->activeyourname == 1 || $select_form_result->activeemail == 1 || ($select_form_result->activeemail == 1) || $select_form_result->activemessage == 1) {
            $var = 0;
        } else {
            $var = 1;
        }

        $this
                ->setTitle($form_title)
                ->setDescription($form_description);
        $this->setAttrib("id", 'show_form');
        $this->setAttrib('class', 'seaocore_form_comment');
        $action = Zend_Controller_Front::getInstance()->getBaseUrl() . '/widget/index/mod/sitestoreform/name/sitestore-viewform';
        $this->setAttrib("action", $action);
        $struct = $this->getFieldStructure();
        $orderIndex = 0;
        //TITLE
        if ($select_form_result->activeyourname == 1) {
            $this->addElement('Text', 'sender_name', array(
                'label' => 'Your Name *',
                'order' => $orderIndex++,
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
            )));
        }
        if ($select_form_result->activeemail == 1) {
            $this->addElement('Text', 'sender_email', array(
                'label' => 'Your Email *',
                'order' => $orderIndex++,
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
            )));
        }
        // MESSAGE
        if ($select_form_result->activemessage == 1) {
            $this->addElement('textarea', 'message', array(
                'label' => 'Message *',
                'order' => $orderIndex++,
                'required' => true,
                'allowEmpty' => false,
                'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                    new Engine_Filter_Censor(),
                ),
            ));
        }
        foreach ($struct as $fskey => $map) {
            $field = $map->getChild();

            // Skip fields hidden on signup
            if (isset($field->show) && !$field->show && $this->_isCreation) {
                continue;
            }

            // Add field and load options if necessary
            $params = $field->getElementParams($this->getItem());

            //$key = 'field_' . $field->field_id;
            $key = $map->getKey();

            // If value set in processed values, set in element
            if (!empty($this->_processedValues[$field->field_id])) {
                $params['options']['value'] = $this->_processedValues[$field->field_id];
            }

            if (!@is_array($params['options']['attribs'])) {
                $params['options']['attribs'] = array();
            }

            // Heading
            if ($params['type'] == 'Heading') {
                $params['options']['value'] = Zend_Registry::get('Zend_Translate')->_($params['options']['label']);
                unset($params['options']['label']);
            }

            // Order
            // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
            $params['options']['order'] = $orderIndex++;

            $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
            unset($params['options']['alias']);
            unset($params['options']['publish']);
            if ($params['type'] == "Radio" || $params['type'] == "Multiselect" || $params['type'] == "MultiCheckbox" || $params['type'] == "Select") {
                $counter = count($params['options']['multiOptions']);
                if ($counter == 0)
                    continue;
            }
            if ($params['type'] == "Select") {
                $counter = count($params['options']['multiOptions']);
                if ($counter == 1)
                    continue;
            }
            $this->addElement($inflectedType, $key, $params['options']);
            $element = $this->getElement($key);

            if (method_exists($element, 'setFieldMeta')) {
                $element->setFieldMeta($field);
            }

            // Set attributes for hiding/showing fields using javscript
            $classes = 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
            $element->setAttrib('class', $classes);


            if ($field->canHaveDependents()) {
                $element->setAttrib('onchange', 'changeFields(this)');
            }

            if ($field->isHeading()) {
                $element->removeDecorator('Label')
                        ->removeDecorator('HtmlTag')
                        ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
            }
        }

        // SEND COPY TO ME
        if (($select_form_result->activeemail == 1) && ($select_form_result->activeemailself == 1)) {
            $this->addElement('Checkbox', 'send_me', array(
                'label' => "Send a copy to my email address.",
            ));
            $this->send_me->getDecorator("label")->setOption("placement", "append");
        }

        // SEND COPY TO ME
        if (($select_form_result->activeemail == 1) && ($select_form_result->activeemailself == 1)) {
            $this->addElement('Checkbox', 'send_me', array(
                'label' => "Send a copy to my email address."
            ));
        }
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.captcha', 1) && empty($viewer_id)) {
            if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            } else {
                $this->addElement('captcha', 'captcha', array(
                    'description' => 'Please type the characters you see in the image.',
                    'captcha' => 'image',
                    'required' => true,
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'fontSize' => '30',
                        'timeout' => 300,
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
                $this->captcha->getDecorator("Description")->setOption("placement", "append");
            }
        }

        // Element: SEND

        $this->addElement('Hidden', 'subject', array(
            'order' => 991,
            'value' => "sitestore_store_$store_id"
        ));

        if ($var == 0 || !empty($struct)) {
            $this->addElement('Button', 'send_11', array(
                'label' => "Submit",
                'type' => 'submit'
            ));
        } else {
            $this->addElement('Hidden', 'show_error_msg', array('order' => 678, 'value' => 1));
        }
    }

    protected function _setFieldValues($values) {
        // Iterate over elements and apply the values
        foreach ($this->getElements() as $key => $element) {
            if (count(explode('_', $key)) == 3) {
                list($parent_id, $option_id, $field_id) = explode('_', $key);
                if (isset($values[$field_id])) {
                    $element->setValue($values[$field_id]);
                }
            }
        }
    }

    /* These are hacks to existing form methods */

    public function init() {
        $this->generate();
    }

}

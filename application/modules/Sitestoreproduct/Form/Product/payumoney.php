<?php



class Sitestoreproduct_Form_Product_Payumoney extends Engine_Form {

    public function init() {
        parent::init();

        $this->setTitle('Payment Gateway: Payumoney');

        $description = $this->getTranslator()->translate('Add steps here to get the gateway credentials.');

        $description .= ' <br/> ' . '<div id="show_payumoney_form_massges"></div>';
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);




        /*
         * Add the code to create form elements for entering gateway credentials.
         */
        $this->addElement('Text', 'merchant_key', array(
            'label' => 'Merchant Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'salt', array(
            'label' => 'Secret Key (Salt)',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));



        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();

        $showExtraInfo = true;
        if ($module == 'siteevent' || $module == 'siteeventticket') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        } elseif ($module == 'sitestore' || $module == 'sitestoreproduct') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }

        if (empty($isPaymentToSiteEnable)) {
            $showExtraInfo = false;
        }

        if (!empty($showExtraInfo)) {

            // Element: enabled
            $this->addElement('Radio', 'enabled', array(
                'label' => 'Enabled?',
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => '0',
            ));

            // Element: execute
            $this->addElement('Button', 'submit', array(
                'label' => 'Save Changes',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => '_formSetDivAddress.tpl',
                            'class' => 'form element')))
            ));

            $this->addDisplayGroup(array('submit'), 'buttons', array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper',
                ),
            ));
        }
    }

}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Graph.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Settings_Graph extends Engine_Form {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Product Sales Statistical Graphs Settings')
            ->setDescription("Store owner can view graphical statistics of various performance metrics of their store like Grand Total, Net Ammount, Total transactions and Commissoin of their store and ads over different time periods. Below, you can customize the theme and other parameters of the graphs.");

    // COLOR VALUE FOR GRAPH BACKGROUND
    $this->addElement('Text', 'sitestoreproduct_graph_bgcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowGraphbg.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH Grand Total LINES WIDTH
    $this->addElement('Text', 'sitestoreproduct_graphgrossamount_width', array(
        'label' => 'Grand Total Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Grand Total in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphgrossamount.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR Grand Total LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphgrossamount_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowGrossAmount.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH Subtotal LINES WIDTH
    $this->addElement('Text', 'sitestoreproduct_graphnetamount_width', array(
        'label' => 'Subtotal Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Subtotal in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphnetamount.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR Subtotal LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphnetamount_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowNetAmount.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH TOTAL TRANSACTION LINES WIDTH
    $this->addElement('Text', 'sitestoreproduct_graphtransactions_width', array(
        'label' => 'Total Transactions Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Total Transactions in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtransactions.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR TOTAL TRANSACTION LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphtransactions_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowTotalTransaction.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH COMMISSION LINES WIDTH
        $this->addElement('Text', 'sitestoreproduct_graphcommission_width', array(
        'label' => 'Commission Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Commission in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphcommission.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR COMMISSION LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphcommission_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowCommission.tpl',
                    'class' => 'form element'
            )))
    ));
    
    // Element: GRAPH TAX LINES WIDTH
        $this->addElement('Text', 'sitestoreproduct_graphtax_width', array(
        'label' => 'Tax Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Tax in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtax.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR TAX LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphtax_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowTax.tpl',
                    'class' => 'form element'
            )))
    ));
    
    // Element: GRAPH SHIPPING PRICE LINES WIDTH
        $this->addElement('Text', 'sitestoreproduct_graphshippingprice_width', array(
        'label' => 'Shipping Price Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Shipping Price in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphshippingprice.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR SHIPPING PRICE LINE OF GRAPH
    $this->addElement('Text', 'sitestoreproduct_graphshippingprice_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowShippingPrice.tpl',
                    'class' => 'form element'
            )))
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
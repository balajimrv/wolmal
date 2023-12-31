<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MangoPay.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_MangoPay extends Engine_Form {

    public function init() {
        parent::init();

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('Personal Details for MangoPay Account')));
        $this->setName('sitestoreproduct_payment_info_mangopay');
        $description = 'Enter your personal details below to be used in MangoPay account. <br>
[Note: You need to fill all the information accurately in ‘Personal Details for MangoPay Account’ and ‘MangoPay Bank Account Configuration’.]<br />Below, you can configure your MangoPay Account to start receiving payments. This information should be accurately provided and enabled.<div id="show_mangopay_form_massges" class="tool_tip"></div>';
        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->setDescription($description);
        $this->addElement('Text', 'first_name', array(
            'label' => 'First Name',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'last_name', array(
            'label' => 'Last Name',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'email', array(
            'label' => 'Email',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            ),
        ));
        $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

        $this->addElement('Date', 'birthday', array(
            'label' => 'Birthday',
            'allowEmpty' => false,
            'required' => true,
            'value' =>date('Y-m-d')
        ));
        $countryCodes = array(
                    "AD"=>"AD","AE"=>"AE","AF"=>"AF","AG"=>"AG","AI"=>"AI",
                    "AL"=>"AL","AM"=>"AM","AO"=>"AO","AQ"=>"AQ","AR"=>"AR",
                    "AS"=>"AS","AT"=>"AT","AU"=>"AU","AW"=>"AW","AX"=>"AX",
                    "AZ"=>"AZ","BA"=>"BA","BB"=>"BB","BD"=>"BD","BE"=>"BE",
                    "BF"=>"BF","BG"=>"BG","BH"=>"BH","BI"=>"BI","BJ"=>"BJ",
                    "BL"=>"BL","BM"=>"BM","BN"=>"BN","BO"=>"BO","BQ"=>"BQ",
                    "BR"=>"BR","BS"=>"BS","BT"=>"BT","BV"=>"BV","BW"=>"BW",
                    "BY"=>"BY","BZ"=>"BZ","CA"=>"CA","CC"=>"CC","CD"=>"CD",
                    "CF"=>"CF","CG"=>"CG","CH"=>"CH","CI"=>"CI","CK"=>"CK",
                    "CL"=>"CL","CM"=>"CM","CN"=>"CN","CO"=>"CO","CR"=>"CR",
                    "CU"=>"CU","CV"=>"CV","CW"=>"CW","CX"=>"CX","CY"=>"CY",
                    "CZ"=>"CZ","DE"=>"DE","DJ"=>"DJ","DK"=>"DK","DM"=>"DM",
                    "DO"=>"DO","DZ"=>"DZ","EC"=>"EC","EE"=>"EE","EG"=>"EG",
                    "EH"=>"EH","ER"=>"ER","ES"=>"ES","ET"=>"ET","FI"=>"FI",
                    "FJ"=>"FJ","FK"=>"FK","FM"=>"FM","FO"=>"FO","FR"=>"FR",
                    "GA"=>"GA","GB"=>"GB","GD"=>"GD","GE"=>"GE","GF"=>"GF",
                    "GG"=>"GG","GH"=>"GH","GI"=>"GI","GL"=>"GL","GM"=>"GM",
                    "GN"=>"GN","GP"=>"GP","GQ"=>"GQ","GR"=>"GR","GS"=>"GS",
                    "GT"=>"GT","GU"=>"GU","GW"=>"GW","GY"=>"GY","HK"=>"HK",
                    "HM"=>"HM","HN"=>"HN","HR"=>"HR","HT"=>"HT","HU"=>"HU",
                    "ID"=>"ID","IE"=>"IE","IL"=>"IL","IM"=>"IM","IN"=>"IN",
                    "IO"=>"IO","IQ"=>"IQ","IR"=>"IR","IS"=>"IS","IT"=>"IT",
                    "JE"=>"JE","JM"=>"JM","JO"=>"JO","JP"=>"JP","KE"=>"KE",
                    "KG"=>"KG","KH"=>"KH","KI"=>"KI","KM"=>"KM","KN"=>"KN",
                    "KP"=>"KP","KR"=>"KR","KW"=>"KW","KY"=>"KY","KZ"=>"KZ",
                    "LA"=>"LA","LB"=>"LB","LC"=>"LC","LI"=>"LI","LK"=>"LK",
                    "LR"=>"LR","LS"=>"LS","LT"=>"LT","LU"=>"LU","LV"=>"LV",
                    "LY"=>"LY","MA"=>"MA","MC"=>"MC","MD"=>"MD","ME"=>"ME",
                    "MF"=>"MF","MG"=>"MG","MH"=>"MH","MK"=>"MK","ML"=>"ML",
                    "MM"=>"MM","MN"=>"MN","MO"=>"MO","MP"=>"MP","MQ"=>"MQ",
                    "MR"=>"MR","MS"=>"MS","MT"=>"MT","MU"=>"MU","MV"=>"MV",
                    "MW"=>"MW","MX"=>"MX","MY"=>"MY","MZ"=>"MZ","NA"=>"NA",
                    "NC"=>"NC","NE"=>"NE","NF"=>"NF","NG"=>"NG","NI"=>"NI",
                    "NL"=>"NL","NO"=>"NO","NP"=>"NP","NR"=>"NR","NU"=>"NU",
                    "NZ"=>"NZ","OM"=>"OM","PA"=>"PA","PE"=>"PE","PF"=>"PF",
                    "PG"=>"PG","PH"=>"PH","PK"=>"PK","PL"=>"PL","PM"=>"PM",
                    "PN"=>"PN","PR"=>"PR","PS"=>"PS","PT"=>"PT","PW"=>"PW",
                    "PY"=>"PY","QA"=>"QA","RE"=>"RE","RO"=>"RO","RS"=>"RS",
                    "RU"=>"RU","RW"=>"RW","SA"=>"SA","SB"=>"SB","SC"=>"SC",
                    "SD"=>"SD","SE"=>"SE","SG"=>"SG","SH"=>"SH","SI"=>"SI",
                    "SJ"=>"SJ","SK"=>"SK","SL"=>"SL","SM"=>"SM","SN"=>"SN",
                    "SO"=>"SO","SR"=>"SR","SS"=>"SS","ST"=>"ST","SV"=>"SV",
                    "SX"=>"SX","SY"=>"SY","SZ"=>"SZ","TC"=>"TC","TD"=>"TD",
                    "TF"=>"TF","TG"=>"TG","TH"=>"TH","TJ"=>"TJ","TK"=>"TK",
                    "TL"=>"TL","TM"=>"TM","TN"=>"TN","TO"=>"TO","TR"=>"TR",
                    "TT"=>"TT","TV"=>"TV","TW"=>"TW","TZ"=>"TZ","UA"=>"UA",
                    "UG"=>"UG","UM"=>"UM","US"=>"US","UY"=>"UY","UZ"=>"UZ",
                    "VA"=>"VA","VC"=>"VC","VE"=>"VE","VG"=>"VG","VI"=>"VI",
                    "VN"=>"VN","VU"=>"VU","WF"=>"WF","WS"=>"WS","YE"=>"YE",
                    "YT"=>"YT","ZA"=>"ZA","ZM"=>"ZM","ZW"=>"ZW"
        );
        $this->addElement('Select', 'nationality', array(
            'label' => "Nationality",
            'multiOptions' => $countryCodes,
            'allowEmpty' => false,
            'required' => true,
        ));
        $this->addElement('Select', 'residence', array(
            'label' => "Country Residence",
            'multiOptions' => $countryCodes,
            'allowEmpty' => false,
            'required' => true,
        ));
    }

}

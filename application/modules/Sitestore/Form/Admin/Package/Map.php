<?php

class Sitestore_Form_Admin_Package_Map extends Engine_Form {

    public function init() {
        $this
                ->setMethod('post')
                ->setAttrib('class', 'global_form_box')
                ->setDescription('Please select the store package that you want to associate with this subscription plan');
        //Element: profile_type
        $table = Engine_Api::_()->getDbtable('packages', 'sitestore');
        $storeName = Engine_Api::_()->getItemtable('sitestore_store')->info("name");
        $select = $table->select()->where('enabled =?', 1)->where('approved =?', 1)->query()->fetchAll();
        if (count($select) > 0) {
            $isStorePackageAvailable = true;
            foreach ($select as $package) {
                $options[$package['package_id']] = $package['title'];
            }
            if (count($options) > 0) {
                $this->addElement('Select', 'package', array(
                    'label' => 'Associate package',
                    'multiOptions' => $options,
                    'required' => true,
                    'allowEmpty' => false,
                ));
            }
        } else {
            $this
                    ->setDescription('For Packages to be mapped to subcription plan, it is necessary that store package should have "Auto-Approve" setting enabled.');
            $isStorePackageAvailable = false;
        }

        if (!empty($isStorePackageAvailable)) {
            $this->addElement('Button', 'yes_button', array(
                'label' => 'Save',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array('ViewHelper')
            ));

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'href' => '',
                'onClick' => 'javascript:parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper'
                )
            ));
        } else {

            $this->addElement('Button', 'cancel', array(
                'label' => 'Close',
                'link' => true,
                'href' => '',
                'onClick' => 'javascript:parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper'
                )
            ));
        }
        $this->addDisplayGroup(array('yes_button', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}

?>
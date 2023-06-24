<?php
class Sitestoreproduct_Form_Admin_Template extends Engine_Form {

    public function init() {

        $this->setTitle('Product Layout Settings')
                ->setDescription('Below, you can choose layouts for selected important pages of this plugin.')
                ->setAttrib('name', 'template');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $defaultHome = "Template 1 (Default)
    " . '<a href="http://demo.socialengineaddons.com/events" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh3.googleusercontent.com/-7_MgPiVmP30/UuzEuGN8FvI/AAAAAAAAA0E/mQVWKVjfqH8/w463-h770-no/eventshome_default.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template1Home = "Template 2
    " . '<a href="http://demo.socialengineaddons.com/pages/events-home-template2" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh5.googleusercontent.com/-LkSJKv5eOz0/UuzEvjaqL9I/AAAAAAAAA00/DuDycq7ClXo/w330-h771-no/eventshome_template2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $this->addElement('Radio', 'sitestoreproduct_product_profiletemp', array(
            'label' => 'Product Profile Page',
            'description' => 'Choose from below the template for Product Profile Page of your site.',
            'multiOptions' => array(
                'default' => $defaultHome,
              'template2' => $template1Home,
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('sitestoreproduct.product.profiletemp', 'default'),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            //'onclick' => 'confirmSubmit()',
            'ignore' => true
        ));
    }

}
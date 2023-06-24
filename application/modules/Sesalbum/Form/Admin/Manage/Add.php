<?php
class Sesalbum_Form_Admin_Manage_Add extends Engine_Form {
  public function init() {
    $this->setTitle('Integrate New Plugin')
            ->setDescription('Here, you can configure the required details for the plugin to be integrated.');
    $integrateothermoduleId = Zend_Controller_Front::getInstance()->getRequest()->getParam('integrateothermodule_id', 0);
    if (!$integrateothermoduleId) {
      $integrateothermoduleItem = array();
      $integrateothermoduleArray = array();
      $integrateothermoduleArray[] = '';
			//get all enabled modules
      $coreTable = Engine_Api::_()->getDbTable('modules', 'core');
      $select = $coreTable->select()
              ->from($coreTable->info('name'), array('name', 'title'))
              ->where('enabled =?', 1)
              ->where('type =?', 'extra');
      $resultsArray = $select->query()->fetchAll();
      if (!empty($resultsArray)) {
        foreach ($resultsArray as $result) {
          $integrateothermoduleArray[$result['name']] = $result['title'];
        }
      }
      if (!empty($integrateothermoduleArray)) {
        $this->addElement('Select', 'module_name', array(
            'label' => 'Choose Plugin',
            'description' => 'Below, you can choose the plugin to be integrated.',
            'allowEmpty' => false,
            'onchange' => 'changemodule(this.value)',
            'multiOptions' => $integrateothermoduleArray,
        ));
      } else {
        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("Here are no module to configure with our plugin lightbox.") . "</span></div>";
        $this->addElement('Dummy', 'module', array(
            'description' => $description,
        ));
        $this->module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      }
      $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
      if (!empty($module)) {
        $this->module_name->setValue($module);
				//get manifest item for given module
        $integrateothermodule = Engine_Api::_()->sesalbum()->getPluginItem($module);
        if (empty($integrateothermodule))
          $this->addElement('Dummy', 'dummy_title', array(
              'description' => 'No item type define for this plugin.',
          ));
      }
    }
		$param = false;
    if ($integrateothermoduleId)
      $param = true;
    elseif ($integrateothermodule)
      $param = true;
    if ($param) {
      if (!$integrateothermoduleId) {
        $this->addElement('Select', 'content_type', array(
            'label' => 'Item Type of Plugin for Album Table',
            'description' => 'Select the item type for photo parent of the above chosen plugin which is defined in its manifest.php file. [This item type is the parent to which photos are associated. For example there are no albums for photos in SocialEngine Groups plugin, so simply choose “groups”, and if any plugin has Albums of photos then choose “PLUGIN_album”]',
            'multiOptions' => $integrateothermodule,
        ));
        $this->addElement('Select', 'content_type_photo', array(
            'label' => 'Item Type of Plugin for Photo Table',
            'description' => 'Select the item type for photo of the above chosen plugin which is defined in its manifest.php file. [This item type for the photos of plugins. For example for SocialEngine Groups plugin, choose “group_photo”.]',
            'multiOptions' => $integrateothermodule,
        ));
      }
      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable This Plugin?',
          'label' => 'Yes, enable this plugin now.',
          'value' => 1,
      ));
      $this->addElement('Button', 'execute', array(
          'label' => 'Add Plugin',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper'),
      ));
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'Cancel',
          'prependText' => ' or ',
          'ignore' => true,
          'link' => true,
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
          'decorators' => array('ViewHelper'),
      ));
    }
  }
}
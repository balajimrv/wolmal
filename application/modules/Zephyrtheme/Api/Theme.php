<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    Zephyrtheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

class Zephyrtheme_Api_Theme extends Core_Api_Abstract
{
    private $_constants;
    private $_const_file;

    public function __construct()
    {
        $this->_const_file = APPLICATION_PATH . '/application/settings/constants.xml';

        if (file_exists($this->_const_file))
            $this->_constants = simplexml_load_file($this->_const_file);
        else
            $this->_constants = simplexml_load_string('<root></root>');
    }

    public function setOptions($options, $prefix = 'zephyr')
    {
        if (is_array($options))
        {
            foreach ($options as $name => $value)
            {
                $nodes = $this->_constants->xpath('/root/constant[name="' . $prefix . '_' . $name . '"]');
 				$name_node = $nodes[0];
                if ($name_node != null)
                {
                    $name_node->value = $value;
                } else {
                    $root = $this->_constants->addChild('constant');
                    $root->addChild('name', $prefix . '_' . $name);
                    $root->addChild('value', $value);
                }
            }

            $is_saved = $this->_constants->asXML($this->_const_file);

            if ($is_saved)
                Core_Model_DbTable_Themes::clearScaffoldCache();
        } else {
            throw new Exception(__FUNCTION__ . ' requires 1st argument as array.');
        }
    }

    public function getOption($name)
    {
        $name_node = $this->_constants->xpath('/root/constant[name="' . $name . '"]');

        return (string)$name_node->value;
    }

    public function removeOption($name)
    {
        $name_node = $this->_constants->xpath('/root/constant[name="' . $name . '"]');

        if ($name_node != null)
            unset($name_node->{0});

        $is_saved = $this->_constants->asXML($this->_const_file);
		if ($is_saved)
            Core_Model_DbTable_Themes::clearScaffoldCache();
    }
}
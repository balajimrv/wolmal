<?php
class Semods_Form_Element_MultiSelect extends Engine_Form_Element_Text
{
    public $helper = 'formMultiSelect';

    protected $_separator = '<li>';

    public function loadDefaultDecorators()
    {
      if( $this->loadDefaultDecoratorsIsDisabled() )
      {
        return;
      }
      $decorators = $this->getDecorators();
      if( empty($decorators) )
      {
        $this->addDecorator('ViewHelper');
        Engine_Form::addDefaultDecorators($this);
      }
    }
}


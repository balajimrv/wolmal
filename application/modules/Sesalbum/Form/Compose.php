<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Compose.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Compose extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Compose Message');
    $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
       ->setAttrib('id', 'messages_compose');;
			     
    // init to
    $this->addElement('Text', 'to',array(
        'label'=>'Send To',
        'autocomplete'=>'off'));

    Engine_Form::addDefaultDecorators($this->to);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Send To',
      'required' => true,
      'allowEmpty' => false,
      'order' => 2,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    // init title
    $this->addElement('Text', 'title', array(
      'label' => 'Subject',
      'order' => 3,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));
    
      // init body - plain text
      $this->addElement('Textarea', 'body', array(
        'label' => 'Message',
        'order' => 4,
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Send Message',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'link' => true,
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
				'order' => 5,
        'decorators' => array(
            
        ),
    ));
		$id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id');
		if($id){
			$item = Engine_Api::_()->getItem('album', $id);
			$type = 'album';
		}
	if(!$id){
		$id = Zend_Controller_Front::getInstance()->getRequest()->getParam('photo_id');
		if($id){
			$type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type','album_photo');
			$item = Engine_Api::_()->getItem($type, $id);
			$type = 'photo';
		}
	}
		if($type == 'photo'){
			$this->addElement('Image', 'item_preview', array(
							'src' => $item->getPhotoUrl(),
							'order' => 6,
					));	
		}else{
			$this->addElement('Image', 'item_preview', array(
							'src' => $item->getPhotoUrl('thumb.normalmain'),
							'order' => 6,
					));		
		}
  }
}
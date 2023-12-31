<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Import.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
class Sitestoreproduct_Form_Import_Import extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Import a File')            
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('File', 'filename', array(
        'label' => 'Import File',
        'required' => true,
    ));
    $this->filename->getDecorator('Description')->setOption('placement', 'append');

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1))
      $ownerTitle = "Store Admins";
    else
      $ownerTitle="Just Me";

    $user = Engine_Api::_()->user()->getViewer();
    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => $ownerTitle,
    );

    //View
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    if (count($view_options) >= 1) {
      $this->addElement('Select', 'auth_view', array(
          'label' => 'View Privacy',
          'description' => 'Who may see these products?',
          'multiOptions' => $view_options,
          'value' => key($view_options),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    //Comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_comment');
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    if (count($comment_options) >= 1) {
      $this->addElement('Select', 'auth_comment', array(
          'label' => 'Comment Privacy',
          'description' => 'Who may post comments on these products?',
          'multiOptions' => $comment_options,
          'value' => key($comment_options),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }
    
         $this->addElement('Radio', 'approved', array(
              'label' => "Publish Products",
              'description' => "Do you want to publish these imported products?",
              'multiOptions' => array("0" => "Yes", "1" => "No"),
              'value' => 0,
          )
        );


    $this->addElement('Radio', 'import_seperate', array(
            'label' => 'File Columns Separator',
            'description' => 'Select a separator from below which you are using for the columns of the CSV file.',
            'multiOptions' => array(
                    1 => "Pipe ('|')",
                    0 => "Comma (',')"
            ),
            'value' => 1,
    ));
    

    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => "javascript:parent.Smoothbox.close()",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}
?>
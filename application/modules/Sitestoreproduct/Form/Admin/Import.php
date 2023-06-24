<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Import.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Admin_Import extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Import Locations')
            ->setDescription("Add a CSV file to import locations corresponding to the entries in it, then click on ‘Submit’ button.")
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('File', 'filename', array(
        'label' => 'Import File',
        'required' => true,
    ));
    $this->filename->getDecorator('Description')->setOption('placement', 'append');

//    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1))
//      $ownerTitle = "Page Admins";
//    else
//      $ownerTitle="Just Me";
//
//    $user = Engine_Api::_()->user()->getViewer();
//    $availableLabels = array(
//        'everyone' => 'Everyone',
//        'registered' => 'All Registered Members',
//        'owner_network' => 'Friends and Networks',
//        'owner_member_member' => 'Friends of Friends',
//        'owner_member' => 'Friends Only',
//        'owner' => $ownerTitle,
//    );
//
//    //View
//    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_view');
//    $view_options = array_intersect_key($availableLabels, array_flip($view_options));
//
//    if (count($view_options) >= 1) {
//      $this->addElement('Select', 'auth_view', array(
//          'label' => 'View Privacy',
//          'description' => 'Who may see these pages?',
//          'multiOptions' => $view_options,
//          'value' => key($view_options),
//      ));
//      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
//    }
//
//    //Comment
//    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_comment');
//    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
//
//    if (count($comment_options) >= 1) {
//      $this->addElement('Select', 'auth_comment', array(
//          'label' => 'Comment Privacy',
//          'description' => 'Who may post comments on these pages?',
//          'multiOptions' => $comment_options,
//          'value' => key($comment_options),
//      ));
//      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
//    }

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
        'onclick' => "javascript:showLightbox()",
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
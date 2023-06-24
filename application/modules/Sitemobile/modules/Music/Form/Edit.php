<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

class Sitemobile_modules_Music_Form_Edit extends Sitemobile_modules_Music_Form_Create
{
  public function init()
  {
    // Init form
    parent::init();
    $this
      ->setDescription('')
      ->setAttrib('id',      'form-upload-music')
      ->setAttrib('name',    'playlist_edit')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Pre-fill form values
    $this->addElement('Hidden', 'playlist_id');
    $this->removeElement('fancyuploadfileids');

    // Override submit button
    $this->removeElement('submit');
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }

  public function populate($playlist)
  {
    $this->setTitle('Edit Playlist');

    foreach (array(
      'playlist_id' => $playlist->getIdentity(),
      'title'       => $playlist->getTitle(),
      'description' => $playlist->description,
      'search'      => $playlist->search,
      ) as $key => $value) {
        $this->getElement($key)->setValue($value);
    }

    // If this is THE profile playlist, hide the title/desc fields
    if( $playlist->special ) {
      $this->removeElement('title');
      $this->removeElement('description');
      $this->removeElement('search');
    }

    // AUTHORIZATIONS
    $auth = Engine_Api::_()->authorization()->context;

    
    $auth_view = $this->getElement('auth_view');
    if ( $auth_view ) {
      $lowest_viewer = array_pop(array_keys($this->_roles));
      foreach (array_reverse(array_keys($this->_roles)) as $role) {
        if ($auth->isAllowed($playlist, $role, 'view')) {
          $lowest_viewer = $role;
        }
      }
      $auth_view->setValue($lowest_viewer);
    }

    $auth_comment = $this->getElement('auth_comment');
    if( $auth_comment ){
      $lowest_commenter = array_pop(array_keys($this->_roles));
      foreach (array_reverse(array_keys($this->_roles)) as $role) {
        if ($auth->isAllowed($playlist, $role, 'comment')) {
          $lowest_commenter = $role;
        }
      }
      $auth_comment->setValue($lowest_commenter);
    }
  }

  public function saveValues()
  {
    $playlist = parent::saveValues();
    $values   = $this->getValues();
    if ($playlist && $playlist->isEditable()) {
      $playlist->title       = $values['title'];
      $playlist->description = $values['description'];
      $playlist->search      = $values['search'];
      $playlist->save();

      //Multiple music upload
        foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
          
          $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);
          
          if (!is_uploaded_file($file['tmp_name'])) {
            continue;
          }
          
          $song = Engine_Api::_()->music()->createSong($file);
          $playlist->addSong($song->file_id);
        }//End
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($playlist) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      
      return $playlist;
    } else {
      return false;
    }
  }
}

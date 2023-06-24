<?php

class Timeline_View_Helper_ItemPhotoThumb extends Engine_View_Helper_HtmlImage
{
  protected $_noPhotos;
  
  public function itemPhotoThumb($item, $type = 'thumb.profile', $alt = "", $attribs = array())
  {
    // Whoops
    if( !($item instanceof Core_Model_Item_Abstract))
    {
      throw new Zend_View_Exception("Item must be a valid item");
    }
    $src = $this->getPhoto($item, $type);
    // Get url
    $safeName = ( $type ? str_replace('.', '_', $type) : 'main' );
    $attribs['class'] = ( isset($attribs['class']) ? $attribs['class'] . ' ' : '' );
    $attribs['class'] .= $safeName . ' ';
    $attribs['class'] .= 'item_photo_' . $item->getType() . ' ';

    // User image
    if( $src )
    {
      // Add auto class and generate
      $attribs['class'] = ( !empty($attribs['class']) ? $attribs['class'].' ' : '' ) . $safeName;
    }

    // Default image
    else
    {
      $src = $this->getNoPhoto($item, $safeName);
      $attribs['class'] .= 'item_nophoto ';            
    }

    return $this->htmlImage($this->view->baseUrl() . "/whshow_thumb_timeline.php?src=$src&w=150&h=150&cz=1", $alt, $attribs);
  }

  public function getPhoto($item, $type) {
    if( empty($item->photo_id) ) {
      return false;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id, $type);
    if( !$file ) {
      return false;
    }
    if ($file->getStorageService() instanceof  Storage_Service_Local) {
      return $file->storage_path;
    }
    else {
      return $file->map();
    }
  }

  public function getNoPhoto($item, $type)
  {
    $type = ( $type ? str_replace('.', '_', $type) : 'main' );
    
    if( ($item instanceof Core_Model_Item_Abstract) ) {
      $item = $item->getType();
    } else if( !is_string($item) ) {
      return '';
    }
    
    if( !Engine_Api::_()->hasItemType($item) ) {
      return '';
    }

    // Load from registry
    if( null === $this->_noPhotos ) {
      // Process active themes
      $themesInfo = Zend_Registry::get('Themes');
      foreach( $themesInfo as $themeName => $themeInfo ) {
        if( !empty($themeInfo['nophoto']) ) {
          foreach( (array)@$themeInfo['nophoto'] as $itemType => $moreInfo ) {
            if( !is_array($moreInfo) ) continue;
            $this->_noPhotos[$itemType] = array_merge((array)@$this->_noPhotos[$itemType], $moreInfo);
          }
        }
      }
    }
    
    // Use default
    if( !isset($this->_noPhotos[$item][$type]) ) {
      $shortType = $item;
      if( strpos($shortType, '_') !== false ) {
        list($null, $shortType) = explode('_', $shortType, 2);
      }
      $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
      $this->_noPhotos[$item][$type] = //$this->view->baseUrl() . '/' .
        'application/modules/' .
        $module .
        '/externals/images/nophoto_' .
        $shortType . '_'
        . $type . '.png';
    }

    return $this->_noPhotos[$item][$type];
  }
}
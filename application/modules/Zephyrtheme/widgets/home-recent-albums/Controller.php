<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

class Zephyrtheme_Widget_HomeRecentAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->home_style = $home_style = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_style', false);
	
    // Should we consider creation or modified recent?
    $recentType = $this->_getParam('recentType', 'creation');
    if( !in_array($recentType, array('creation', 'modified')) ) {
      $recentType = 'creation';
    }
    $this->view->recentType = $recentType;
    $this->view->recentCol = $recentCol = $recentType . '_date';
    
    // Get paginator
    $table = Engine_Api::_()->getItemTable('album');
    $select = $table->select()
      ->where('search = ?', true);
    if( $recentType == 'creation' ) {
      // using primary should be much faster, so use that for creation
      $select->order('album_id DESC');
    } else {
      $select->order($recentCol . ' DESC');
    }

    // Create new array filtering out private albums
    $viewer = Engine_Api::_()->user()->getViewer();
    $album_select = $select;
    $new_select = array();
    $i = 0;
    foreach($album_select->getTable()->fetchAll($album_select) as $album )  {
      if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')){
        $new_select[$i++] = $album;
      }
    }
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($new_select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}
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

class Zephyrtheme_Widget_TabbedMembersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->tbbnumber = $tbbnumber = $this->_getParam('tbbnumber');
	$this->view->tbbpopular = $tbbpopular = $this->_getParam('tbbpopular');
	$this->view->tbbnewest = $tbbnewest = $this->_getParam('tbbnewest');
	$this->view->tbbwomen = $tbbwomen = $this->_getParam('tbbwomen');
	$this->view->tbbmen = $tbbmen = $this->_getParam('tbbmen');
	$this->view->tbbbrowse = $tbbbrowse = $this->_getParam('tbbbrowse');
	$this->view->tbbnavposition = $tbbnavposition = $this->_getParam('tbbnavposition');

	if( !empty($tbbnumber) ){ $maxMembersShown = $tbbnumber; } else { $maxMembersShown = 22; }

    // Get popular_user
    $table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where('enabled = ?', 1)
      ->where('verified = ?', 1)
      ->where('photo_id > ?', 0)
      ->order('view_count  DESC')
	->limit($maxMembersShown);
    // Hide if nothing to show
    if( count($table->fetchAll($select)) <= 0 ) { return $this->setNoRender(); }
    $this->view->popular_user = $popular_user = $table->fetchAll($select);
	
	// Get new_user
    $table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where('enabled = ?', 1)
      ->where('verified = ?', 1)
      ->where('photo_id > ?', 0)
      ->order('creation_date DESC')
	->limit($maxMembersShown);
    // Hide if nothing to show
    if( count($table->fetchAll($select)) <= 0 ) { return $this->setNoRender(); }
    $this->view->new_user = $new_user = $table->fetchAll($select);

	//Get Women member
	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $selectNew = $db->select()
            ->from('engine4_user_fields_values')
            ->join('engine4_users','engine4_user_fields_values.item_id = engine4_users.user_id')  
            ->where('engine4_user_fields_values.field_id = ?',5)
            ->where ('engine4_user_fields_values.value = ?',3) 
            ->where('engine4_users.photo_id > ?', 0)
			->order('view_count DESC')
            ->limit($maxMembersShown);
    $memberWomen = $db->fetchAll($selectNew);
    $women = array();
    foreach ($memberWomen as $item)
    {
    	$user = Engine_Api::_()->user()->getUser($item['item_id']);
    	if ($user->getIdentity()) { $women[] = $user; }
    }
    $this->view->memberWomen= $women;
	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $selectMen = $db->select()
            ->from('engine4_user_fields_values')
            ->joinLeft('engine4_users','engine4_user_fields_values.item_id = engine4_users.user_id')  
            ->where('engine4_user_fields_values.field_id = ?',5)
            ->where ('engine4_user_fields_values.value = ?',2)
            ->where('engine4_users.photo_id > ?', 0) 
			->order('view_count DESC')
            ->limit($maxMembersShown);   
    $memberMen = $db->fetchAll($selectMen);
    $men = array();
    foreach ($memberMen as $item)
    {
    	$user = Engine_Api::_()->user()->getUser($item['item_id']);
    	if ($user->getIdentity()) { $men[] = $user; }
    }
    $this->view->memberMen= $men;
  }
  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale();
  }
  public function getCacheSpecificLifetime()
  {
    return 120;  
  }
}

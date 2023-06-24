<?php

class Activityrewards_Model_Earner extends Core_Model_Item_Abstract
{
  // Properties


  protected $_searchColumns = array('userpointearner_title', 'userpointearner_body');
  
  protected $_shortType = 'userpointearner';
  
  public $_err_msg = '';
  public $_transaction_message = '';



  protected function _insert() {

    if(is_array($this->userpointearner_metadata)) {
      $this->userpointearner_metadata = serialize($this->userpointearner_metadata);
    }
    
    parent::_insert();

  }

  protected function _update() {
    
    if(is_array($this->userpointearner_metadata)) {
      $this->userpointearner_metadata = serialize($this->userpointearner_metadata);
    }
    
    parent::_update();
    
  }


  // General

  public function isOwner(Core_Model_Item_Abstract $owner) {

    if( $this->isSelf($owner) ) {
      return true;
    }
    
    return false;
    
  }

  public function getPoints() {
    
    return $this->userpointearner_cost;
    
  }

  public function canView($viewer) {

    if(empty($this->userpointearner_levels)) {
      return true;
    }
    
    $userpointearner_levels = explode(",", $this->userpointearner_levels);

    // public 
    if(!$viewer->getIdentity() && !in_array(5, $userpointearner_levels)) {
      return false;      
    }
    
    if(!in_array($viewer->level_id, $userpointearner_levels)) {
      return false;
    }
    
    return true;
    
  }


  public function getMetadata() {
    return unserialize($this->userpointearner_metadata);
  }


  public function getItemType() {
    
    $type = Engine_Api::_()->getDbTable('earnertype','activityrewards')->fetchRow( array("userpointearnertype_type = ?" => $this->userpointearner_type ) );

    return $type ? $type->userpointearnertype_name : null;
    
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array('item_id' => $this->userpointearner_id, 'item_title'  => $this->normalizeTitle($this->userpointearner_title)), $params);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'activityrewards_offer_view', true);
  }
  
  // @tbd
  public function normalizeTitle($title) {
    
    $title = str_replace(" ","-", $title);
    $title = preg_replace('/[^a-zA-Z0-9\_\-]/','',$title);
    
    return $title;
    
  }

  public function getTitle()
  {
    return $this->userpointearner_title;
  }

  public function getDescription()
  {
    $tmpBody = strip_tags($this->userpointearner_body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }
  
  function getIdentity() {
    return $this->userpointearner_id;
  }

  public function setPhoto($photo)
  {


    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Activityrewards_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'userpointearner',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();
    
    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');


    $this->userpointearner_photo = $iMain->getIdentity();
    $this->save();

    return $this;
  }
  
  public function removePhoto() {
    $this->userpointearner_photo = '';
    $this->save();
  }

  public function getPhoto()
  {
    return $this->userpointearner_photo;
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->userpointearner_photo) )
    {
      return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->userpointearner_photo, $type);
    if( !$file )
    {
      return null;
    }

    return $file->map();
  }
  








  public function transact($user, $transaction_params = array() ) {
      
    // @tbd check file exists, it's not checked in SE
    $earnerType = Engine_Api::_()->getDbTable('earnertype','activityrewards')->fetchRow("userpointearnertype_type = {$this->userpointearner_type}");
    if($earnerType && !empty($earnerType->model)) {
      
      $classname = $earnerType->model;
      
    } else {

      $classname = 'Activityrewards_Model_Earner_' . ucwords($this->userpointearner_name);
      
    }
    
    try {
      
      $handler = new $classname();
      
    } catch (Exception $ex) {
      
    }
    
    if(!$handler) {
      $this->_err_msg = 100016028;
      return false;
    }
    
    
    
    /** BEFORE TRANSACTION **/
    
    
    // check limits, in-stock, etc
    // similar to core::updatePoints
    if($this->userpointearner_max_acts > 0) {

      $table = Engine_Api::_()->getDbTable('counters', 'activitypoints');
      $db = $table->getAdapter();
      $tableName = $table->info("name");

      $now = time();
      $rolloverornew = false;
      
      $type = 'upearner_' . $this->userpointearner_id;
      $action_data = Engine_Api::_()->getDbTable('actionpoints', 'activitypoints')->get($type, $user->getIdentity());

      // user still has no entry
      if(!$action_data) {

        $rolloverornew = true;
        $action_data = Engine_Api::_()->getDbTable('actionpoints', 'activitypoints')->get($type);
        
      } else {
        
        // there's an entry, check limits. "cost" may have been changed
        if($action_data['userpointcounters_amount'] >= ($this->userpointearner_cost * $this->userpointearner_max_acts)) {
          
          // got limit, check if it will be reset sometime
          if($this->userpointearner_rolloverperiod == 0 ) {
            
            // all time cap
            $this->_err_msg = "You have reached maximum uses.";
            return false;
            
          } else {

            if(($now - intval($action_data['userpointcounters_lastrollover'])) >= intval($action_data['action_rolloverperiod'])) {
              
              // time for rollover

              $rolloverornew = true;
              
            } else {
              
              // rollover not reached
              
              $diff = intval($action_data['action_rolloverperiod']) - ($now - intval($action_data['userpointcounters_lastrollover']));
              
              $days = intval( $diff / 86400 );
              $hours = intval( ($diff % 86400) / 3600 );
              $minutes = intval( (($diff % 86400) % 3600) / 60 );
              $seconds = intval( (($diff % 86400) % 3600) % 60 );

              if($days > 0) {
                $msg = sprintf(Zend_Registry::get('Zend_Translate')->_("ACTIVITYREWARDS_MAXUSE_DAYHOURMINUTE"), $days, $hours, $minutes, $seconds);
              } elseif($hours > 0) {
                $msg = sprintf(Zend_Registry::get('Zend_Translate')->_("ACTIVITYREWARDS_MAXUSE_HOURMINUTESECOND"), $hours, $minutes, $seconds);
              } elseif($minutes > 0) {
                $msg = sprintf(Zend_Registry::get('Zend_Translate')->_("ACTIVITYREWARDS_MAXUSE_MINUTESECOND"), $minutes, $seconds);
              } else {
                $msg = sprintf(Zend_Registry::get('Zend_Translate')->_("ACTIVITYREWARDS_MAXUSE_SECOND"), $seconds);
              }
              
              $this->_err_msg = $msg;
              
              return false;
              
            }
            
          }
        
        }
        
      }
      
    }
    
    
    // Check in-stock
    if(($this->userpointearner_instock_track == 1) && (!($this->userpointearner_instock > 0))) {
      $this->_err_msg = "This item is out of stock.";
      return false;
    }
    
    
    
    
    $metadata = !empty( $this->userpointearner_metadata ) ? unserialize($this->userpointearner_metadata) : array();
    $params = array( $this, $user, $metadata, $transaction_params );

    if( !$handler->onTransactionStart($params) ) {

      if( isset($params['redirect']) ) {
        
        return array('redirect' => $params['redirect']);

      }
      
      $this->_err_msg = Semods_Utils::g($params,'err_msg',100016027);
      return false;
    }




    /** TRANSACTION **/

    // Instantly completed
    if( $this->userpointearner_transact_state == 0) {
      Engine_Api::_()->getApi('core','activitypoints')->addPoints($user->getIdentity(), $this->userpointearner_cost );        
    }
    


    /** AFTER TRANSACTION SUCCESS **/

    if( !$handler->onTransactionSuccess($params) ) {

      $this->_err_msg = $params['err_msg'];

      // rollback

      return false;
    }

    $this->_transaction_message = isset($params['transaction_message']) ? $params['transaction_message'] : '';

    if( Semods_Utils::g($params, 'transaction_record', 1) != 0) {

      $transaction_text = Semods_Utils::g($params,'transaction_text', '');
      
      $transaction_id = Engine_Api::_()->getDbTable('transactions','activitypoints')-> add(
                                                    $user->getIdentity(),
                                                    $this->userpointearner_type,
                                                    1,  // cat
                                                    $this->userpointearner_transact_state,
                                                    $transaction_text,
                                                    $this->userpointearner_cost
                                                   );
      
      // 3 => metadata
      $params[3]['transaction_id'] = $transaction_id;

    }


    /** UPDATE ENGAGEMENTS COUNTER **/
    
    // not atomic
    $this->userpointearner_engagements++;

    // in-stock
    if($this->userpointearner_instock_track == 1) {
      $this->userpointearner_instock--;
    }

    $this->save();


    if( !$handler->onTransactionFinished($params) ) {
      $this->_err_msg = $params['err_msg'];
  
      // rollback 
  
      return false;
    }

    

    // limits ("max acts")
    if($this->userpointearner_max_acts > 0) {

      if($rolloverornew) {
        $sql = "INSERT INTO `{$tableName}` (
                  userpointcounters_user_id,
                  userpointcounters_action_id,
                  userpointcounters_lastrollover,
                  userpointcounters_amount, 
                  userpointcounters_cumulative )
                VALUES (
                  ?,
                  ?,
                  ?,
                  ?,
                  ? )
                ON DUPLICATE KEY UPDATE
                  userpointcounters_lastrollover = ?,
                  userpointcounters_amount = ?,
                  userpointcounters_cumulative = userpointcounters_cumulative + ?";
  
        $values = array('userpointcounters_user_id'       => $user->getIdentity(),
                        'userpointcounters_action_id'     => $action_data['action_id'],
                        'userpointcounters_lastrollover'  => $now,
                        'userpointcounters_amount'        => $this->userpointearner_cost,
                        'userpointcounters_cumulative'    => $this->userpointearner_cost,
                        );
        $values2 = array(
                        'userpointcounters_lastrollover'  => $now,
                        'userpointcounters_amount'        => $this->userpointearner_cost,
                        'userpointcounters_cumulative'    => $this->userpointearner_cost,
                        );

        $res = $db->query($sql, array_merge(array_values($values), array_values($values2)));
        
      } else {

        $table->update(array(
          'userpointcounters_amount' => new Zend_Db_Expr('userpointcounters_amount + ' . $table->getAdapter()->quote($this->userpointearner_cost)),
          'userpointcounters_cumulative' => new Zend_Db_Expr('userpointcounters_cumulative + ' . $table->getAdapter()->quote($this->userpointearner_cost)),
        ), array(
          'userpointcounters_user_id = ?' => $user->getIdentity(),
          'userpointcounters_action_id = ?' => $action_data['action_id'],
        ));
        
      }
      
      
    }

    // Redirection after transaction completed
    if( isset($params['redirect']) ) {
      return array('redirect' => $params['redirect']);
    }

    return array( 'transaction_message'  => $this->_transaction_message );
  }






  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
}
<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_IndexController extends Core_Controller_Action_Standard {

  public function loadInstagramGalleryAction() {
  
    $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sesbasic');
    $instagramApi = $instagramTable->getApi();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $extra_params = "&count=".$settings->getSetting('sesadvancedactivity.photoshowcount', 8);
    
    //Get Albums
    if(!empty($_POST['after'])) {
      $extra_params = $extra_params.'&max_id='.$_POST['after'];
    }
    
    $this->view->typeSeelect = $_GET['type'];
    $this->view->is_ajax = isset($_POST['is_ajax']) ? true : false;
    $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sesbasic');
    $instagram = $instagramTable->getApi();
    $inData =  $_SESSION['sesbasic_instagram'];
    $this->view->access_token = $access_token = $instagram->getAccessToken();
		$json_link = "https://api.instagram.com/v1/users/{$inData['in_id']}/media/recent/?access_token={$access_token}". $extra_params;
		$result = json_decode(file_get_contents($json_link),true);
		$this->view->gallerydata = $result;
    $this->renderScript('index/load-instagram-photo.tpl');
  }
  
  public function instagramLogoutAction() {
  
    unset($_SESSION['sesbasic_instagram']['inphoto_url'] );
    unset($_SESSION['sesbasic_instagram']['in_id'] );
    unset($_SESSION['sesbasic_instagram']['in_name'] );
    unset($_SESSION['sesbasic_instagram']['in_username'] );
    unset($_SESSION['instagram_lock']);
    unset($_SESSION['instagram_uid']);
    return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
  }
  
  public function hashtagAction() {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;  
  }
  
  public function updateusereventAction() {
  
    $event_id = $this->_getParam('event_id', null);
    $user_id = $this->_getParam('user_id', null);
    Engine_Api::_()->getDbtable('eventmessages', 'sesadvancedactivity')->update(array('userclose' => 1), array('event_id = ?' => $event_id, 'user_id = ?' => $user_id));
    
  
  }
  public function onthisdayAction(){
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity_enableonthisday', 1)){
			 return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		}  
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;  
  }
  public function postAction()
  {
    $this->view->error = 'An error occured. Please try again after some time.';
    $this->view->userphotoalign = $this->_getParam('userphotoalign', 'left');
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if( $subject_guid ) {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if( null === $subject ) {
      $subject = $viewer;
    }

    // Make form
    $form = $this->view->form = new Sesadvancedactivity_Form_Post();

    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
      return $this->_helper->requireAuth()->forward();
    }

    // Check if post
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }
    
    if(empty($_GET['is_ajax'])){
      // Check token
      if( !($token = $this->_getParam('token')) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
        return;
      }
      $session = new Zend_Session_Namespace('ActivityFormToken');
      if( $token != $session->token ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid token, please try again');
        return;
      }
      $session->unsetAll();
    }
    
    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$postData['body'];
    Engine_Api::_()->getApi('settings', 'core')->setSetting($viewer->getIdentity().'.activity.user.setting',$postData['privacy']);
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;
    
    if( !$form->isValid($postData) ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check one more thing
    if( $form->body->getValue() === '' && $form->getValue('attachment_type') === '' ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    
    // set up action variable
    $action = null;
    $scheduled_post = !empty($_POST['scheduled_post']) ? $_POST['scheduled_post'] : false;
    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    // If we're here, we're done
    $this->view->status = true;
    try {
      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);
      // Try attachment getting stuff
      $attachment = null;
      $params = array();
      $embedpost = false;
      $attachmentData = $this->getRequest()->getParam('attachment'); 
      
      //Facebook embed work
      if($attachmentData['type'] == 'sesadvancedactivityfacebookpostembed') {
        $attachment = null;
        if($body) {
          $body = $body . '<br />' . $attachmentData['uri'];
        } else {
          $body = $attachmentData['uri'];
        }
        $params['type'] = 'facebookpostembed';
        $embedpost = true;
        $attachmentData = null;
      }

      if(!empty($_POST['fancyalbumuploadfileids'])){
        if($attachmentData['type'] != 'buysell')
        $attachmentData['type'] = 'photo';
        $arrachmentPhotoIds = $_POST['fancyalbumuploadfileids'];
        $attachmentIds = explode(' ',$arrachmentPhotoIds);
      }
      if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
        $type = $attachmentData['type'];
        $config = null;
        
        foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
          if( !empty($data['composer'][$type]) ) {
            $config = $data['composer'][$type];
          }
        }
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          $config = null;
        }
        if( $config ) {
          $attachmentData['actionBody'] = $body;          
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach'.ucfirst($type);
          $execute = false;
          if(empty($attachmentIds) || ($attachmentData['type'] == 'buysell' && !empty($attachmentIds))){
           if($config['plugin'] == 'Sesadvancedactivity_Plugin_FileuploadComposer')
            $fileUpload = $_FILES['fileupload'];
           else
            $fileUpload = '';
           $attachment = $attachmentAttachData = $plugin->$method($attachmentData,$fileUpload,$_POST);
            $execute = true;
          }
          if(!$execute || $attachmentData['type'] == 'buysell'){
            $attachmentData['actionBody'] = '';
            if($attachmentData['type'] == 'buysell'){
             if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum'))
              $plugin =  Engine_Api::_()->loadClass('Album_Plugin_Composer');
             else
              $plugin =  Engine_Api::_()->loadClass('Sesalbum_Plugin_Composer');
             $method = 'onAttachPhoto';
            }
            foreach($attachmentIds as $attachmentId){
              if(!$attachmentId)
                continue;
               $attachmentData['photo_id'] = $attachmentId;
              $attachment = $plugin->$method($attachmentData);
             }  
          }
        }
      }
      // Is double encoded because of design mode
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      $videoProcess = 0;
      // Special case: status
      if( !$attachment && $viewer->isSelf($subject) ) {
        if( $body != '' && !$embedpost) {
          $viewer->status = $body;
          $viewer->status_date = date('Y-m-d H:i:s');
          $viewer->save();
          $viewer->status()->setStatus($body);
        }
        
        $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->addActivity($viewer, $subject, 'status', $body ,$params, $postData);
      } else { // General post
        $type = 'post';
        if( $viewer->isSelf($subject) ) {
          $type = 'post_self';
        }
        if($attachment){
          if($attachmentData['type'] == 'buysell')
            $type = 'post_self_buysell';
          else if($attachment->getType() == 'album_photo' || $attachment->getType()  == 'photo'){
           if($viewer->isSelf($subject))
              $type = 'post_self_photo';
           else
              $type = 'post_photo';
          }
          else if($attachment->getType() == 'video' || $attachment->getType()  == 'sesvideo_video'){
           if($viewer->isSelf($subject))
              $type = 'post_self_video';
           else
              $type = 'post_video';
           if($attachment->status != 1)
             $videoProcess = 1;
          }else if($attachment->getType() == 'music_playlist' || $attachment->getType()  == 'sesmusic_albumsong')
            if($viewer->isSelf($subject))
              $type = 'post_self_music';
           else
              $type = 'post_music';
          else if($attachment->getType() == 'sesadvancedactivity_file')
            $type = 'post_self_file';
        }
        // Add notification for <del>owner</del> user
        $subjectOwner = $subject->getOwner();
        if( !$viewer->isSelf($subject) &&
            $subject instanceof User_Model_User ) {
          $notificationType = 'post_'.$subject->getType();
          Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
            'url1' => $subject->getHref()
          ));
        }
        // Add activity
        $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->addActivity($viewer, $subject, $type, $body, array(),$postData) ;       
        if($action && !empty($attachmentAttachData) && $attachmentData['type'] == 'buysell'){
          $attachmentAttachData->action_id = $action->getIdentity();
          $attachmentAttachData->save();
        }
        // Try to attach if necessary
        if( $action && $attachment) {
          if(empty($attachmentIds) && $attachmentData['type'] != 'buysell')
            Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->attachActivity($action, $attachment);
          else{
            foreach($attachmentIds as $attachmentId){
              if(!$attachmentId)
                continue;
             //make item of photo object
             $photo = Engine_Api::_()->getItem('album_photo',$attachmentId);
              Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
            }
          }
        }
      }
      
      //tag location in post
      if(!empty($_POST['tag_location']) && !empty($_POST['activitylng']) && !empty($_POST['activitylat'])){
         //check location
         $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
         $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type,venue) VALUES ("' . $action->getIdentity() . '", "' . $_POST['activitylat'] . '","' . $_POST['activitylng'] . '","activity_action","'.$_POST['tag_location'].'")	ON DUPLICATE KEY UPDATE	 lat = "' . $_POST['activitylat'] . '" , lng = "' . $_POST['activitylng'] . '",venue="'.$_POST['tag_location'].'"');     
      }
      //tag friend in post
      if(!empty($_POST['tag_friends'])){
        if(empty($dbGetInsert))
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $tagUsers = array_unique(explode(",", $_POST['tag_friends']));
        if(count($tagUsers)) {
          $postLink = '<a href="' . $action->getHref() . '">' . "post" . '</a>';
          foreach($tagUsers  as $tagUser) {
            $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_tagusers` (`user_id`, `action_id`) VALUES ("'.$tagUser.'", "'.$action->getIdentity().'")');
            
            $item = Engine_Api::_()->getItem('user', $tagUser);
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedactivity_tagged_people', array("postLink" => $postLink));
          }
        }
      }

      //Tagging People by status box
      preg_match_all('/(^|\s)(@\w+)/', $_POST['body'], $result);
      $postLink = '<a href="' . $action->getHref() . '">' . "post" . '</a>';
      foreach($result[2] as $value) {
        $user_id = str_replace('@_user_','',$value);
        $item = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedactivity_tagged_people', array("postLink" => $postLink));
      }
      //Tagging People by status box

      //insert reaction
      if(!empty($_POST['reaction_id']))
        $action->reaction_id = $_POST['reaction_id'];
      $action->save();
      // Preprocess attachment parameters
      $publishMessage = html_entity_decode($form->getValue('body'));
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if( $attachment && $attachment->getHref()) {
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if( empty($publishName) ) {
          $publishName = ucwords($attachment->getShortType());
        }
        if( ($tmpPicUrl = $attachment->getPhotoUrl()) ) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if( $publishPicUrl &&
            preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST)) ) {
          $publishPicUrl = null;
        }
      } else {
          $publishUrl = !$action ? null : $action->getHref();
      }
      // Check to ensure proto/host
      if( $publishUrl &&
          false === stripos($publishUrl, 'http://') &&
          false === stripos($publishUrl, 'https://') ) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if( $publishPicUrl &&
          false === stripos($publishPicUrl, 'http://') &&
          false === stripos($publishPicUrl, 'https://') ) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if( $publishName ) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
            . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }
      
      
      
      // Publish to facebook, if checked & enabled
      if( $this->_getParam('post_to_facebook', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookApi = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();
          
          if( $fb_uid &&
              $fb_uid->facebook_uid &&
              $facebookApi &&
              $facebookApi->getUser() &&
              $facebookApi->getUser() == $fb_uid->facebook_uid ) {
            $fb_data = array(
              'message' => $publishMessage,
            );
            if( $publishUrl ) {
              $fb_data['link'] = $publishUrl;
            }
            
            if( $publishName ) {
              $fb_data['name'] = $publishName;
            }
            if( $publishDesc ) {
              $fb_data['description'] = $publishDesc;
            }
            if( $publishPicUrl ) {
              $fb_data['picture'] = $publishPicUrl;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
          }
        } catch( Exception $e ) {
          
          // Silence
        }
      } // end Facebook

      // Publish to twitter, if checked & enabled
      if( $this->_getParam('post_to_twitter', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if( $twitterTable->isConnected() ) {
            // @todo truncation?
            // @todo attachment
            $twitter = $twitterTable->getApi();
            //check for attachment
            if ( ($attachment && $attachment->getHref()) || strlen(html_entity_decode($_POST['body'])) > 140) {
                  if (!empty($attachment)) {
                      $attachmentUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $attachment->getHref();
                      $attachmentUrlLen = strlen($attachmentUrl);
                  }else{
                   $attachmentUrlLen = $attachmentUrl = '';
                  }
                  $publishMessage = substr(html_entity_decode($_POST['body']), 0, (140 - ($attachmentUrlLen + 1))) . ' ' . $attachmentUrl;
              } else
                  $publishMessage = html_entity_decode($_POST['body']);
            
               $twitter->statuses->update($publishMessage);
            }
        } catch( Exception $e ) {
          // Silence
        }
      }
      
       // Publish to linkedin, if checked & enabled
      if( $this->_getParam('post_to_linkedin', false) &&
           Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.linkedin.enable',false) ) {
        try {
          $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sesadvancedactivity');
          if (!empty($_SESSION[linkedin_access])) {
            // @todo attachment
            $linkedin = $linkedinTable->getApi();
            if ($attachment){
              if ($publishPicUrl)
                $linkedin_data['submitted-image-url'] = $publishPicUrl;
              if ($publishName)
                $linkedin_data['title'] = $publishName;
              if ($publishDesc)
                $linkedin_data['description'] = $publishDesc;
              if ($publishUrl)
                $linkedin_data['submitted-url'] = $publishUrl;
            }
            $linkedin_data['comment'] = strip_tags($publishMessage);
            $linkedin->setTokenAccess($_SESSION['linkedin_access']);           
            $sharepostLinkedin = $linkedin->share('new', $linkedin_data); 
          }
        } catch( Exception $e ) {
          throw $e;
          // Silence
        }
      }
      
      // Publish to janrain
            if ('publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();
                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $this->_HOST_NAME . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;
                } catch (Exception $e) {
                    // Silence
                }
            }
        
      $hashtagValue = '';
      if(isset($_GET['hashtag'])){
        $hashtagValue = $_GET['hashtag'];  
      }
      $existsHashTag = false;
      // extrack #  tag value from post
      if($action){
         preg_match_all("/(#\w+)/u", $action->body, $matches);
         if(count($matches)){
          if(empty($dbGetInsert))
            $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
          $hashtags = array_unique($matches[0]);
          foreach($hashtags  as $hashTag){
           if('#'.$hashtagValue == $hashTag)
             $existsHashTag = true;
           $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_hashtags` (`action_id`, `title`) VALUES ("'.$action->getIdentity().'", "'.str_replace('#','',$hashTag).'")');  
          }  
         }
      }
      //check for target post
      if( $this->_getParam('post_to_targetpost', false)) {
        $targetpost['location_send'] = $_POST['targetpost']['location_send'];        
        $targetpost['gender_send'] =  $_POST['targetpost']['gender_send'];
        $targetpost['age_min_send'] =  $_POST['targetpost']['age_min_send'];
        $targetpost['age_max_send'] = $_POST['targetpost']['age_max_send'];
        $targetpost['action_id'] = $action->getIdentity(); 
        $targetpost['country_name'] = '';
        $targetpost['city_name'] = '';
        if($targetpost['location_send'] == 'country'){
          $targetpost['country_name'] = $_POST['targetpost']['country_name'];
          $targetpost['lat'] =  $_POST['targetpost']['targetpostlat'];
          $targetpost['lng'] = $_POST['targetpost']['targetpostlng'];
          $targetpost['location_country'] = $_POST['targetpost']['location_country'];
        }else if($targetpost['location_send'] == 'city'){
          $targetpost['lat'] =  $_POST['targetpost']['targetpostlatcity'];
          $targetpost['lng'] = $_POST['targetpost']['targetpostlngcity'];  
          $targetpost['location_city'] = $_POST['targetpost']['location_city'];  
          $targetpost['city_name'] = $_POST['targetpost']['city_name'];              
        }else{
          $targetpost['lat'] =  '';
          $targetpost['lng'] = '';
          $targetpost['location_country']  = '';
          $targetpost['location_city']  = '';
        }
        if(empty($dbGetInsert))
           $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_targetpost`(`action_id`, `location_send`, `location_city`, `location_country`, `gender_send`, `age_min_send`, `age_max_send`, `lat`, `lng`,`country_name`,`city_name`) VALUES ("'.$targetpost['action_id'].'","'.$targetpost['location_send'].'","'.$targetpost['location_city'].'","'.$targetpost['location_country'].'","'.$targetpost['gender_send'].'","'.$targetpost['age_min_send'].'","'.$targetpost['age_max_send'].'","'.$targetpost['lat'].'","'.$targetpost['lng'].'","'.$targetpost['country_name'].'","'.$targetpost['city_name'].'")'); 
      }
      $db->commit();
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Success');
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
      $this->view->status = false;
      if(!empty($_GET['is_ajax']))
        $this->view->error = 'An error occured. Please try again after some time.';
      else
        throw $e;
    }
    
    // Check if action was created
    $post_fail = "";
    if( !$action ){
      $post_fail = "?pf=1";
    }
    if($action && $scheduled_post){
      $post_fail = "?sp=1";
    }
    
    // Redirect if in normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() && empty($_GET['is_ajax'])) {
      $return_url = $form->getValue('return_url', false);
      if( $return_url ) {
        return $this->_helper->redirector->gotoUrl($return_url.$post_fail, array('prependBase' => false));
      }
    }else if(!empty($_GET['is_ajax'])){
      if($action){
       $feed = $this->view->activity($action,array('ulInclude'=>true, 'userphotoalign' => $this->view->userphotoalign));
       $last_id = $action->getIdentity();
      }else{
        $feed = $last_id = '';
      }
      
      if($videoProcess){
        $action->delete();
      }
      
      echo json_encode(array('videoProcess'=>$videoProcess,'status'=> $this->view->status,'last_id'=>$last_id,'existsHashTag'=>$existsHashTag,'feed'=>$feed,error=>$this->view->error,'scheduled_post'=>$scheduled_post,'userhref'=>$viewer->getHref(),'scheduled_post_time'=>(!empty($action->schedule_time)) ? $action->schedule_time : ''),JSON_HEX_QUOT | JSON_HEX_TAG);die;
    }
  }

  /**
   * Handles HTTP request to get an activity feed item's likes and returns a 
   * Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/viewlike
   * 
   * @return void
   */
  public function viewlikeAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);


    // Redirect if not json context
    if( null === $this->_getParam('format', null) ) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_getParam('format', null) ) {
      $this->view->body = $this->view->activity($action, array('viewAllLikes' => true, 'noList' => $this->_getParam('nolist', false)));
    }
  }

  /**
   * Handles HTTP request to like an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/like
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function likeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();
        
    // Start transaction
    $db = Engine_Api::_()->getDbtable('likes', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      
      // Action
      if( !$comment_id ) {

        // Check authorization
        if($coreVersion <= '4.8.5') {
          // Check authorization
          if( $action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        } else {
          if( $action && !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        }

        $action->likes()->addLike($viewer);

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

          Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
            'label' => 'post'
          ));
        }

      }
      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);
        
        if(version_compare($coreVersion, '4.8.5') < 0){
          // Check authorization
          if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        } else {
          // Check authorization
          if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        }

        $comment->likes()->addLike($viewer);

        // @todo make sure notifications work right
        if( $comment->poster_id != $viewer->getIdentity() ) {
          Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')
              ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                'label' => 'comment'
              ));
        }

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

        }
      }
      
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);

    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $method = 'update';
      $this->view->body = $this->view->activity($action, array('noList' => true), $method);
    }
  }

  /**
   * Handles HTTP request to remove a like from an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/unlike
   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function unlikeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Start transaction
    $db = Engine_Api::_()->getDbtable('likes', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      
      // Action
      if( !$comment_id ) {
      
        if(version_compare($coreVersion, '4.8.5') < 0){
          // Check authorization
          if( !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to unlike this item');
          }
        } else {
          // Check authorization
          if( !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to unlike this item');
          }
        }

        $action->likes()->removeLike($viewer);
      }

      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        if(version_compare($coreVersion, '4.8.5') < 0){
          // Check authorization
          if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        } else {
          // Check authorization
          if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment') ) {
            throw new Engine_Exception('This user is not allowed to like this item');
          }
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $method = 'update';
      $this->view->body = $this->view->activity($action, array('noList' => true), $method);
    }
  }

  /**
   * Handles HTTP request to get an activity feed item's comments and returns 
   * a Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/viewcomment
   *
   * @return void
   */
  public function viewcommentAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer    = Engine_Api::_()->user()->getViewer();

    $action    = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
    $form      = $this->view->form = new Sesadvancedactivity_Form_Comment();
    $form->setActionIdentity($action_id);
    

    // Redirect if not json context
    if (null===$this->_getParam('format', null))
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $this->view->body = $this->view->activity($action, array('viewAllComments' => true, 'noList' => $this->_getParam('nolist', false)));
    }
  }

  /**
   * Handles HTTP POST request to comment on an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/comment
   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function commentAction()         
  {     
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Sesadvancedactivity_Form_Comment();
    
    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }

    // Not valid
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Start transaction
    $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
      $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      if (!$action) {
        $this->view->status = false;
        $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
        return;
      }
      $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
      $body = $form->getValue('body');

      if(version_compare($coreVersion, '4.8.5') < 0){
        // Check authorization
        if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
          throw new Engine_Exception('This user is not allowed to comment on this item.');
      } else {
        // Check authorization
        if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment'))
          throw new Engine_Exception('This user is not allowed to comment on this item.');
      }

      // Add the comment
      $action->comments()->addComment($viewer, $body);

      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity');

      // Add notification for owner of activity (if user and not viewer)
      if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
          'label' => 'post'
        ));
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->comments()->getAllCommentsUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->likes()->getAllLikesUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Assign message for json
    $this->view->status = true;
    $this->view->message = 'Comment posted';

    // Redirect if not json
    if( null === $this->_getParam('format', null) )
    {
      $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $method = 'update';
      $show_all_comments = $this->_getParam('show_all_comments');
      //$showAllComments = $this->_getParam('show_all_comments', false);           
      $this->view->body = $this->view->activity($action, array('noList' => true), $method, $show_all_comments);
    }
  }

  /**
   * Handles HTTP POST request to share an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/share
   *
   * @return void
   */
  public function shareAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    $type = $this->_getParam('type');
    $id = $this->_getParam('id');    

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    $this->view->form = $form = new Sesadvancedactivity_Form_Share();

    if( !$attachment ) {
      // tell smoothbox to close
      $this->view->status  = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }
    // hide facebook and twitter option if not logged in
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if( !$facebookTable->isConnected() ) {
      $form->removeElement('post_to_facebook');
    }
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if( !$twitterTable->isConnected() ) {
      $form->removeElement('post_to_twitter');
    }
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    try {
      // Get body
      $body = $form->getValue('body');
      // Set Params for Attachment
      $params = array(
          'type' => '<a href="'.$attachment->getHref().'">'.$attachment->getMediaType().'</a>',          
          );      
      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity');
      //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
      if($type == 'sesadvancedactivity_event'){
       $typeShare = 'sesadvancedactivity_event_share'; 
      }else
        $typeShare = 'share';
      $action = $api->addActivity($viewer, $attachment->getOwner(), $typeShare, $body, $params);      
      if( $action ) { 
        if($type == 'sesadvancedactivity_event'){
          $params = array(
          'type' => '<a href="'.$action->getHref().'">post</a>',          
          ); 
          $action->params = $params;
          $action->save();  
        }
        $api->attachActivity($action, $attachment);
      }
      $db->commit();
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity');
      // Add notification for owner of activity (if user and not viewer)
      if( $action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
          'label' => $attachment->getMediaType(),
        ));
      }
      // Preprocess attachment parameters
      $publishMessage = html_entity_decode($form->getValue('body'));
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if( $attachment ) {        
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if( empty($publishName) ) {
          $publishName = ucwords($attachment->getShortType());
        }
        if( ($tmpPicUrl = $attachment->getPhotoUrl()) ) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if( $publishPicUrl &&
            preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST)) ) {
          $publishPicUrl = null;
        }
      } else {
        $publishUrl = $action->getHref();
      }
      // Check to ensure proto/host
      if( $publishUrl &&
          false === stripos($publishUrl, 'http://') &&
          false === stripos($publishUrl, 'https://') ) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if( $publishPicUrl &&
          false === stripos($publishPicUrl, 'http://') &&
          false === stripos($publishPicUrl, 'https://') ) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if( $publishName ) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
            . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }
      // Publish to facebook, if checked & enabled
      if( $this->_getParam('post_to_facebook', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
        try {

          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebookApi = $facebook = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

          if( $fb_uid &&
              $fb_uid->facebook_uid &&
              $facebookApi &&
              $facebookApi->getUser() &&
              $facebookApi->getUser() == $fb_uid->facebook_uid ) {
            $fb_data = array(
              'message' => $publishMessage,
            );
            if( $publishUrl ) {
              $fb_data['link'] = $publishUrl;
            }
            if( $publishName ) {
              $fb_data['name'] = $publishName;
            }
            if( $publishDesc ) {
              $fb_data['description'] = $publishDesc;
            }
            if( $publishPicUrl ) {
              $fb_data['picture'] = $publishPicUrl;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
          }
        } catch( Exception $e ) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if( $this->_getParam('post_to_twitter', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if( $twitterTable->isConnected() ) {

            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();

            // Check stuff
            if( $url && false === stripos($url, 'http://') ) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if( $picUrl && false === stripos($picUrl, 'http://') ) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if( strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140 ) {
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
              if( $picUrl ) {
                $message .= ' - ' . $picUrl;
              }
            } else if( strlen($message) + strlen($title) + strlen($url) + 6 <= 140 ) {
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
            } else {
              if( strlen($title) > 24 ) {
                $title = Engine_String::substr($title, 0, 21) . '...';
              }
              // Sigh truncate I guess
              if( strlen($message) + strlen($title) + strlen($url) + 9 > 140 ) {
                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
              }
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
            }
            
            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          }
        } catch( Exception $e ) {
          // Silence
        }
      }      
      // Publish to janrain
      if( //$this->_getParam('post_to_janrain', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable ) {
        try {
          $session = new Zend_Session_Namespace('JanrainActivity');
          $session->unsetAll();
          $session->message = $publishMessage;
          $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
          $session->name = $publishName;
          $session->desc = $publishDesc;
          $session->picture = $publishPicUrl;
        } catch( Exception $e ) {
          // Silence
        }
      }      
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }

    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');

    // Redirect if in normal context
     $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> false,
        'messages' => array('')
      ));
  }
 
  /**
   * Handles HTTP POST request to delete a comment or an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/delete
   *
   * @return void
   */
  function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    
    // Identify if it's an action_id or comment_id being deleted
    $this->view->comment_id = $comment_id = (int) $this->_getParam('comment_id', null);
    $this->view->action_id  = $action_id  = (int) $this->_getParam('action_id', null);

    $action       = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
    if (!$action){
      // tell smoothbox to close
      $this->view->status  = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot delete this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }

    // Send to view script if not POST
    if (!$this->getRequest()->isPost())
      return;

    // Both the author and the person being written about get to delete the action_id
    if (!$comment_id && (
        $activity_moderate ||
        ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
        ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id)))   // commenter
    {
      // Delete action item and all comments/likes
      $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
      $db->beginTransaction();
      try {
        $action->deleteItem();
        $db->commit();

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');
        $this->view->smoothboxClose = true;
        echo true;die;
        //return $this->render('deletedItem');
      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }

    } elseif ($comment_id) {
        $comment = $action->comments()->getComment($comment_id);
        // allow delete if profile/entry owner
        $db = Engine_Api::_()->getDbtable('comments', 'sesadvancedactivity')->getAdapter();
        $db->beginTransaction();
        if ($activity_moderate ||
           ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
           ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id))
        {
          try {
            $action->comments()->removeComment($comment_id);
            $db->commit();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted');
            $commentModuleEnableSes = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment');
            if($comment->parent_id && $commentModuleEnableSes){
              $parentCommentType = 'core_comment';
        
              if($action->getType() == 'activity_action'){
                $commentType = $action->likes(true);
                if($commentType->getType() == 'activity_action')
                  $parentCommentType = 'activity_comment';
              }
              $parentCommentId = $comment->parent_id;
              $parentComment = Engine_Api::_()->getItem($parentCommentType,$parentCommentId);
              $parentComment->reply_count = new Zend_Db_Expr('reply_count - 1');
              $parentComment->save();
             
            }
            if($commentModuleEnableSes){
             $this->view->commentCount = Engine_Api::_()->sesadvancedcomment()->commentCount($action);
             $this->view->action = $action;
            }
            return $this->render('deletedComment');
          } catch (Exception $e) {
            $db->rollback();
            $this->view->status = false;
          }
        } else {
          $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
          return $this->render('deletedComment');
        }
      
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_forward('requireauth', 'error', 'core');
    }

  }

  public function getLikesAction()
  {
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');

    if( !$action_id ||
        !$comment_id ||
        !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
        !($comment = $action->comments()->getComment($comment_id)) ) {
      $this->view->status = false;
      $this->view->body = '-';
      return;
    }

    $likes = $comment->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
      count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }
  public function suggestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      
      $usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

      
     $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();          
      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
      }
         
      if( $this->_getParam('includeSelf', false) ) {
        $data[] = array(
          'type' => 'user',
          'id' => $viewer->getIdentity(),
          'guid' => $viewer->getGuid(),
          'label' => $viewer->getTitle() . ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url' => $viewer->getHref(),
        );
      }

      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
        $select->limit($limit);
      }

     
      
      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend ) {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
  public function editPostAction(){
   try{
    $this->view->composerOptions = $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.composeroptions',array());
    $this->view->userphotoalign = $this->_getParam('userphotoalign', 'left');

    $action_id = $this->_getParam('action_id',false);
    $this->view->action = $action =  Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
    if(!$action)
      throw new Engine_Exception('Not Valid Action');
    //fetch networks
    $this->view->usernetworks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll(Engine_Api::_()->getDbtable('networks', 'network')->select()->order('engine4_network_networks.title ASC'));
    //fetch lists
      $this->view->userlists = Engine_Api::_()->getDbtable('lists', 'user')->fetchAll(Engine_Api::_()->getDbtable('lists', 'user')->select()->order('engine4_user_lists.title ASC'));
    if($action->type == 'post_self_buysell')
    {
       $this->view->item = $buysell =  $action->getBuySellItem();
       $this->view->locationBuySell = Engine_Api::_()->getDbTable('locations','sesbasic')->getLocationData('sesadvancedactivity_buysell',$buysell->getIdentity());;
    }
    //fetch target post data
    $this->view->targetPost = Engine_Api::_()->getDbTable('targetpost','sesadvancedactivity')->getTargetPost($action->getIdentity());
    //fetch location
    $this->view->location = Engine_Api::_()->getDbTable('locations','sesbasic')->getLocationData('activity_action',$action->getIdentity());
    $this->view->members = Engine_Api::_()->getDbTable('tagusers','sesadvancedactivity')->getActionMembers($action_id);;
   }catch(Exception $e){
      throw $e;
   }
   $mentionUserData = array();
    preg_match_all('/(^|\s)(@\w+)/', $action->body, $result);
    foreach($result[2] as $value){
        $user_id = str_replace('@_user_','',$value);
        $user = Engine_Api::_()->getItem('user',$user_id);
        if(!$user)
          continue;
        $mentionUserData[] = array(
          'type'  => 'user',
          'id'    => $user->getIdentity(),
          'name' => $user->getTitle(),
          'avatar' => $this->view->itemPhoto($user, 'thumb.icon'),
        );
    }
    $this->view->mentionData = $mentionUserData;
    $this->renderScript('_editPostComposer.tpl');  
  }
  public function editFeedPostAction()
  {
    $this->view->error = 'An error occured. Please try again after some time.';
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $viewer;
    $this->view->userphotoalign = $this->_getParam('userphotoalign', 'left');

    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
      return $this->_helper->requireAuth()->forward();
    }

    // Check if post
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }
    
    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$_POST['bodyText'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;
    
   
    // If we're here, we're done
    $this->view->status = true;
    try {
      $action_id = $this->_getParam('action_id',false);
      $this->view->action = $action =  Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      if(!$action)
        throw new Engine_Exception('Not Valid Action');
      // Get body
      //$body = $postData['bodyText'];
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);
      // Add activity
      $action->body = $body;
      $action->privacy = $_POST['privacy'];        
      //tag location in post
      if(!empty($_POST['tag_location']) && !empty($_POST['activitylng']) && !empty($_POST['activitylat'])){
         //check location
         $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
         $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type,venue) VALUES ("' . $action->getIdentity() . '", "' . $_POST['activitylat'] . '","' . $_POST['activitylng'] . '","activity_action","'.$_POST['tag_location'].'")	ON DUPLICATE KEY UPDATE	 lat = "' . $_POST['activitylat'] . '" , lng = "' . $_POST['activitylng'] . '",venue="'.$_POST['tag_location'].'"');     
      }else{
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query("DELETE FROM engine4_sesbasic_locations WHERE resource_id = '".$action->getIdentity()."' AND resource_type = 'activity_action'");
      }
      //tag friend in post
      if(!empty($_POST['tag_friends'])){
        $dbGetInsert->query("DELETE FROM engine4_sesadvancedactivity_tagusers WHERE action_id = '".$action->getIdentity()."'");
        if(empty($dbGetInsert))
           $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
         $tagUsers = array_unique(explode(",", $_POST['tag_friends']));
         if(count($tagUsers)){
            $postLink = '<a href="' . $action->getHref() . '">' . "post" . '</a>';
            foreach($tagUsers  as $tagUser){
               $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_tagusers` (`user_id`, `action_id`) VALUES ("'.$tagUser.'", "'.$action->getIdentity().'")');
              //Notification work
              $item = Engine_Api::_()->getItem('user', $tagUser);
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedactivity_tagged_people', array("postLink" => $postLink));
            }
         }
      }else{
        $dbGetInsert->query("DELETE FROM engine4_sesadvancedactivity_tagusers WHERE action_id = '".$action->getIdentity()."'");  
      }
      
      //Tagging People by status box
      preg_match_all('/(^|\s)(@\w+)/', $_POST['body'], $result);
      $postLink = '<a href="' . $action->getHref() . '">' . "post" . '</a>';
      foreach($result[2] as $value) {
        $user_id = str_replace('@_user_','',$value);
        $item = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedactivity_tagged_people', array("postLink" => $postLink));
      }
      //Tagging People by status box
      
      // extrack #  tag value from post
      if($action){
        $dbGetInsert->query("DELETE FROM engine4_sesadvancedactivity_hashtags WHERE action_id = '".$action->getIdentity()."'");  
         preg_match_all("/(#\w+)/u", $action->body, $matches);
         if(count($matches)){
          if(empty($dbGetInsert))
            $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
          $hashtags = array_unique($matches[0]);
          foreach($hashtags  as $hashTag){
           $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_hashtags` (`action_id`, `title`) VALUES ("'.$action->getIdentity().'", "'.str_replace('#','',$hashTag).'")');  
          }  
         }
      }
      if($action->type == 'post_self_buysell')
      {
        $buysell = $action->getBuySellItem();
        $buysell->title = $_POST['buysell-title'];
        $buysell->description = $_POST['buysell-description'];
        $buysell->price = $_POST['buysell-price'];
        $buysell->currency = $_POST['buysell-currency'];
        $buysell->save();
        if(!empty($_POST['buysell-location']) && !empty($_POST['activitybuyselllng']) && !empty($_POST['activitybuyselllat'])){
         $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
         $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type,venue) VALUES ("' . $buysell->getIdentity() . '", "' . $postData['activitybuyselllat'] . '","' . $postData['activitybuyselllng'] . '","sesadvancedactivity_buysell","'.$postData['buysell-location'].'")	ON DUPLICATE KEY UPDATE	 lat = "' . $postData['activitybuyselllat'] . '" , lng = "' . $postData['activitybuyselllng'] . '",venue="'.$postData['buysell-location'].'"');     
        }
      }
      if(empty($dbGetInsert))
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
      $dbGetInsert->query('DELETE FROM engine4_sesadvancedactivity_targetpost WHERE action_id ='.$action->getIdentity());
       //check for target post
      if( $this->_getParam('post_to_targetpost', false)) {
        $targetpost['location_send'] = $_POST['targetpost']['location_send'];        
        $targetpost['gender_send'] =  $_POST['targetpost']['gender_send'];
        $targetpost['age_min_send'] =  $_POST['targetpost']['age_min_send'];
        $targetpost['age_max_send'] = $_POST['targetpost']['age_max_send'];
        $targetpost['action_id'] = $action->getIdentity(); 
        $targetpost['country_name'] = '';
        $targetpost['city_name'] = '';
        if($targetpost['location_send'] == 'country'){
          $targetpost['country_name'] = $_POST['targetpost']['country_name'];
          $targetpost['lat'] =  $_POST['targetpost']['targetpostlat'];
          $targetpost['lng'] = $_POST['targetpost']['targetpostlng'];
          $targetpost['location_country'] = $_POST['targetpost']['location_country'];
        }else if($targetpost['location_send'] == 'city'){
          $targetpost['lat'] =  $_POST['targetpost']['targetpostlatcity'];
          $targetpost['lng'] = $_POST['targetpost']['targetpostlngcity'];  
          $targetpost['location_city'] = $_POST['targetpost']['location_city']; 
          $targetpost['city_name'] = $_POST['targetpost']['city_name'];        
        }else{
          $targetpost['lat'] =  '';
          $targetpost['lng'] = '';
          $targetpost['location_country']  = '';
          $targetpost['location_city']  = '';
        }
        if(empty($dbGetInsert))
          $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
          $dbGetInsert->query('INSERT INTO `engine4_sesadvancedactivity_targetpost`(`action_id`, `location_send`, `location_city`, `location_country`, `gender_send`, `age_min_send`, `age_max_send`, `lat`, `lng`,`country_name`,`city_name`) VALUES ("'.$targetpost['action_id'].'","'.$targetpost['location_send'].'","'.$targetpost['location_city'].'","'.$targetpost['location_country'].'","'.$targetpost['gender_send'].'","'.$targetpost['age_min_send'].'","'.$targetpost['age_max_send'].'","'.$targetpost['lat'].'","'.$targetpost['lng'].'","'.$targetpost['country_name'].'","'.$targetpost['city_name'].'")'); 
      }
      
      //reset privacy
      if(!$action->schedule_time)
        Engine_Api::_()->getDbTable('actions','sesadvancedactivity')->resetActivityBindings($action);
      // Preprocess attachment parameters
      $action->save();
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Success');
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
      $this->view->status = false;
      if(!empty($_GET['is_ajax']))
        $this->view->error = 'An error occured. Please try again after some time.';
      else
        throw $e;
    }    

       $feed = $this->view->activity($action,array('ulInclude'=>true, 'userphotoalign' => $this->view->userphotoalign));
       $last_id = $action->getIdentity();
       echo json_encode(array('status'=> $this->view->status,'last_id'=>$last_id,'feed'=>$feed,error=>$this->view->error),JSON_HEX_QUOT | JSON_HEX_TAG);die;
  }
  public function reschedulePostAction(){
    $action_id = $this->_getParam('action_id',false);
    $value = $this->_getParam('value',false);
    $action = Engine_Api::_()->getItem('sesadvancedactivity_action',$action_id);
    if($action && $action->schedule_time){
        $str = str_replace('_','/',$value);
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $str);
        $action->schedule_time = $date->format('Y-m-d H:i:s');;
        $action->save();
        $feed = $this->view->activity($action,array('ulInclude'=>true));
       $last_id = $action->getIdentity();
       echo json_encode(array('status'=> true,'last_id'=>$last_id,'feed'=>$feed),JSON_HEX_QUOT | JSON_HEX_TAG);die; 
    }    
   echo json_encode(array('status'=> false,'last_id'=>'','feed'=>''),JSON_HEX_QUOT | JSON_HEX_TAG);die; 
  }
  public function welcomeAction(){
    $this->_helper->content->setNoRender()->setEnabled();  
  }
}

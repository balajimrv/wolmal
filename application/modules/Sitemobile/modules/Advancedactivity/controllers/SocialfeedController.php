<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_SocialfeedController extends Seaocore_Controller_Action_Standard {

  protected $_HOST_NAME;
  protected $_MOBIDEFAULTCOUNT = 629817899;
  protected $_MOBISTARTFLAG = 3526;

  public function indexAction() {
    
    $this->_helper->content
            ->setNoRender()            
            ->setEnabled();
  }
  
  public function postAction() {
    
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid())
      return;

    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $strLimit = 6;
    $subject = null;

    $subject_guid = $this->_getParam('subject', null);
    if ($subject_guid) {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if (null === $subject) {
      $subject = $viewer;
    }
    $is_ajax = $this->_getParam('is_ajax', 0);
    // Make form
    $form = $this->view->form = new Activity_Form_Post();
    $this->view->status = true;
    // Check auth
  

    // Check if post
    if (!$this->getRequest()->isPost()) {
      if (empty($is_ajax)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
        return;
      } else {
        echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Not post')));
        exit();
      }
    }
    if (empty($is_ajax) && !Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
      // Check token
      if (!($token = $this->_getParam('token'))) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
        return;
      }
      $session = new Zend_Session_Namespace('ActivityFormToken');
      if ($token != $session->token) {

        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid token, please try again');
        return;
      }

      $session->unsetAll();
    }
    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $mobiAttempt = Engine_Api::_()->sitemobile()->isMobiAttempt();
    $getMobiAttemptStr = $getTotalMobiFlag = $getMobiStr = null;
    $getAttribName = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.attribs.name', false);
    $body = @$postData['body'];    
    if (isset($postData['auth_view']))
      $privacy = @$postData['auth_view'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;

    if (!$form->isValid($postData)) {
      if (empty($is_ajax)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
        return;
      } else {
        echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Invalid data')));
        exit();
      }
    } $composerDatas = $this->getRequest()->getParam('composer', null);

    // set up action variable
    $action = null;    

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');

      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = strtolower($attachmentData['type']);

        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {

          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
          $config = null;
        }

        if ($config) {
          $typeExplode = explode("-", $type);
          for ($i = 1; $i < count($typeExplode); $i++)
            $typeExplode[$i] = ucfirst($typeExplode[$i]);
          $type = implode("", $typeExplode);
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
        }
      }


      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // Is double encoded because of design mode
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      // Special case: status
      //CHECK IF BOTH FACEBOOK AND TWITTER IS DISABLED.
      $web_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.fb.twitter', 0);
      $currentcontent_type = 1;
      if (isset($_POST['activity_type']))
        $currentcontent_type = $_POST['activity_type'];
  

      $publishMessage = html_entity_decode($form->getValue('body'));
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if ($attachment) {
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if (empty($publishName)) {
          $publishName = ucwords($attachment->getShortType());
        }
        if (($tmpPicUrl = $attachment->getPhotoUrl())) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if ($publishPicUrl &&
                preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
          $publishPicUrl = null;
        }
      } else {
        $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
      }
      // Check to ensure proto/host
      if ($publishUrl &&
              false === stripos($publishUrl, 'http://') &&
              false === stripos($publishUrl, 'https://')) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if ($publishPicUrl &&
              false === stripos($publishPicUrl, 'http://') &&
              false === stripos($publishPicUrl, 'https://')) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if ($publishName) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }


      // Publish to facebook, if checked & enabled
      if ($currentcontent_type == 1) {
        try {

          $session = new Zend_Session_Namespace();

          $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

          if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
            $fb_data = array(
                'message' => $publishMessage,
            );
            if ($publishUrl) {
              if (isset($_POST['attachment'])) {
                $fb_data['link'] = $publishUrl;
              }
            }
            if ($publishName) {
              $fb_data['name'] = $publishName;
            }
            if ($publishDesc) {
              $fb_data['description'] = $publishDesc;
            }
            if ($publishPicUrl) {
              $fb_data['picture'] = $publishPicUrl;
            }
            if (isset($_POST['attachment']) && $_POST['attachment']['type'] == 'music') {

              $file = Engine_Api::_()->getItem('storage_file', $attachment->file_id);
              $fb_data['source'] = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->seaddonsBaseUrl() . '/' . $file->storage_path;
              $fb_data['type'] = 'mp3';
              $fb_data['picture'] = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Advancedactivity/externals/images/music-button.png';
              ;
            }

            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
            if ($subject && isset($subject->fbpage_url) && !empty($subject->fbpage_url) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postfbpage', 1)) {
              //EXTRACTING THE PAGE ID FROM THE PAGE URL.
              $url_expload = explode("?", $subject->fbpage_url);
              $url_expload = explode("/", $url_expload[0]);
              $count = count($url_expload);
              $page_id = $url_expload[--$count];
              //$manages_pages = $facebookApi->api('/me/accounts', 'GET');
              //NOW IF THE USER WHO IS COMENTING IS OWNER OF THIS FACEBOOK PAGE THEN GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED.

              $pageinfo = $facebookApi->api('/' . $page_id . '?fields=access_token', 'GET');
              if (isset($pageinfo['access_token']))
                $fb_data['access_token'] = $pageinfo['access_token'];

              $res = $facebookApi->api('/' . $page_id . '/feed', 'POST', $fb_data);
            }

              $last_fbfeedid = $_POST['fbmin_id'];
              
              $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivityfacebook-userfeed", array('getUpdate' => true, 'is_ajax' => 1, 'minid' => $last_fbfeedid, 'currentaction' => 'post_new'));
              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream, 'feedtype' => 'fbfeed'));
              exit();
          }
        } catch (Exception $e) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if ($currentcontent_type == 2) {
        try { 
          $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');          
          //$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($Api_twitter && $Api_twitter->isConnected()) {
            // @todo truncation?
            // @todo attachment
            $twitter = $twitterOauth = $Api_twitter->getApi();
            $lastfeedobject = $twitterOauth->post(
                  'statuses/update',                    
                   array('status' => html_entity_decode(substr($_POST['body'], 0, 138)))

            );
            

             $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitytwitter-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'feedobj' => $lastfeedobject));
              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream, 'feedtype' => 'tweetfeed'));
              exit();
          }
        } catch (Exception $e) { 
          // Silence
        }
      }

      // Publish to linkedin, if checked & enabled
      if ($currentcontent_type == 3) {
        try {
           $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
           $OBJ_linkedin = $Api_linkedin->getApi();     


          // $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($OBJ_linkedin) {
            if ($attachment):
              if ($publishUrl) {
                $content['submitted-url'] = $publishUrl;
              }
              if ($publishName && $publishUrl) {
                $content['title'] = $publishName;
              }
              if ($publishDesc) {
                $content['description'] = $publishDesc;
              }
              if ($publishPicUrl) {
                $content['submitted-image-url'] = $publishPicUrl;
              }
            endif;
            $content['comment'] = $publishMessage;

            $lastfeedobject = $OBJ_linkedin->share('new', $content);
         
              $last_linkedinfeedid = $_POST['linkedinmin_id'];

              $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitylinkedin-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'minid' => $last_linkedinfeedid, 'is_ajax' => 1));
              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream, 'feedtype' => 'linkedinfeed'));
              exit();
           
          }
        } catch (Exception $e) {
          // Silence
        }
      }
    
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }  
  }
  
  public function filterHTML($html_string) {
    try {
    $dom = new DOMDocument;
    $dom->loadHTML($html_string);
    $divs = $dom->getElementsByTagName('div');
    $innerHTML_contents = $this->DOMinnerHTML($divs->item(0));
    }catch (Exception $e) {
      
    }
    return $innerHTML_contents;
  }
  
  function DOMinnerHTML($element) 
{ 
   $innerHTML = ""; 
   $children = $element->childNodes; 
   foreach ($children as $child) 
   { 
      $tmp_dom = new DOMDocument(); 
      $tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
      $innerHTML.=trim($tmp_dom->saveHTML()); 
   } 
   return $innerHTML; 
}

  // Display Page for Facebook, Twitter, Linkedin SocialFeeds

  public function socialfeedAction() {
    $this->_helper->content
            ->setNoRender()
            //->setContentName("advancedactivity_index_socialfeed")
            ->setEnabled();
  }

  // Display Likes for Facebook Feed  
  public function getFbFeedLikesAction() {
    //Getting the all likes info belongs to this action id.

    $this->view->action_id = $action_id = $this->_getParam('action_id');
    $this->view->comment_id = $comment_id = $this->_getParam('comment_id', '');
    $this->view->page = $page_id = $this->_getParam('page', 1);
    $Next_id = $this->_getParam('next_id', '');
    $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
    $checksiteIntegrate = true;
    try {
      $fecheck_Connection = $facebookCheck->checkConnection(null, $facebook_userfeed);
      if ($facebook_userfeed && $fecheck_Connection) {
        $limit = 15;
        if (!empty($comment_id))
          $this->view->fbLikes = $fbLikes = $facebook_userfeed->api('/' . $comment_id . '/likes', array('limit' => (int) $limit));
        else
          $this->view->fbLikes = $fbLikes = $facebook_userfeed->api('/' . $action_id . '/likes', array('limit' => (int) $limit));

        $this->view->showlikes = true;
      }
    } catch (Exception $e) {
      $this->view->showlikes = false;
    }
  }

  // Display Likes for Facebook Feed  
  public function getFbFeedCommentsAction() {
    //Getting the all likes info belongs to this action id.

    $this->view->action_id = $action_id = $this->_getParam('action_id');
    $this->view->like_count = $this->_getParam('like_count');
    $this->view->page = $page_id = $this->_getParam('page', 1);
    $Next_id = $this->_getParam('next_id', '');
    $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
    $checksiteIntegrate = true;
    try {
      $fecheck_Connection = $facebookCheck->checkConnection(null, $facebook_userfeed);
      if ($facebook_userfeed && $fecheck_Connection) {
        $limit = 15;
        $this->view->fbComments = $fbComments = $facebook_userfeed->api('/' . $action_id . '/comments', array('limit' => (int) $limit));


        $this->view->showcoments = true;
      }
    } catch (Exception $e) {
      $this->view->showcomments = false;
    }
  }
  
   public function replyTwitterAction() {
    
    $this->view->tweet_id = $action_id = $this->_getParam('twitter_id');
    $this->view->screen_name = $this->_getParam('screen_name');
    $this->view->page = $page_id = $this->_getParam('page', 1);    
//    $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
//    $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
//    $checksiteIntegrate = true;
//    try {
//      $fecheck_Connection = $facebookCheck->checkConnection(null, $facebook_userfeed);
//      if ($facebook_userfeed && $fecheck_Connection) {
//        $limit = 15;
//        $this->view->fbComments = $fbComments = $facebook_userfeed->api('/' . $action_id . '/comments', array('limit' => (int) $limit));
//
//
//        $this->view->showcoments = true;
//      }
//    } catch (Exception $e) {
//      $this->view->showcomments = false;
//    }   
  }
  
  public function getLinkedinFeedLikesAction() {
    
    $this->view->linkedin_id = $linkedin_id = $this->_getParam('linkedin_id');
    $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
    $OBJ_linkedin = $Api_linkedin->getApi();
    try {
      $this->view->updateLikes = $OBJ_linkedin->likes($linkedin_id);
      
  }
  catch (Exception $e) {
    
    
  }
    
    
    
    
    $this->view->screen_name = $this->_getParam('screen_name');
    $this->view->page = $page_id = $this->_getParam('page', 1);    
    $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
    $checksiteIntegrate = true;
    try {
      $fecheck_Connection = $facebookCheck->checkConnection(null, $facebook_userfeed);
      if ($facebook_userfeed && $fecheck_Connection) {
        $limit = 15;
        $this->view->fbComments = $fbComments = $facebook_userfeed->api('/' . $action_id . '/comments', array('limit' => (int) $limit));


        $this->view->showcoments = true;
      }
    } catch (Exception $e) {
      $this->view->showcomments = false;
    }   
  }
  
  //SEND LINKEDIN MESSAGE
  
  public function sendLinkedinMessageAction() { 
    
    $this->view->form = $form = new Advancedactivity_Form_LinkedinCompose();
    $allParams =  $this->_getAllParams();
    $form->populate($allParams);
    
  }
  
  //FETCH LINKEDIN COMMENTS
  
  public function viewLinkedinCommentsAction() {  
     $this->view->post_id = $post_id = $this->_getParam('post_id', null);
     $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
     $this->view->timestamp = $timestamp = $this->_getParam('timestamp', null);
     $LinkedinObj = $Api_linkedin->getApi();
     try{     
       $response = $LinkedinObj->comments($post_id);
       $this->view->commentinfo = $getcommetUserinfo = json_decode(json_encode((array) simplexml_load_string($response['linkedin'])), 1); 
      
       
     }catch(Exception $e) {     
     
     }
    $form = $this->view->commentForm = new Sitemobile_modules_Activity_Form_Comment();
    
    $this->view->like_count = $this->_getParam('like_count', null);
    
  }
  
   //FETCH LINKEDIN COMMENTS
  
  public function getAllLikeUserAction() {  
     $this->view->post_id = $post_id = $this->_getParam('post_id', null);
     $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
     $LinkedinObj = $Api_linkedin->getApi();
     try{     
       $response = $LinkedinObj->likes($post_id);
       $this->view->commentinfo = $getcommetUserinfo = json_decode(json_encode((array) simplexml_load_string($response['linkedin'])), 1); 
       
       
     }catch(Exception $e) {     
     
     }
    $form = $this->view->commentForm = new Sitemobile_modules_Activity_Form_Comment();
    
    $this->view->like_count = $this->_getParam('like_count', null);
    
  }

}

<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Album.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Album extends Engine_Form
{
	protected $_defaultProfileId;
  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }
  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }
  public function init()
  {
    
    $anfalbumparams = Zend_Controller_Front::getInstance()->getRequest()->getParam('params');
    
    
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();
    // Init form
    $this
      ->setTitle('Add New Photos')
      ->setDescription('Choose photos on your computer to add to this album.')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
       ->setAttrib('autocomplete','false')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    // Init album
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
        ->from($albumTable, array('album_id', 'title'))
        ->where('owner_type = ?', 'user')
        ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
        ->query()
        ->fetchAll();
		
    $values['user_id'] = $user->getIdentity();
    $current_count =Engine_Api::_()->getDbtable('albums', 'sesalbum')->getUserAlbumCount($values);
    $quota = $quota = Engine_Api::_()->authorization()->getPermission($user_level, 'album', 'max_albums');
		
		if (($current_count >= $quota) && !empty($quota)){}else{
    	$albumOptions = array('0' => 'Create A New Album');
		}
    foreach( $myAlbums as $myAlbum ) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    $this->addElement('Select', 'album', array(
      'label' => 'Choose Album',
      'multiOptions' => $albumOptions,
      'onchange' => "updateTextFields()",
    ));
    // Init name 
    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'maxlength' => '255',
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
	if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){
		$this->addElement('Text', 'location', array(
        'label' => 'Location',
				'id' =>'locationSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
		$this->addElement('Text', 'lat', array(
        'label' => 'Lat',
				'id' =>'latSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
		$this->addElement('dummy', 'map-canvas', array());
		$this->addElement('dummy', 'ses_location', array('content'));
		$this->addElement('Text', 'lng', array(
        'label' => 'Lng',
				'id' =>'lngSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
	}
		// init to 
    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
		 /*$this->addElement('File', 'art_cover', array(
        'label' => 'Art Cover',
        'description' => 'Upload an art cover.'
    ));		
		$this->art_cover->addValidator('Extension', false, 'jpg,jpeg,png,PNG,JPG,JPEG');*/
    // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sesalbum')->getCategoriesAssoc();
		$album_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', 0);  
	  if( count($categories) > 0 ) {
			if((empty($_POST['album']) && intval($_POST['album']) == 0)){
		  if(!$album_id){
				$setting = Engine_Api::_()->getApi('settings', 'core');
				$categorieEnable = $setting->getSetting('sesalbum.category.enable','1');
				if($categorieEnable == 1){
					$required = true;	
					$allowEmpty = false;
				}else{
					$required = false;	
					$allowEmpty = true;	
				}
			}else{
				$required = false;	
				$allowEmpty = true;	
			}				
		}else{
				$required = false;	
				$allowEmpty = true;
		} 
			$categories = array(''=>'')+$categories;
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories,
				'allowEmpty' => $allowEmpty,
				'required' => $required,
    		'onchange' => "showSubCategory(this.value);showFields(this.value,1);",
		  ));
    	//Add Element: 2nd-level Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => "2nd-level Category",
          'allowEmpty' => true,
          'required' => false,
					'multiOptions' => array('0'=>''),
          'registerInArrayValidator' => false,
          'onchange' => "showSubSubCategory(this.value);"
      ));			
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => "3rd-level Category",
          'allowEmpty' => true,
          'registerInArrayValidator' => false,
          'required' => false,
					'multiOptions' => array('0'=>''),
					'onchange' => 'showFields(this.value,1);'
      ));
		$defaultProfileId = "0_0_" . $this->getDefaultProfileId();
    $customFields = new Sesbasic_Form_Custom_Fields(array(
        'item' => 'album',
        'decorators' => array(
            'FormElements'
    )));
    $customFields->removeElement('submit');
    if ($customFields->getElement($defaultProfileId)) {
      $customFields->getElement($defaultProfileId)
              ->clearValidators()
              ->setRequired(false)
              ->setAllowEmpty(true);
    }
    $this->addSubForms(array(
        'fields' => $customFields
    ));
		} 
    // Init descriptions
    $this->addElement('Textarea', 'description', array(
      'label' => 'Album Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    //ADD AUTH STUFF HERE
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'All Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );
    // Init search
    $this->addElement('Checkbox', 'search', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
      'value' => 1,
      'disableTranslator' => true
    )); 
    // Element: auth_view
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this album?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    // Element: auth_comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this album?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    } 
    // Element: auth_tag
    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_tag');
    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
    if( !empty($tagOptions) && count($tagOptions) >= 1 ) {
      // Make a hidden field
      if(count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array('value' => key($tagOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_tag', array(
            'label' => 'Tagging',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $tagOptions,
            'value' => key($tagOptions),
        ));
        $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
      }
    }
		
		$allowAdultContent = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.allow.adult.filtering');
		if($allowAdultContent){
			 // Init search
			$this->addElement('Checkbox', 'adult', array(
					'label' => "Mark Album as Adult",
					'value' => 0,
			));	
		}
		$this->addElement('hidden', 'sesmediaimporter_data', array('value' => ''));
		$viewer = Engine_Api::_()->user()->getViewer();		
		$sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
	
    //check dependent module sesprofile install or not
    if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock')) && in_array('sesalbum',$sesprofilelock_enable_module) && Engine_Api::_()->authorization()->getPermission($viewer, 'album', 'sesalbum_locked')) {
      // Video enable password
      $this->addElement('Select', 'is_locked', array(
          'label' => 'Enable Album Lock',
          'multiOptions' => array(
              0 => 'No',
              1 => 'Yes',
          ),
          'onchange' => 'enablePasswordFiled(this.value);',
          'value' => 0
      ));
      // Video lock password
      $this->addElement('Password', 'password', array(
          'label' => 'Set Lock Password',
          'value' => '',
      ));
    }
		if($anfalbumparams) {
			$this->addDisplayGroup(array('album','title','location','lat','lng','map-canvas','tags','category_id', 'subcat_id', 'subsubcat_id', 'description', 'search', 'auth_view', 'auth_comment', 'auth_tag', 'adult', 'is_locked', 'password'), 'sesact_popup_fields', array('disableLoadDefaultDecorators' => true));
			$sesact_popup_fields = $this->getDisplayGroup('sesact_popup_fields');
			$sesact_popup_fields->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'sesact_popup_fields', 'class'=>'sesbasic_custom_scroll'))));
		}
		
		$translate = Zend_Registry::get('Zend_Translate');
		$this->addElement('Dummy', 'fancyuploadfileids', array('content'=>'<input id="fancyuploadfileids" name="file" type="hidden" value="" >'));
    
    if(empty($anfalbumparams)) {
    
      $this->addElement('Dummy', 'tabs_form_albumcreate', array(
            'content' => '<div class="sesalbum_create_form_tabs sesbasic_clearfix sesbm"><ul id="sesalbum_create_form_tabs" class="sesbasic_clearfix"><li class="active sesbm"><i class="fa fa-arrows sesbasic_text_light"></i><a href="javascript:;" class="drag_drop">'.$translate->translate('Drag & Drop').'</a></li><li class=" sesbm"><i class="fa fa-upload sesbasic_text_light"></i><a href="javascript:;" class="multi_upload">'.$translate->translate('Multi Upload').'</a></li><li class=" sesbm"><i class="fa fa-link sesbasic_text_light"></i><a href="javascript:;" class="from_url">'.$translate->translate('From URL').'</a></li></ul></div>',
      ));
      $this->addElement('Dummy', 'drag-drop', array(
            'content' => '<div id="dragandrophandler" class="sesalbum_upload_dragdrop_content sesbasic_bxs dragandrophandler">'.$translate->translate('Drag & Drop Photos Here').'</div>',
      ));
      $this->addElement('Dummy', 'from-url', array(
            'content' => '<div id="from-url" class="sesalbum_upload_url_content sesbm"><input type="text" name="from_url" id="from_url_upload" value="" placeholder="'.$translate->translate('Enter Image URL to upload').'"><span id="loading_image"></span><span></span><button id="upload_from_url">'.$translate->translate('Upload').'</button></div>',
      ));
			$feeddiv = '';
    } else {
      //AAF Feed Album Work
      $this->addElement('Dummy', 'tabs_form_albumcreate', array(
            'content' => '<div class="sesalbum_create_form_tabs sesbasic_clearfix sesbm"><ul id="sesalbum_create_form_tabs" class="sesbasic_clearfix"><li class="sesbm"><a href="javascript:;" class="multi_upload sesbasic_button"><i class="fa fa-plus"></i>'.$translate->translate('Add Photos').'</a></li></ul></div>',
      ));
    $feeddiv = '<div class="sesalbum_upload_item_plus multi_upload_sesact sesbm"><a href="javascript:void(0);"><i class="fa fa-plus"></i><span>Add More Photos</span></a></div>';
    }
    
    // Init file
  /* $fancyUpload = new Engine_Form_Element_FancyUpload('file');
   $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);*/
	
    $this->addElement('Dummy', 'file_multi_sesalbum', array('content'=>'<input type="file" accept="image/x-png,image/jpeg" onchange="readImageUrlsesalbum(this)" multiple="multiple" id="file_multi_sesalbum" name="file_multi">'));
    $this->addElement('Dummy', 'uploadFileContainer', array('content'=>'<div id="show_photo_sesalbum_container" class="sesalbum_upload_photos_container sesbasic_bxs sesbasic_custom_scroll clear"><div id="show_photo_sesalbum">'.$feeddiv.'</div></div>'));
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));
  }
  public function clearAlbum()
  {
    $this->getElement('album')->setValue(0);
  }
  public function saveValues($isSesmediaimporter = true)
  {
    $set_cover = false;
    $values = $this->getValues();
    $params = array();
    if ((empty($values['owner_type'])) || (empty($values['owner_id'])))
    {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['owner_type'] = 'user';
    }
    else
    {
      $params['owner_id'] = $values['owner_id'];
      $params['owner_type'] = $values['owner_type'];
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
    if( ($values['album'] == 0) )
    {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['category_id'] = (int) @$values['category_id'];
			$params['subcat_id'] = (int) @$values['subcat_id'];
			$params['subsubcat_id'] = (int) @$values['subsubcat_id'];
      $params['description'] = $values['description'];
      $params['search'] = $values['search'];
		 //disable lock if password not set.
			if (isset($values['is_locked']) && $values['is_locked'] && $values['password'] == '') {
				$params['is_locked'] = '0';
			}else if(isset($values['is_locked']) && isset($values['password'])){
				$params['is_locked'] = $values['is_locked'];
				$params['password'] = $values['password'];
			}
      $album = Engine_Api::_()->getDbtable('albums', 'sesalbum')->createRow();
      $album->setFromArray($params);
      $album->save();
      $set_cover = true;
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if( empty($values['auth_view']) ) {
        $values['auth_view'] = key($form->auth_view->options);
        if( empty($values['auth_view']) ) {
          $values['auth_view'] = 'everyone';
        }
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if( empty($values['auth_comment']) ) {
          $values['auth_comment'] = 'owner_member';
        }
      }
      if( empty($values['auth_tag']) ) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if( empty($values['auth_tag']) ) {
          $values['auth_tag'] = 'owner_member';
        }
      }
      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $tagMax = array_search($values['auth_tag'], $roles);
      foreach( $roles as $i => $role ) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }
    }
    else
    {
      if (!isset($album))
      {
        $album = Engine_Api::_()->getItem('album', $values['album']);
      }
    }
		$api = Engine_Api::_()->getDbtable('actions', 'activity');
    if($isSesmediaimporter)
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' =>  count(explode(' ',rtrim($_POST['file'],' ')))));
    // Do other stuff
    $count = 0;
	if(isset($_POST['file'])){
		$explodeFile = explode(' ',rtrim($_POST['file'],' '));
    foreach( $explodeFile as $photo_id )
    {
			if($photo_id == '')
				continue;
      $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
      if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;
      if(isset($_POST['cover']) && $_POST['cover'] == $photo_id ){
			 $album->photo_id = $photo_id;
       $album->save();
			 unset($_POST['cover']);
			 $set_cover = false;
			}else if( $set_cover){
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
      $photo->album_id = $album->album_id;
      $photo->order    = $photo_id;
      $photo->save();
      if( $action instanceof Activity_Model_Action && $count < 9 && $isSesmediaimporter)
      {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
	}	
		
		$album->adult = isset($values['adult']) ? $values['adult'] : 0;
		$album->ip_address = $_SERVER['REMOTE_ADDR'];
  	if(isset($_POST['location']) && !empty($_POST['location']))
			$album->location = $_POST['location'];
		$album->save();
    return $album;
  }
}
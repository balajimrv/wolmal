<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Create extends Engine_Form {

  public $_error = array();
  protected $_packageId;
  protected $_owner;
  
  public function getOwner() {
    return $this->_owner;
  }

  public function setOwner($owner) {
    $this->_owner = $owner;
    return $this;
  }
  public function getPackageId() {
    return $this->_packageId;
  }

  public function setPackageId($package_id) {
    $this->_packageId = $package_id;
    return $this;
  }

  public function init() {
    parent::init();
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->getOwner();
    $viewer_id = $viewer->getIdentity();
    $userlevel_id = $user->level_id;
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    $this->setTitle('Open a New Store')
            ->setDescription('Configure your store to showcase your offerings and connect to your customers.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitestores_create');

    // TITLE
    $this->addElement('Text', 'title', array(
        'label' => 'Title',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    // Element: store_url
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', Zend_Controller_Front::getInstance()->getRequest()->getParam('store', null));
    $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
    $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    $show_url = $coreSettings->getSetting('sitestore.showurl.column',1);
    $change_url = $coreSettings->getSetting('sitestore.change.url',1);
    $edit_url = $coreSettings->getSetting('sitestore.edit.url',0);
    //if (empty($store_id)) {

// // This will be the end of your store URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your store will be:
//  <br /> <span id="store_url_address">http://%s</span>
//       $description = Zend_Registry::get('Zend_Translate')->_('This will be the end of your store URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your store will be:');
      //$description = sprintf($description, $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => Zend_Registry::get('Zend_Translate')->_('STORE-NAME')), 'sitestore_entry_view')).'<br />';

			$link = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => Zend_Registry::get('Zend_Translate')->_('STORE-NAME')), 'sitestore_entry_view');

      if(!empty($sitestoreUrlEnabled) && !empty($change_url)) {

				$front = Zend_Controller_Front::getInstance();
				$baseUrl = $front->getBaseUrl();
				$STORE_NAME = Zend_Registry::get('Zend_Translate')->_("STORE-NAME");
        $link2 = $_SERVER['HTTP_HOST'] . $baseUrl.'/'.$STORE_NAME;
				$limit = $coreSettings->getSetting('sitestore.likelimit.forurlblock', 5);
				if(empty($limit)) {
				  $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your store URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your store will be: %s"), "<span id='short_store_url_address'>http://$link2</span>");
				}
				else {
				  $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your store URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your store will be: %s"), "<span id='store_url_address'>http://$link</span>");
					$description = $description.sprintf(Zend_Registry::get('Zend_Translate')->_('<br />and if your store has %1$s or more likes URL will be: <br />%2$s'), "$limit", "<span id='short_store_url_address'>http://$link2</span>");
				}
      }
      else {
				$description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your store URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your store will be: %s"), "<span id='store_url_address'>http://$link</span>");
      }

      if(!empty($sitestoreUrlEnabled) && !empty($store_id) && !empty($show_url) && !empty($edit_url)) {
				$this->addElement('Text', 'store_url', array(
						'label' => 'URL',
						'description' => $description,
						'autocomplete' => 'off',
						'required' => true,
						'allowEmpty' => false,
						'validators' => array(
								array('NotEmpty', true),
								// array('Alnum', true),
								array('StringLength', true, array(3, 255)),
								array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
							
						),
						'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
				));
				$this->store_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
				$this->store_url->getValidator('NotEmpty')->setMessage('Please enter a valid store url.', 'isEmpty');
				$this->store_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
				$this->addElement('dummy', 'store_url_msg', array('value' => 0));
      }
      elseif(empty($store_id)) {
        $this->addElement('Text', 'store_url', array(
						'label' => 'URL',
						'description' => $description,
						'autocomplete' => 'off',
						'required' => true,
						'allowEmpty' => false,
						'validators' => array(
								array('NotEmpty', true),
								// array('Alnum', true),
								array('StringLength', true, array(3, 255)),
								array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
								array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitestore_stores', 'store_url'),array('field' => 'store_id', 'value != ?' => 1))
						),
						'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
								//'onblur' => 'var el = this; en4.user.checkstore_urlTaken(this.value, function(taken){ el.style.marginBottom = taken * 100 + "px" });'
				));
				$this->store_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
				$this->store_url->getValidator('NotEmpty')->setMessage('Please enter a valid store url.', 'isEmpty');
				$this->store_url->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this store url, please use another one.', 'recordFound');
				$this->store_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
				//$this->store_url->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');
				$this->addElement('dummy', 'store_url_msg', array('value' => 0));
      }
    //}

    // init to
    $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->tags->getDecorator("Description")->setOption("placement", "append");

    // prepare categories
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

      //category field
			if( !$this->_item && $coreSettings->getSetting('sitestore.profile.fields', 1)) {
				$this->addElement('Select', 'category_id', array(
						'label' => 'Category',
						'allowEmpty' => false,
						'required' => true,
						'multiOptions' => $categories_prepared,
						'onchange' => " var profile_type = getProfileType($(this).value); 
														if(profile_type == 0) profile_type = '';
														$('0_0_1').value = profile_type;
														changeFields($('0_0_1'));
														subcategory(this.value, '', '');",
				));

			} else {
				$this->addElement('Select', 'category_id', array(
						'label' => 'Category',
						'allowEmpty' => false,
						'required' => true,
						'multiOptions' => $categories_prepared,
						'onchange' => "subcategory(this.value, '', '');",
				));
			}
    }

		$this->addElement('Select', 'subcategory_id', array(
				'RegisterInArrayValidator' => false,
				'allowEmpty' => true,
				'required' => false,
		));

		$this->addElement('Select', 'subsubcategory_id', array(
				'RegisterInArrayValidator' => false,
				'allowEmpty' => true,
				'required' => false,
		));
		$this->addDisplayGroup(array(
				'subcategory_id',
				'subsubcategory_id',
						), 'Select', array(
				'decorators' => array(array('ViewScript', array(
										'viewScript' => 'application/modules/Sitestore/views/scripts/_formSubcategory.tpl',
										'class' => 'form element')))
		));

    if( !$this->_item && $coreSettings->getSetting('sitestore.profile.fields', 1)) {
			$customFields = new Sitestore_Form_Custom_Standard(array(
										'item' => 'sitestore_store',
										'decorators' => array(
														'FormElements'
										)));

			$customFields->removeElement('submit');

			$customFields->getElement("0_0_1")           
							->clearValidators()
							->setRequired(false)
							->setAllowEmpty(true);

			$this->addSubForms(array(
				'fields' => $customFields
			));
    } 

    if($coreSettings->getSetting('sitestore.description.allow', 1)) {
			if ($coreSettings->getSetting('sitestore.requried.description', 1)) {
				// body
				$this->addElement('textarea', 'body', array(
						'label' => 'Description',
						'required' => true,
						'allowEmpty' => false,
						'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
						'filters' => array(
								'StripTags',
								//new Engine_Filter_HtmlSpecialChars(),
								new Engine_Filter_EnableLinks(),
								new Engine_Filter_Censor(),
						),
				));
			} else {
				$this->addElement('textarea', 'body', array(
						'label' => 'Description',
						'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
						'filters' => array(
								'StripTags',
								//new Engine_Filter_HtmlSpecialChars(),
								new Engine_Filter_EnableLinks(),
								new Engine_Filter_Censor(),
						),
				));
			}
    }
    //$allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestore', 'photo');
    //if ($allowed_upload) {
    if ($coreSettings->getSetting('sitestore.requried.photo', 1)) {
      $this->addElement('File', 'photo', array(
          'label' => 'Main Photo',
          'required' => true,
          'allowEmpty' => false,
      ));
      $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
    } else {
      $this->addElement('File', 'photo', array(
          'label' => 'Main Photo',
      ));
      $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
    }
    //}
    // PRICE
    if ($coreSettings->getSetting('sitestore.price.field', 0)) {
      $localeObject = Zend_Registry::get('Locale');
      $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
      $this->addElement('Text', 'price', array(
          'label' => "Price ($currencyName)",
          // 'description' => '(Zero will make this a free store.)',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              )));
      //$this->price->getDecorator('Description')->setOption('placement', 'append');
    }
    // LOCATION
    if ($coreSettings->getSetting('sitestore.locationfield', 1)) {
      $this->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              )));
      $this->location->getDecorator('Description')->setOption('placement', 'append');
      $this->addElement('Hidden', 'locationParams', array( 'order' => 800000));
      
      
      include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';      
    }

    // Privacy
    // Privacy
    $storeadminsetting = $coreSettings->getSetting('sitestore.manageadmin', 1);
    if (!empty($storeadminsetting)) {
      $ownerTitle = "Store Admins";
    } else {
      $ownerTitle="Just Me";
    }


    //START SITESTOREMEMBER PLUGIN WORK
    $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'smecreate');
    $allowMemberInthisPackage = false;
    $allowMemberInthisPackage = Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestoremember");
    
    if ($sitestoreMemberEnabled) {
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if ($allowMemberInthisPackage) {
        
          $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
          if (!empty($memberTitle)) {
						$this->addElement('Text', 'member_title', array(
						'label' => 'What will members be called?',
						'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
						'filters' => array(
						'StripTags',
						new Engine_Filter_Censor(),
						)));
						$this->member_title->getDecorator('Description')->setOption('placement', 'append');
          }

					$memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.invite.option' , 1);
           if(!empty($memberInvite)) {
						$this->addElement('Radio', 'member_invite', array(
							'label' => 'Invite member',	
							//'description' => 'Do you want store members to invite other people to this store?',
							'multiOptions' => array(
							  '0' => 'Yes, members can invite other people.',
						   	'1' => 'No, only store admins can invite other people.',
							  
							),
							'value' => '1',
						));
					}

					$member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.approval.option' , 1);
					if (!empty($member_approval)) {
						$this->addElement('Radio', 'member_approval', array(
							'label' => 'Approve members?',
							'description' => 'When people try to join this store, should they be allowed '.
							'to join immediately, or should they be forced to wait for approval?',
							'multiOptions' => array(
								'1' => 'New members can join immediately.',
								'0' => 'New members must be approved.',
							),
							'value' => '1',
						));
					}

			  }
      } else if (!empty($allowMemberInLevel)) {
      
				$memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
				if (!empty($memberTitle)) {
					$this->addElement('Text', 'member_title', array(
					'label' => 'What will members be called?',
					'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
					'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
					)));
					$this->member_title->getDecorator('Description')->setOption('placement', 'append');
				}
				
				$this->addElement('Radio', 'member_invite', array(
						'label' => 'Invite member',	
						'multiOptions' => array(
							  '0' => 'Yes, members can invite other people.',
						   	'1' => 'No, only store admins can invite other people.',
						),
						'value' => '1',
					));

					$this->addElement('Radio', 'member_approval', array(
						'label' => 'Approve members?',
						'description' => ' When people try to join this store, should they be allowed '.
							'to join immediately, or should they be forced to wait for approval?',
						'multiOptions' => array(
							'1' => 'New members can join immediately.',
							'0' => 'New members must be approved.',
						),
						'value' => '1',
					));
      }
    }
    
    
    $this->addElement('Select', 'all_post', array(
        'label' => 'Post in Updates Tab',
        'multiOptions' => array("1" => "Everyone", "0" => "Store Admins"),
        'description' => Zend_Registry::get('Zend_Translate')->_('Who is allowed to post in this store?'),
        'attribs' => array('class' => 'sp_quick_advanced')
    ));
    $this->all_post->getDecorator('Description')->setOption('placement', 'append');
    
    //END STORE MEMBER WORK
		$availableLabels = array(
			'everyone' => 'Everyone',
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
		);
		if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
			$availableLabels['member'] = 'Store Members Only';
		} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		}
		$availableLabels['owner'] = $ownerTitle;

    // View
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    if (count($view_options) >= 1) {
      $this->addElement('Select', 'auth_view', array(
          'label' => 'View Privacy',
          'description' => 'Who may see this store?',
          'multiOptions' => $view_options,
          'value' => key($view_options),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    // Comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_comment'); 
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    if (count($comment_options) >= 1) {
      $this->addElement('Select', 'auth_comment', array(
          'label' => 'Comment Privacy',
          'description' => 'Who may post comments on this store?',
          'multiOptions' => $comment_options,
          'value' => key($comment_options),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }
    
    //START DISCUSSION PRIVACY WORK
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif( !empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
		
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_sdicreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestorediscussion")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sdicreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'sdicreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sdicreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }

        if ($can_show_list) {
          $this->addElement('Select', 'sdicreate', array(
              'label' => 'Discussion Topic Post Privacy',
              'description' => 'Who may post discussion topics for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->sdicreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END DISCUSSION PRIVACY WORK    
  
    //START PHOTO PRIVACY WORK
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif( !empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
		
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_spcreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestorealbum")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'spcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'spcreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'spcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }

        if ($can_show_list) {
          $this->addElement('Select', 'spcreate', array(
              'label' => 'Photo Creation Privacy',
              'description' => 'Who may upload photos for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->spcreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END PHOTO PRIVACY WORK
    //START SITESTOREDOCUMENT PLUGIN WORK
    $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
      if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore'))) || $sitestoreDocumentEnabled) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
			
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_sdcreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestoredocument")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sdcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'sdcreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sdcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'sdcreate', array(
              'label' => 'Documents Creation Privacy',
              'description' => 'Who may create documents for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->sdcreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTOREDOCUMENT PLUGIN WORK
    //START SITESTOREVIDEO PLUGIN WORK
    $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
    if ($sitestoreVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
			
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_svcreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestorevideo")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'svcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'svcreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'svcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'svcreate', array(
              'label' => 'Videos Creation Privacy',
              'description' => 'Who may create videos for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->svcreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTOREVIDEO PLUGIN WORK
    //START SITESTOREPOLL PLUGIN WORK
    $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
    if ($sitestorePollEnabled) {

			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
			
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_splcreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestorepoll")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'splcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'splcreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'splcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'splcreate', array(
              'label' => 'Polls Creation Privacy',
              'description' => 'Who may create polls for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->splcreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTOREPOLL PLUGIN WORK
    //START SITESTORENOTE PLUGIN WORK
    $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
    if ($sitestoreNoteEnabled) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
			
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_sncreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestorenote")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sncreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'sncreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'sncreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'sncreate', array(
              'label' => 'Notes Creation Privacy',
              'description' => 'Who may create notes for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->sncreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTORENOTE PLUGIN WORK
    //START SITESTOREEVENT PLUGIN WORK
     $sitestoreEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
		if ($sitestoreEventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
      $availableLabels = array(
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'like_member' => 'Who Liked This Store',
      );
      if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
        $availableLabels['member'] = 'Store Members Only';
      } elseif (!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
        $availableLabels['member'] = 'Store Members Only';
      }
      $availableLabels['owner'] = $ownerTitle;

      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_secreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestoreevent")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'secreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'secreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'secreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'secreate', array(
              'label' => 'Event Creation Privacy',
              'description' => 'Who may create events for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->secreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTOREEVENT PLUGIN WORK
    //START SITESTOREMUSIC PLUGIN WORK
    $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
    if ($sitestoreMusicEnabled) {
			$availableLabels = array(
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'like_member' => 'Who Liked This Store',
			);
			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
				$availableLabels['member'] = 'Store Members Only';
			} elseif(!empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
				$availableLabels['member'] = 'Store Members Only';
		  }
			$availableLabels['owner'] = $ownerTitle;
			
      $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_smcreate');
      $options_create = array_intersect_key($availableLabels, array_flip($options));

      if (!empty($options_create)) {
        $can_show_list = true;
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($this->getPackageId(), "modules", "sitestoremusic")) {
            $can_show_list = false;
            $this->addElement('Hidden', 'smcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        } else {
          $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'smcreate');
          if (!$can_create) {
            $can_show_list = false;
            $this->addElement('Hidden', 'smcreate', array(
                'value' => @array_search(@end($options_create), $options_create)
            ));
          }
        }
        if ($can_show_list) {
          $this->addElement('Select', 'smcreate', array(
              'label' => 'Music Creation Privacy',
              'description' => 'Who may upload music for this store?',
              'multiOptions' => $options_create,
              'value' => @array_search(@end($options_create), $options_create)
          ));
          $this->smcreate->getDecorator('Description')->setOption('placement', 'append');
        }
      }
    }
    //END SITESTOREMUSIC PLUGIN WORK
    
    //START SUB STORE WORK
//		if (empty($parent_id)) {
//			$available_Labels = array(
//				'registered' => 'All Registered Members',
//				'owner_network' => 'Friends and Networks',
//				'owner_member_member' => 'Friends of Friends',
//				'owner_member' => 'Friends Only',
//				'like_member' => 'Who Liked This Store',
//			);
//			if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
//				$available_Labels['member'] = 'Store Members Only';
//			} elseif( !empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
//				$available_Labels['member'] = 'Store Members Only';
//			}
//			$available_Labels['owner'] = $ownerTitle;
//
//			$substorecreate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'auth_sspcreate');
//			$substorecreate_options = array_intersect_key($available_Labels, array_flip($substorecreate_options));
//			
//			$can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitestore_store', 'sspcreate');
//			$can_show_list = true;
//			if (!$can_create) {
//				$can_show_list = false;
//				$this->addElement('Hidden', 'sspcreate', array(
//						'value' => @array_search(@end($substorecreate_options), $substorecreate_options)
//				));
//			}
//			
//			if (count($substorecreate_options) >= 1 && !empty($can_show_list)) {
//				$this->addElement('Select', 'auth_sspcreate', array(
//						'label' => 'Sub Stores Creation Privacy',
//						'description' => 'Who may create sub stores in this store?',
//						'multiOptions' => $substorecreate_options,
//						'value' => @array_search(@end($substorecreate_options), $substorecreate_options)
//				));
//				$this->auth_sspcreate->getDecorator('Description')->setOption('placement', 'append');
//			}
//    }
    //END WORK FOR SUBSTORE WORK.

    //NETWORK BASE STORE VIEW PRIVACY
    if (Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
      // Make Network List
      $table = Engine_Api::_()->getDbtable('networks', 'network');
      $select = $table->select()
                      ->from($table->info('name'), array('network_id', 'title'))                    
                      ->order('title');
      $result = $table->fetchAll($select);

      $networksOptions = array('0' => 'Everyone');
      foreach ($result as $value) {
        $networksOptions[$value->network_id] = $value->title;
      }
      if (count($networksOptions) > 0) {
        $this->addElement('Multiselect', 'networks_privacy', array(
            'label' => 'Networks Selection',
            'description' => 'Select the networks, members of which should be able to see your store. (Press Ctrl and click to select multiple networks. You can also choose to make your store viewable to everyone.)',
//            'attribs' => array('style' => 'max-height:150px; '),
            'multiOptions' => $networksOptions,
            'value' => array(0)
        ));
      }
    }

    $table = Engine_Api::_()->getDbtable('listmemberclaims', 'sitestore');
    $select = $table->select()
                    ->where('user_id = ?', $viewer_id)
                    ->limit(1);

    $row = $table->fetchRow($select);
    if ($row !== null) {
      $this->addElement('Checkbox', 'userclaim', array(
          'label' => 'Show "Claim this Store" link on this store.',
          'value' => 1,
      ));
    }
    $this->addElement('Select', 'draft', array(
        'label' => 'Status',
        'multiOptions' => array("1" => "Published", "0" => "Saved As Draft"),
        'description' => 'If this entry is published, it cannot be switched back to draft mode.',
        'onchange'=>'checkDraft();'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Checkbox', 'search', array(
        'label' => 'Show this store in search results.',
        'value' => 1,
    ));

    $this->addElement('Radio', 'toggle_products_status', array(
							'label' => '',	
							'description' => '',
							'multiOptions' => array(
							  '1' => 'Yes',
						   	'0' => 'No',
							),
							'value' => '0',
						));
    
    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Open Store',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
       // 'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitestore_general', true),
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }
}
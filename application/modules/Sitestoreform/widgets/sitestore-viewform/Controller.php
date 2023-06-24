<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Widget_SitestoreViewformController extends Seaocore_Content_Widget_Abstract
{ 
  //protected $_childCount;
  public function indexAction()
  {
		global $sitestoreform_mail;
    $viewer =  Engine_Api::_()->user()->getViewer();
    $this->view->viewr_id = $viewr_id = $viewer->getIdentity();
    
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
		$getPackageFormMail = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreform');
		$sitestoreform_tabInfo = Zend_Registry::isRegistered('sitestoreform_tabInfo') ? Zend_Registry::get('sitestoreform_tabInfo') : null;
    $Showtab_Fornonloggedin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform_formtabseeting', 1);
    if (empty($viewr_id) && $Showtab_Fornonloggedin == 0) {
      return $this->setNoRender();
    }
		if( empty($sitestoreform_tabInfo) ) {
			return $this->setNoRender();
		}
		
		$ITEM =  Engine_Api::_()->core()->getSubject();
		$layout_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		if(empty($layout_type)) {
			$sitestoreformtable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
			$offerWidgetName = $sitestoreformtable->select()
									->from($sitestoreformtable->info('name'),'offer_tab_name')
									->where('store_id = ?', $ITEM->store_id)
									->query()->fetchColumn();
			if( !empty($offerWidgetName) ) {
				$offer_tab_name = $offerWidgetName;
			}
			else {
				$tablecontent = Engine_Api::_()->getDbtable('content', 'core');
				$params = $tablecontent->select()
										->from($tablecontent->info('name'),'params')
										->where('name = ?', 'sitestoreform.sitestore-viewform')
										->query()->fetchColumn();
				$decodedParam = Zend_Json::decode($params);
				$tabName = $decodedParam['title'];
				$offer_tab_name = $tabName;
			}
			$this->getElement()->setTitle($offer_tab_name);
		}
    $occupations = array(  'admn' =>  'Administrative / Secretarial',
			'arch' =>  'Architecture / Interior design',
			'crea' =>  'Artistic / Creative / Performance',
			'educ' =>  'Education / Teacher / Professor',
			'mngt' =>  'Executive / Management',
			'fash' =>  'Fashion / Model / Beauty',
			'fina' =>  'Financial / Accounting / Real Estate',
			'labr' =>  'Labor / Construction',
			'lawe' =>  'Law enforcement / Security / Military',
			'legl' =>  'Legal',
			'medi' =>  'Medical / Dental / Veterinary / Fitness',
			'nonp' =>  'Nonprofit / Volunteer / Activist',
			'poli' =>  'Political / Govt / Civil Service / Military',
			'retl' =>  'Retail / Food services',
			'retr' =>  'Retired',
			'sale' =>  'Sales / Marketing',
			'self' =>  'Self-Employed / Entrepreneur',
			'stud' =>  'Student',
			'tech' =>  'Technical / Science / Computers / Engineering',
			'trav' =>  'Travel / Hospitality / Transportation',
			'othr'=>  'Other profession'
    );

		$education = array( 
			'high_school' =>  'High School',
			'some_college' =>  'Some College',
			'associates' =>  'Associates Degree',
			'bachelors' =>  'Bachelors Degree',
			'graduate' =>  'Graduate Degree',
			'phd' =>  'PhD / Post Doctoral'
	
		);
  
		$relationshipstatus = array( 
			'single' =>  'Single',
			'relationship' =>  'In a Relationship',
			'engaged' =>  'Engaged',
			'married' =>  'Married',
			'complicated' => 'Its Complicated',
			'open' =>  'In an Open Relationship',
			'widow' =>  'Widowed'
		);

    $weight = array( 
			'slender' =>  'Slender',
			'average' =>  'Average',
			'athletic' =>  'Athletic',
			'heavy' =>  'Heavy',
			'stocky' =>  'Stocky',
			'little_fat' =>  'A few extra pounds' 
   );

		$religion = array( 
			'agnostic' =>  'Agnostic',
			'atheist' =>  'Atheist',
			'buddhist' =>  'Buddhist',
			'taoist' =>  'Taoist',
			'catholic' =>  'Christian (Catholic)',
			'mormon' =>  'Christian (LDS)', 
			'protestant' =>  'Christian (Protestant)',
			'hindu' =>  'Hindu',
			'jewish' =>  'Jewish',
			'muslim' =>  'Muslim ',
			'spiritual' =>  'Spiritual',
			'other' =>  'Other' 
		);

		$Ethnicity = array( 
			'asian' =>  'Asian',
			'black' =>  'Black / African descent',
			'hispanic' =>  'Latino / Hispanic',
			'pacific' =>  'Pacific Islander',
			'white' =>  'White / Caucasian',
			'other' =>  'Other' 
		);

		$political_views = array( 
			'mid' =>  'Middle of the Road',
			'far_right' =>  'Very Conservative',
			'right' =>  'Conservative',
			'left' =>  'Liberal',
			'far_left' =>  'Very Liberal',
			'anarchy' =>  'Non-conformist',
			'libertarian' =>  'Libertarian',
			'green' =>  'Green',
			'other' =>  'Other' 
		);
    
    $incomesource = array( 
			'0' =>  'Less than $25,000',
			'25_35' =>  '$25,001 to $35,000',
			'35_50' =>  '$35,001 to $50,000',
			'50_75' =>  '$50,001 to $75,000',
			'75_100' =>  '$75,001 to $100,000',
			'100_150' =>  '$100,001 to $150,000', 
			'1' =>  '$150,001' 
		);
    $lookingfor = array( 
			'friendship' =>  'Friendship',
			'dating' =>  'Dating',
			'relationship' =>  'A Relationship',
			'networking' =>  'Networking'
		);
    $interestedin = array( 
			'men' =>  'Men',
			'women' =>  'Women'
		);
    $front = Zend_Controller_Front::getInstance();

    //GET THE SUBJECT OF SITESTORE
    $this->view->store_id = $store_id =   $ITEM->store_id;
    $store_name =   $ITEM->title;

		$this->view->sitestore_object = $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $store_id);

     // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore_object->package_id, "modules", "sitestoreform")) {
        return $this->setNoRender();
      }
    }else{
				$isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore_object, 'form');
			if (empty($isStoreOwnerAllow)) {
				return $this->setNoRender();
			}
		}
    // PACKAGE BASE PRIYACY END

		//START MANAGE-ADMIN CHECK

		$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore_object, 'view');
		if(empty($isManageAdmin)) {
			 return $this->setNoRender();
		}

		//END MANAGE-ADMIN CHECK

    $this->view->user_id =$owner_id =  $ITEM->owner_id;

    $this->view->slug =  $ITEM->getSlug();
    
    $this->view->store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);

    //GET THE OBJECT OF STOREQUESTION TABLE$this->view->store_url
    $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
    $select_quetion = $quetion->select()->where('store_id = ?', $store_id);
    $result_quetion = $quetion->fetchRow($select_quetion);
    if( !empty($result_quetion) )
      $this->view->option_id = $option_id = $result_quetion->option_id;
    if (empty($option_id)) {
      return $this->setNoRender();
    }

    $itestoreforms_table = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
    $select_sitestoreform = $itestoreforms_table->select()->where('store_id = ?', $store_id);
    $select_sitestoreform_result = $itestoreforms_table->fetchRow($select_sitestoreform);
    $sitestoreform_id= $select_sitestoreform_result->sitestoreform_id;
    if($select_sitestoreform_result->status == 0 || $select_sitestoreform_result->storeformactive == 0){
      return $this->setNoRender();
    }
		Engine_Api::_()->core()->clearSubject();
		$subject = null;
		if ( !Engine_Api::_()->core()->hasSubject() )
		{
			if( null !== $store_id )
			{
				$subject = Engine_Api::_()->getItem('sitestoreform', $sitestoreform_id);
				if ( $subject && $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}
			}
		}

    //GET THE LOGGED IN USER ID
    $this->view->topLevelId = $topLevelId = 1;
    $this->view->topLevelValue = $option_id;
    $form = $this->view->form = new Sitestoreform_Form_Standard(array(
      'item' => $subject,
      'topLevelId' => 1,
      'topLevelValue' => $option_id,
    ));
		$store_owner = Engine_Api::_()->getItem('user', $owner_id);
		$this->view->show_msg = $form->show_error_msg;
    //GET THE OBJECT OF MANAGEADMIN TABLE
    $table_manageadmin =  Engine_Api::_()->getDbTable('manageadmins', 'sitestore');
    $table_manageadmin_name = $table_manageadmin->info('name'); 
		$select_manageadmin = $table_manageadmin->select()->where('store_id =?',$store_id);                                      
		$result_mangesadmin = $table_manageadmin->fetchAll($select_manageadmin);

    $count_result = count($result_mangesadmin);
    $user_id = '';
		$sendme = null;
		$viewer_name = null;
		
    $user_id.= $result_mangesadmin[0]['user_id'];
    $i = 1;
    while($i < $count_result) {
    $user_id.= ','. $result_mangesadmin[$i]['user_id'];
    $i++;
    }
   
    $table_user =  Engine_Api::_()->getDbTable('users','user');
    $user_table_name = $table_user->info('name'); 
		$select_user = $table_user->select()->where($user_table_name . '.user_id IN (' . $user_id . ')')->orWhere('user_id = ?', $owner_id);                                    
		$result_usertable = $table_user->fetchAll($select_user);
    $final_result_mail = $result_usertable->toarray();
    $email_user = array();
    foreach($final_result_mail as $key=> $mail_id) {
            $email_user[] = $mail_id['email'];
    } 
     
    //$final_result1 = $result_mangesadmin->toarray(); 
		$sender_email = $store_owner->email;
    if (!empty($viewr_id)) {
      $sendme = $viewer->email;
      $viewer_name = $viewer->displayname;
      if($select_sitestoreform_result->activeemail == 1){
				$form->sender_email->setValue("$sendme");
      }
      if($select_sitestoreform_result->activeyourname == 1){
        $form->sender_name->setValue("$viewer_name");
      }
      $form->populate($_POST);
    }
    else {
      if(!empty($_POST['sender_name'])) {
        $sendme = $_POST['sender_name'];
      }
      if(!empty($_POST['sender_email'])) {
        $viewer_name = $_POST['sender_email'];
      }
      if($select_sitestoreform_result->activeemail == 1){
        $form->sender_email->setValue("$sendme");
      }
      if($select_sitestoreform_result->activeyourname == 1){
        $form->sender_name->setValue("$viewer_name");
      }
      $form->populate($_POST);
    }
    $this->view->ispost = 0;
    $this->view->success =0;
    //Getting the tab id from the content table.
 		$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
 		$this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout,$store_id); 
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreform.sitestore-viewform', $store_id, $layout); 
    $this->view->module_tabid =  $currenttabid =  Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $isajax = $this->_getParam('isajax', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $store_id);
    if (!empty($isajax) || ($currenttabid == $this->view->identity)|| ($front->getRequest()->isPost() && empty($isajax)) || ($widgets == 0)) {
    	$this->view->identity_temp  =  Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);    	 
      $this->view->store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);
			$this->view->show_content = true;
			
			if ($front->getRequest()->isPost() && !$form->isValid($front->getRequest()->getPost()) && empty($isajax)) {

				$this->view->ispost = 1;
				$this->view->identity_temp  =  Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid); 
           
				//CLEAR THE SUBJECT OF SITESTOREFORM
				Engine_Api::_()->core()->clearSubject();
				$subject = null;
				if ( !Engine_Api::_()->core()->hasSubject() )
				{
					$subject= Engine_Api::_()->getItem('sitestore_store', $store_id);
					if ( $subject && $subject->getIdentity() )
					{
						Engine_Api::_()->core()->setSubject($subject);
					}	
				}
			}

			if ($front->getRequest()->isPost() && $form->isValid($front->getRequest()->getPost()) && empty($isajax)) {
				$this->view->identity_temp  =  Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);    
				$this->view->ispost = 1;
        
        if( $select_sitestoreform_result->activeyourname == 1){
          if (empty($_POST['sender_name'])) {
						$error = $this->view->translate('Your Name
            * Please complete this field - it is required.');
						$this->view->status = false;
						$error = Zend_Registry::get('Zend_Translate')->_($error);
						$form->getDecorator('errors')->setOption('escape', false);
						$form->addError($error);
						return;
					}
        } 
        if($select_sitestoreform_result->activeemail == 1){
					if (empty($_POST['sender_email'])) {
						$error = $this->view->translate('Your Email
            * Please complete this field - it is required.');
						$this->view->status = false;
						$error = Zend_Registry::get('Zend_Translate')->_($error);
						$form->getDecorator('errors')->setOption('escape', false);
						$form->addError($error);
						return;
					} 
        }
        
        if( $select_sitestoreform_result->activemessage == 1){
          if (empty($_POST['message'])) {
						$error = $this->view->translate('Message
            * Please complete this field - it is required.');
						$this->view->status = false;
						$error = Zend_Registry::get('Zend_Translate')->_($error);
						$form->getDecorator('errors')->setOption('escape', false);
						$form->addError($error);
						return;
					}
        } 
        
				//GET THE OBJECT OF SITESTORE TABLE
				$sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
				$values_form = $values = $_POST;
				//$sender_emails = explode(',', $values_form['sender_email']);
				//$sender_email = $values_form['sender_email'];
				$validator = new Zend_Validate_EmailAddress();
        $validator->getHostnameValidator()->setValidateTld(false);
		
				//CLEAR THE SUBJECT OF SITESTOREFORM
				Engine_Api::_()->core()->clearSubject();
				$subject = null;
				if ( !Engine_Api::_()->core()->hasSubject() )
				{
					$subject= Engine_Api::_()->getItem('sitestore_store', $store_id);
					if ( $subject && $subject->getIdentity() )
					{
						Engine_Api::_()->core()->setSubject($subject);
					}	
				}
        if($select_sitestoreform_result->activeemail == 1){
					if (!$validator->isValid($_POST['sender_email'])) {
						$form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address.'));
						return;
					}
        }
				
				$option_value = array();
        $final_result = array();
				$i = 0;
				foreach( $values as $key => $value ){   
					$parts = explode('_', $key);
					if( count($parts) != 3 ) continue;
					$form->$key->setValue('');
					list($parent_id, $option_id, $field_id) = $parts;
					if ($parts[0] == 1&& $parts[1] == $option_id ) {
						//GET THE OBJECT OF SITESTOREFORM META TABLE
						$table_option =  Engine_Api::_()->fields()->getTable('sitestoreform', 'meta');
						$table_option_name = $table_option->info('name'); 
						$select_options = $table_option->select()->where('field_id = ?',$parts[2]);
						$select_options_result =  $select_options->from($table_option->info('name'), array('type', 'label', 'field_id'));
						$result = $table_option->fetchRow($select_options_result);
						if ($result->canHaveDependents()) {
							if ( is_array($value) )  {
								$RESOURCE_TYPE_STRING="'";
								$RESOURCE_TYPE_STRING .= implode ($value, "','");
								$RESOURCE_TYPE_STRING.="'";
								$table_option =  Engine_Api::_()->fields()->getTable('sitestoreform', 'options');
								$table_option_name = $table_option->info('name'); 
								$select_options = $table_option->select();                                      
								$select_options
													->from($table_option_name,array('label', 'option_id'))
													->where($table_option_name. '.option_id IN (' . $RESOURCE_TYPE_STRING .')');
								$resultAns = $table_option->fetchAll($select_options);
								$final_result[$i] ['answer'] = $resultAns->toarray();  
							}
							else {
								//GET THE OBJECT OF SITESTOREFORM OPTION TABLE
								$table_option =  Engine_Api::_()->fields()->getTable('sitestoreform', 'options');
								$table_option_name = $table_option->info('name'); 
								$select_options = $table_option->select();
								$select_options
													->from($table_option_name,array('label', 'option_id'))
													->where($table_option_name. '.option_id =?', $value);
								$resultAns = $table_option->fetchAll($select_options);
								$final_result[$i] ['answer'] = $resultAns->toarray();
							} 
						}
						else {
							$final_result[$i] ['answer'] = $value;
						}
						$final_result[$i] ['Question'] = $result->toarray();
						if ($result->type == 'Radio') {
							$option_value[] = $value;
						}
						else {
							$option_value = $value;
						}				
					}
					$i++;
				}
        
				$i =0;
        $Question_ans = null;
				foreach ($final_result as $key2 => $values) {
					if(empty($values['answer']) && $values['Question']['type'] != 'checkbox'  )
						continue;
          if($values['Question']['type'] == 'birthdate' || $values['Question']['type'] == 'date') {
            if(empty($values['answer']['month']) && empty($values['answer']['day']) && empty( $values['answer']['year']))
            continue;
          }
         
					$Question_ans .= '<b>'.$this->view->translate('Question ') . ++$i . ': </b> ' . $values['Question']['label'] . '<br /> <b>'.$this->view->translate('Answer ').'</b>';

				if ($values['Question']['type'] == 'country'){
						$locale = Zend_Registry::get('Zend_Translate')->getLocale();
						$territories = Zend_Locale::getTranslationList('territory', $locale, 2);
              $country= $territories[$values['answer']];
							$Question_ans .= '<b>:</b>' . $country . '<br /><br/>';
					}
					elseif ($values['Question']['type'] == 'occupation'){
						foreach($occupations as $keys=> $occupation) {
							if ($keys == $values['answer']) {
								$Question_ans .= '<b>:</b>' . $occupation . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';
						
					}
					elseif ($values['Question']['type'] == 'education_level'){
						foreach($education as $keys=> $educations) {
							if ($keys == $values['answer']) {
								$Question_ans .= '<b>:</b>' . $educations . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';
						
					}
          elseif ($values['Question']['type'] == 'looking_for'){
						$k =97;
            $k = chr($k);
						$space="";
						foreach($values['answer'] as $keys=> $lookingfors) {
							if (!empty($lookingfor[$lookingfors])) {
								$Question_ans .=$space. $k++ . '<b>:</b>' . $lookingfor[$lookingfors]."<br />" ;
								$space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
						}		
							$Question_ans .= 	 '<br />';
					}
          elseif ($values['Question']['type'] == 'partner_gender'){
						$k =97;
            $k = chr($k);
            $space="";
						foreach($values['answer'] as $keys=> $interestedins) {
							if (!empty($interestedin[$interestedins])) {
								$Question_ans .= $space.$k++ . '<b>:</b>' . $interestedin[$interestedins] . '<br />';
                $space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
						}
            $Question_ans .= 	 '<br />';		
					}
						elseif ($values['Question']['type'] == 'relationship_status'){
						foreach($relationshipstatus as $keys=> $relationship) {
							if ($keys == $values['answer']) {
								$Question_ans .= '<b>:</b>' . $relationship . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';	
					}
					elseif ($values['Question']['type'] == 'weight'){
						foreach($weight as $keys=> $weighted) {
							if ($keys == $values['answer']) {
								$Question_ans .= '<b>:</b>' . $weighted . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';	
						
					}
					elseif ($values['Question']['type'] == 'religion'){
						foreach($religion as $keys=> $religions) {
							if ($keys == $values['answer']) {
								$Question_ans .= ':' . $religions . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';			
					}
					elseif ($values['Question']['type'] == 'political_views'){
						foreach($political_views as $keys=> $political_view) {
							if ($keys == $values['answer']) {
								$Question_ans .= '<b>:</b>' . $political_view . '<br />';
							}
						}
            $Question_ans .= 	 '<br />';		
					}
          elseif ($values['Question']['type'] == 'checkbox'){
					  if(!empty($values['answer']))
						$Question_ans .= '<b>:</b>' . 'Yes' . '<br /><br />';	
						else
						$Question_ans .= '<b>:</b>' . 'No' . '<br /><br />';	
					}
          elseif ($values['Question']['type'] == 'income'){
					  foreach(	$incomesource as $keys=> 	$income) {
						  if ($keys == $values['answer']) {
							  $Question_ans .= '<b>:</b>' . $income . '<br /><br />';
						  }
					  }
            $Question_ans .= 	 '<br />';				
				  }
					elseif ($values['Question']['type'] == 'ethnicity'){
						$k =97;
            $k = chr($k);
            $space= 	 '';
						foreach($values['answer'] as $keys=> $Ethnicities) {
							if (!empty($Ethnicity[$Ethnicities])) {
								$Question_ans .= $space.$k++ . '<b>:</b>' . $Ethnicity[$Ethnicities] . '<br />';
                $space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
						}		
            $Question_ans .= 	 '<br />';
					}
					elseif (is_array($values['answer'][0])&& !empty($values['answer'][0]))  {
						$j =97;
            $j = chr($j);
            $space= 	 '';
						$count = count($values['answer']);
						foreach($values['answer'] as   $k => $value) {
							if ($count> 1){
								$Question_ans .= $space.$j++ . '<b>:</b>' . $value['label'] . '<br/>';
                $space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							else {
								$Question_ans .= '<b>:</b>' . $value['label'] . '<br/>' ;
							}
						}
            $Question_ans .= 	 '<br />';
					}
					elseif(!is_array($values['answer'])&& !empty($values['answer'])) {
						$Question_ans .= '<b>:</b>' . $values['answer'] . '<br /><br />';
					}
					elseif ($values['Question']['type'] == 'birthdate'){
            $date = $values['answer']['year']  . '-'. $values['answer']['month']. '-'  .$values['answer']['day']. ' 00:00:00' ;
						if(!empty($values['answer']['year'] ))
						  $Question_ans .= '<b>:</b>' .gmdate('M d,Y',strtotime($date))  .  '<br /><br />';
						else
						  $Question_ans .= '<b>:</b>' .gmdate('M d',strtotime($date))  .  '<br /><br />';
					}
					elseif ($values['Question']['type'] == 'date'){
            $date = $values['answer']['year']  . '-'. $values['answer']['month']. '-'  .$values['answer']['day']. ' 00:00:00' ;
						if(!empty($values['answer']['year'] ))
						  $Question_ans .= '<b>:</b>' .gmdate('M d,Y',strtotime($date))  .  '<br /><br />';
						else
						  $Question_ans .= '<b>:</b>' .gmdate('M d',strtotime($date))  .  '<br /><br />';
					}
					elseif (is_array($values['answer'])&& !empty($values['answer']))  {
					$j =97;
          $j = chr($j);
          $space = '';
						$count = count($values['answer']);
						foreach($values['answer'] as   $k => $value) {
							if ($count> 1){
								$Question_ans .= $space.$j++ . '<b>:</b>' . $value ;
                $space="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							else {
								$Question_ans .= '<b>:</b>' . $value  ;
							}
						}
          $Question_ans .= 	 '<br />';
					}
				}
        $message = '';
        if(!empty($Question_ans)){
          $message = $this->view->translate('Below, you can see the answers submitted by the visitor.');
        }
        $sendme = $values_form['sender_email'];
        if($select_sitestoreform_result->activemessage == 1) {
					$message .= $this->view->translate('The visitor has also sent a message:');
        }
				if($select_sitestoreform_result->activemessage == 1 || (count($Question_ans > 0))) {
					$message .= '<br /><br />';
				}
				if($select_sitestoreform_result->activemessage == 1) {
					$message .= $this->view->translate('Message'. ':<br />'. $values_form['message']. '<br/><br/>');
				}

				$message .= $Question_ans;
				$sender = '';
        if($select_sitestoreform_result->activeyourname == 1) {
          $sender = $this->view->translate('Name').': ';
          $sender.= $values_form['sender_name'];
        }
        else {
         $sender= '';
        }
        if($select_sitestoreform_result->activeemail == 1) {
          $sendme = $this->view->translate('Email').': ';
          $sendme.=  $values_form['sender_email'];
       }
        else {
         $sendme= '';
        }
        $sendme_viewer =  $values_form['sender_email'];
				if( !empty($sitestoreform_mail) && !empty($getPackageFormMail) ) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($email_user, 'SITESTOREFORM_QUESTION_EMAIL', array(
						'host' => $_SERVER['HTTP_HOST'],
						'sender_name' => $sender,
						'sender_email' => $sendme,
						'store_name'=> $store_name,
						'heading' => '',
						'message' =>  $message,
						'object_link' => Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $viewr_id, $sitestore->getSlug()),
						'email' => $sender_email,
						'queue' => false
					));
				}

				// send copy of email also sender if he wants
				if  ( (!empty($values_form['send_me'])) && !empty($sitestoreform_mail) && !empty($getPackageFormMail) ) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($sendme_viewer, 'SITESTOREFORM_QUESTION_EMAIL', array(
						'host' => $_SERVER['HTTP_HOST'],
						'sender_name' => $sender,
						'sender_email' => $sendme,
						'store_name'=> $store_name,
						'heading' => '',
						'message' => $message,
						'object_link' => Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()),
						'email' => $sendme,
						'queue' => false
					));
				} 
        $this->view->success = 1;//$form->addNotice(Zend_Registry::get('Zend_Translate')->_(''));
        $request = Zend_Controller_Front::getInstance()->getRequest();
				if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
					$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreform.sitestore-viewform', $store_id, 1);
					$request->setParams(array(
					'parentRedirect' => $sitestore_object->getHref(array('tab' => $tab_id)),
					'parentRedirectTime' => '1',
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('Thank you for sending us the details.')
					)));
					$request->setControllerName('utility');
					$request->setModuleName('core');
					$request->setActionName('success');
					if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
					$sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
					}
					$request->setDispatched(false);
				}
			} 
		
			//CLEAR THE SUBJECT OF SITESTOREFORM
			Engine_Api::_()->core()->clearSubject();
			$subject = null;
			if ( !Engine_Api::_()->core()->hasSubject() )
			{
				$subject= Engine_Api::_()->getItem('sitestore_store', $store_id);
				if ( $subject && $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}	
			}
    }
    else { 
       //CLEAR THE SUBJECT OF SITESTOREFORM
		  Engine_Api::_()->core()->clearSubject();
			$subject = null;
			if ( !Engine_Api::_()->core()->hasSubject() )
			{
				$subject= Engine_Api::_()->getItem('sitestore_store', $store_id);
				if ( $subject && $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}	
			} 
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
    }
    if(!empty($form->show_error_msg) && ($owner_id != $viewr_id) && empty($isManageAdmin)) {
      return $this->setNoRender();
    }
  }
}
?>
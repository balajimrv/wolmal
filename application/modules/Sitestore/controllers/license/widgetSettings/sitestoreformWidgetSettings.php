<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestoreformWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
//END LANGUAGE WORK
//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();	

//CHECK THAT SITESTORE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
	$select
  ->from('engine4_core_settings')
  ->where('name = ?', 'sitestore.is.active')
	->limit(1);
$sitestore_settings = $select->query()->fetchAll();
if(!empty($sitestore_settings)) {
	$sitestore_is_active = $sitestore_settings[0]['value'];
}
else {
	$sitestore_is_active = 0;
}

//CHECK THAT SITESTORE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
	$select
	  ->from('engine4_core_modules')
  ->where('name = ?', 'sitestore')
	->where('enabled = ?', 1);
$check_sitestore = $select->query()->fetchObject();
if(!empty($check_sitestore)  && !empty($sitestore_is_active)) {
	$select = new Zend_Db_Select($db);
	$select_store = $select
										 ->from('engine4_core_pages', 'page_id')
										 ->where('name = ?', 'sitestore_index_view')
										 ->limit(1);
  $store = $select_store->query()->fetchAll();
	if(!empty($store)) {
		$store_id = $store[0]['page_id'];
		
		//INSERTING THE FORM WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'true', '114');			
	 
		//INSERTING THE FORM WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'true', '114');
		
    //INSERTING THE FORM WIDGET IN SITESTORE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
    foreach ($contentstore_ids as $contentstore_id) {
			if(!empty($contentstore_id)) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreform.sitestore-viewform', $contentstore_id['contentstore_id'], 'Form', 'true', '114');
			}
		}
	}	
}  
	  
$select = new Zend_Db_Select($db);
$content = $select->from('engine4_sitestoreform_storequetions')->query()->fetchAll();		
if(empty($content)) {
	$select = new Zend_Db_Select($db);
	$sitestore_result = $select->from('engine4_sitestore_stores')->query()->fetchAll();
	if(!empty($sitestore_result)) {
		foreach ($sitestore_result as $key => $value) {
			$store_id = $value['store_id'];
			$values = $value ['title'];
			$db->insert('engine4_sitestoreform_fields_options', array(
										'field_id' => 1,
										'label' => $values,
			));
		$option_id =  $db->lastInsertId('engine4_sitestoreform_fields_options');
		$select = new Zend_Db_Select($db);
		$select_content = $select
														->from('engine4_sitestoreform_storequetions')
														->where('store_id = ?', $store_id)
														->where('option_id = ?', $option_id)
														->limit(1);
		$content = $select_content->query()->fetchAll();
		if( empty($content) ) {
				$db->insert('engine4_sitestoreform_storequetions', array(
					'store_id' => $store_id,
					'option_id' => $option_id,
				));
			}

		$db->insert('engine4_sitestoreform_sitestoreforms', array(
										'store_id' => $store_id,
										));
		}
	}
}
//CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
$type_array = $db->query('SHOW COLUMNS FROM engine4_authorization_permissions LIKE \'type\'')->fetch();
if(!empty($type_array)) {
	$varchar = $type_array['Type'];
	$length_varchar = explode('(', $varchar);
	$length = explode(')', $length_varchar[1]);
	$length_type = $length[0];
	if($length_type < 32) 	{
		$run_query  = $db->query('ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL');
	}	
}

?>
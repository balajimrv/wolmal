<?php
class Sesbasic_TooltipController extends Core_Controller_Action_Standard {
	public function indexAction(){
		$guid = $this->_getParam('guid',false);
		if(!$guid)
		return;
		$this->view->subject = $subject =	Engine_Api::_()->getItemByGuid($guid);

		if(!$subject)
		return;
		$settings = Engine_Api::_()->getApi('settings', 'core');

		if($subject->getType() == 'user' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesmember')) {
			$this->view->globalEnableTip = $settings->getSetting('sesbasic_settings_tooltip',array('title','mainphoto','coverphoto'));
			$this->view->moduleEnableTip = $settings->getSetting($subject->getType().'_settings_tooltip',array('title','mainphoto','coverphoto', 'socialshare','location', 'friendCount', 'mutualFriendCount', 'likeButton', 'message', 'view', 'like', 'follow', 'friendButton', 'age', 'profileType', 'email', 'rating'));
			$this->renderScript('tooltip/member-data.tpl');
		}
		elseif($subject->getType() == 'sesblog_blog') {
			$this->view->globalEnableTip = $settings->getSetting('sesbasic_settings_tooltip',array('title','mainphoto'));
			$this->view->moduleEnableTip = $settings->getSetting($subject->getType().'_settings_tooltip',array('title','mainphoto', 'socialshare','location','view', 'like', 'rating'));
			$this->renderScript('tooltip/blog-data.tpl');
		}
		else if($subject->getType() == 'sesevent_event'){	
			$this->view->globalEnableTip = $settings->getSetting('sesbasic_settings_tooltip',array('title','mainphoto','coverphoto','category'));
			$this->view->moduleEnableTip = $settings->getSetting($subject->getType().'_settings_tooltip',array('title','mainphoto','coverphoto','category','socialshare','location','hostedby','startendtime','buybutton'));
		}else{
      $this->view->globalEnableTip = $settings->getSetting('sesbasic_settings_tooltip',array('title','mainphoto'));
			$this->view->moduleEnableTip = $settings->getSetting($subject->getType().'_settings_tooltip',array('title','mainphoto', 'socialshare','comment','view', 'like'));
      //common tooltip  
      $this->renderScript('tooltip/basic-data.tpl');
    }
	}
}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Api_Siteapi_Core extends Core_Api_Abstract {
    
    public function init()
    {
        
    }
    
    public function guttermenu($subject)
    {
        $menu = array();
        
        if( $subject->status )
        {
            $menu[] = array(
                'name' => 'disable',
                'label' => $this->translate('Disable Coupon'),
                'url' => 'sitestore/offer/enable/'.$subject->getIdentity(),
            );
        }
        else
        {
            $menu[] = array(
                'name' => 'enable',
                'label' => $this->translate('enable'),
                'url' => 'sitestore/offer/enable/'.$subject->getIdentity(),
            );
        }
        
        // Delete coupon 
        $menu[] = array(
            'name' => 'delete',
            'label' => $this->translate('Delete Coupon'),
            'url' => 'sitestore/offer/delete/'.$subject->getIdentity(),
        );
        
        return $menu;
        
    }
    
    
    /**
     * Translate the text from english to specified language by user
     *
     * 	@param message string
     *   @return string
     */
    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }
    
}
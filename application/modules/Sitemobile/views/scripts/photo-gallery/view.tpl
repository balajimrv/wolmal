<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
if (Engine_API::_()->sitemobile()->isApp()) : 
    include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/photo-gallery/sitemobileapp.tpl';
    else:
    include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/photo-gallery/sitemobile.tpl';
endif;
?>

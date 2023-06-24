<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php

$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title'));
$logo = $this->logo;
$height = $this->height;
$width = $this->width;
$alignment = $this->alignment;
$route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');

echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $title), array('height' => $height, 'width' => $width, 'align' => $alignment))) : $this->htmlLink($route, $title);


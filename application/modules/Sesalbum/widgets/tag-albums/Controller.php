<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_tagAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
			$this->view->tagCloudData  = Engine_Api::_()->sesalbum()->tagCloudItemCore('fetchAll');
			// Do not render if nothing to show
			if( count($this->view->tagCloudData) <= 0 )
				return $this->setNoRender();
	}
}

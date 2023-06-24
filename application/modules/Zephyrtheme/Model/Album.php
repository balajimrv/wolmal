<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zephyrtheme_Model_Album extends Core_Model_Item_Abstract implements Countable
{
    protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    protected $_parent_is_owner = true;

    public function getHref($params = array())
    {
        /**
         * TODO: Add custom Route for Album View
         */

        return '#';
    }

    public function count()
    {
        $photoTable = Engine_Api::_()->getItemTable('zephyrtheme_photo');
        return $photoTable->select()
            ->from($photoTable, new Zend_Db_Expr('COUNT(photo_id)'))
            ->where('album_id = ?', $this->getIdentity())
            ->limit(1)
            ->query()
            ->fetchColumn();
    }

    protected function _postDelete()
    {
        $photoTable = Engine_Api::_()->getItemTable('zephyrtheme_photo');
        $photoSelect = $photoTable->select()
            ->where('album_id = ?', $this->getIdentity())
        ;
        foreach( $photoTable->fetchAll($photoSelect) as $photo ) {
            try {
                $photo->delete();
            } catch( Exception $e ) {
                // Silence
            }
        }

        parent::_postDelete();
    }
}

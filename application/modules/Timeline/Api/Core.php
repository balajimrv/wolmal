<?php

class Timeline_Api_Core extends Core_Api_Abstract {

    protected $_cover = array();
    protected $_default_cover;

    public function getTimeLineCover(User_Model_User $user) {
        $file = $this->initUserCover($user);
        if ($file !== null) {
            return $file->map();
        }
        return $this->getDefaultTimeLineCover();
    }

    public function hasTimeLineCover(User_Model_User $user) {
        return (bool) $this->initUserCover($user);
    }

    public function getDefaultTimeLineCover() {
        if ($this->_default_cover == null) {
            $cover = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'system',
                                                                                   'type = ?' => 'cover.default'));
            if ($cover == null) {
                $this->_default_cover = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Timeline/externals/images/cover_timeline.png';
            }
            else {
                $this->_default_cover = $cover->map();
            }
        }

        return $this->_default_cover;
    }
    
    protected function initUserCover(User_Model_User $user) {
        $identity = $user->getIdentity();
        if (!isset ($this->_cover[$identity])) {
            $this->_cover[$identity] = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'user',
                                                                                                     'parent_id = ?' => $identity,
                                                                                                     'user_id = ?' => $identity,
                                                                                                     'type = ?' => 'cover.timeline'));
        }
        return $this->_cover[$identity];
    }
}

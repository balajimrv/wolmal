<?php
class Zephyrtheme_Model_DbTable_Albums extends Engine_Db_Table
{
    protected $_rowClass = 'Zephyrtheme_Model_Album';

    public function getCoverPhoto(User_Model_User $user)
    {
        $album = $this->getCoverAlbum($user);

        if ($album->cover_id)
        {
            $photo = Engine_Api::_()->getItem("zephyrtheme_photo", $album->cover_id);
            return $photo;
        }

        return null;
    }

    public function getCoverAlbum(User_Model_User $user)
    {
		$album = $this->getAlbum(array(
            'owner' => $user,
            'title' => 'Profile Cover'
        ));

        if (!empty($album))
        {
            return $album;
        }

        // Create album
        $album = $this->createRow();
        $album->setFromArray(array(
            'owner_id' => $user->getIdentity(),
            'owner_type' => $user->getType(),
            'title' => 'Profile Cover'
        ));
        $album->save();

        return $album;
    }

    public function getAlbum($options = array())
    {
        $select = $this->select()->limit(1);
        if (!empty($options['owner']) && $options['owner'] instanceof Core_Model_Item_Abstract)
        {
            $select->where('owner_type = ?', $options['owner']->getType())
                ->where('owner_id = ?', $options['owner']->getIdentity());
        }

        if (!empty($options['title']))
        {
            $select->where('title = ?', $options['title']);
        }

        return $this->fetchRow($select);
    }
}
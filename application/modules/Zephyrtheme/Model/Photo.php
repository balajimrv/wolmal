<?php
class Zephyrtheme_Model_Photo extends Core_Model_Item_Abstract
{
    protected $_searchTriggers = array('file_id');

    public function getHref($params = array())
    {
        return Engine_Api::_()->user()->getUser($this->getAlbum()->owner_id)->getHref();
    }

    public function getAlbum()
    {
        return Engine_Api::_()->getItem('zephyrtheme_album', $this->album_id);
    }

    public function getParent($type = null)
    {
        if (null === $type || $type === 'album')
        {
            return $this->getAlbum();
        } else {
            return $this->getAlbum()->getParent($type);
        }
    }

    public function getPhotoUrl($type = null)
    {
        $photo_id = $this->file_id;
        if (!$photo_id)
        {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id);
        if (!$file)
        {
            return null;
        }

        return $file->map();
    }

    public function setPhoto($photo)
    {
        if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if(is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Engine_Exception('Invalid Upload or file too large');
        }

        if (!$fileName)
        {
            $fileName = $file;
        }

        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $cover_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('profile_cover_width', '1100');

        // Store
        try {
            if ($image->getWidth() < $cover_width)
            {
                throw new Engine_Exception("Please choose an image that's at least 1100px width.");
            }

            $image->write($mainPath)->destroy();

            $viewer = Engine_Api::_()->user()->getViewer();
            $iMain = $filesTable->createFile($mainPath, array(
                'parent_type' => $this->getType(),
                'parent_id' => $this->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'name' => $fileName
            ));
        } catch (Engine_Exception $e) {
            // Remove temp files
            @unlink($mainPath);

            // Throw
            if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
                throw new Engine_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }

        // Remove temp files
        @unlink($mainPath);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->file_id = $iMain->file_id;
        $this->save();

        return $this;
    }
}
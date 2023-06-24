<?php

class Whcore_ThumbController extends Core_Controller_Action_Standard {

    public function indexAction() {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        $src = $this->_getParam('src');
        if (null === $src) {
            die("No source image");
        }

        //width
        $width = (int) $this->_getParam('w');
        //height
        $height = (int) $this->_getParam('h');
        //quality    
        $quality = (int) $this->_getParam('q', 95);

        //crop_zoom    
        $crop_zoom = (int) $this->_getParam('cz', 1);

        $sourceFilename = str_replace(' ', '', strip_tags($src));
        if (empty($sourceFilename))
            die("Couldn't read source image.");
        try {
            $file = @fopen($sourceFilename, "r");
        } catch (Exception $e) {
            die("Couldn't read source image " . $sourceFilename);
        }

        if ($file) {
            fclose($file);
            include 'application/modules/Whcore/Library/phpThumb/phpthumb.class.php';

            $phpThumb = new phpThumb();

            $phpThumb->src = $sourceFilename;
            $phpThumb->w = $width;
            $phpThumb->h = $height;
            $phpThumb->q = $quality;
            $phpThumb->zc = $crop_zoom;
            $phpThumb->bg = 'FFFFFF';

            $phpThumb->config_allow_src_above_docroot = false;
            $phpThumb->config_imagemagick_path = '/usr/bin/convert';
            $phpThumb->config_prefer_imagemagick = true;
            $phpThumb->config_output_format = 'jpg';
            $phpThumb->config_error_die_on_error = true;
            $phpThumb->config_document_root = $_SERVER['DOCUMENT_ROOT'];
            $phpThumb->config_temp_directory = 'temporary/whcore_tmp/';
            $phpThumb->config_cache_directory = 'temporary/whcore_cache/';
            $phpThumb->config_cache_disable_warning = true;
            $phpThumb->nohotlink_enabled = false;
            $cacheFilename = md5($_SERVER['REQUEST_URI']);

            $phpThumb->cache_filename = $phpThumb->config_cache_directory . $cacheFilename;

            if (!is_dir($phpThumb->config_temp_directory)) {
                if (!mkdir($phpThumb->config_temp_directory, 0777, true)) {
                    throw new Exception('Thumb temporary directory did not exist and could not be created.');
                }
            }
            if (!is_dir($phpThumb->config_cache_directory)) {
                if (!mkdir($phpThumb->config_cache_directory, 0777, true)) {
                    throw new Exception('Thumb temporary directory did not exist and could not be created.');
                }
            }

            if (!is_file($phpThumb->cache_filename)) { // If Image was not cached - resize it.
                if ($phpThumb->GenerateThumbnail()) {
                    $phpThumb->RenderToFile($phpThumb->cache_filename);
                } else {
                    die('Failed: ' . $phpThumb->error);
                }
            }

            if (is_file($phpThumb->cache_filename)) { // If thumb was already generated we want to use cached version
                $cachedImage = getimagesize($phpThumb->cache_filename);
                header('Content-Type: ' . $cachedImage['mime']);
                readfile($phpThumb->cache_filename);
                exit;
            }
        } else { // Can't read source
            die("Couldn't read source image " . $sourceFilename);
        }
    }

}
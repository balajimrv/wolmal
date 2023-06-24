<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'sitestorevideo_video_show_menu', array(
        'label' => 'Videos Link',
        'description' => 'Do you want to show the Videos link on Stores Navigation Menu? (You might want to show this if Videos from Stores are an important component on your website. This link will lead to a widgetized store listing all Store Videos, with a search form for Store Videos and multiple widgets.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.video.show.menu', 1),
    ));

    $this->addElement('Text', 'sitestorevideo_ffmpeg_path', array(
        'label' => 'Path to FFMPEG',
        'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.ffmpeg.path', ''),
    ));

    $this->addElement('Checkbox', 'sitestorevideo_html5', array(
      'description' => 'HTML5 Video Support',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.html5', false),
    ));
    
    $description = 'While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our <a href="http://support.socialengine.com/php/customer/portal/articles/2018371-create-your-youtube-api-key" target="_blank">KB Article</a>';

    $currentYouTubeApiKey = '******';
    if( !_ENGINE_ADMIN_NEUTER ) {
      $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    }
    $this->addElement('Text', 'video_youtube_apikey', array(
      'label' => 'YouTube API Key',
      'description' => $description,
      'filters' => array(
        'StringTrim',
      ),
      'value' => $currentYouTubeApiKey,
    ));
    $this->video_youtube_apikey->getDecorator('Description')->setOption('escape', false);
        
    $this->addElement('Text', 'sitestorevideo_jobs', array(
        'label' => 'Encoding Jobs',
        'description' => 'How many jobs do you want to allow to run at the same time?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.jobs', 2),
    ));

    $this->addElement('Radio', 'sitestorevideo_embeds', array(
        'label' => 'Allow Embedding of Videos?',
        'description' => 'Enabling this option will give members the ability to embed videos on this site in other stores using an iframe (like YouTube).',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.embeds', 1),
        'multiOptions' => array(
            '1' => 'Yes, allow embedding of videos.',
            '0' => 'No, do not allow embedding of videos.',
        ),
    ));

    // Order of store video store
    $this->addElement('Radio', 'sitestorevideo_order', array(
        'label' => 'Default Ordering in Store Videos listing',
        'description' => 'Select the default ordering of videos in Store Videos listing. (This widgetized store will list all Store Videos. Sponsored videos are videos created by paid Stores.)',
        'multiOptions' => array(
            1 => 'All videos in descending order of creation.',
            2 => 'All videos in alphabetical order.',
            3 => 'Featured videos followed by others in descending order of creation.',
            4 => 'Sponsored videos followed by others in descending order of creation.(If you have enabled packages.)',
            5 => 'Featured videos followed by sponsored videos followed by others in descending order of creation.(If you have enabled packages.)',
            6 => 'Sponsored videos followed by featured videos followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.order', 1),
    ));

     $this->addElement('Radio', 'sitestorevideo_featured', array(
        'label' => 'Making Store Videos Highlighted',
        'description' => 'Allow Store Admins to make videos in their Stores as highlighted.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.featured', 1),
    ));

    $this->addElement('Text', 'sitestorevideo_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'maxlength' => 3,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

			$this->addElement('Text', 'sitestorevideo_manifestUrl', array(
        'label' => 'Store Videos URL alternate text for "store-videos"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "storevideos" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.manifestUrl', "store-videos"),
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: General.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Video_General extends Engine_Form {

  protected $_field;

  public function init() {

    $this->setTitle('General Setting')
            ->setDescription('Below, you can configure the settings for the videos uploaded in products of this plugin.');

    $videoModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');

    if ($videoModuleEnabled) {
      $this->addElement('Radio', 'sitestoreproduct_show_video', array(
          'label' => 'Plugin for Videos',
          'description' => 'Select from below the plugin which you want to enable for the videos uploaded in products of this plugin.',
          'multiOptions' => array(
              1 => "Official SocialEngine Videos Plugin (Note: If enabled, video settings will be inherited from the 'Videos' plugin and videos uploaded in products will be displayed on 'Video Browse Page' and 'Products Profile' pages.)",
              0 => "Inbuilt Videos (Videos uploaded in the products will only be displayed on Product Profile pages and will have their own widgetized Video View page.)"
          ),
          'onclick' => 'showDefaultVideo(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1),
      ));
    }

    $this->addElement('Text', 'sitestoreproduct_video_ffmpeg_path', array(
        'label' => 'Path to FFMPEG',
        'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.ffmpeg.path', ''),
    ));

      $this->addElement('Checkbox', 'sitestoreproductvideo_html5', array(
          'description' => 'HTML5 Video Support',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproductvideo.html5', false),
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

    $this->addElement('Text', 'sitestoreproduct_video_jobs', array(
        'label' => 'Encoding Jobs',
        'description' => 'How many jobs do you want to allow to run at the same time?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.jobs', 2),
    ));

    $this->addElement('Radio', 'sitestoreproduct_video_embeds', array(
        'label' => 'Allow Embedding of Videos?',
        'description' => 'Enabling this option will give members the ability to embed videos on this site in other products using an iframe (like YouTube).',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.embeds', 1),
        'multiOptions' => array(
            '1' => 'Yes, allow embedding of videos.',
            '0' => 'No, do not allow embedding of videos.',
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
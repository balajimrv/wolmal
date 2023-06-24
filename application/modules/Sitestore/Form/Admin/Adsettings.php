<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitestore_Form_Admin_Adsettings extends Engine_Form {

  public function init() {

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      $this->addElement('Dummy', 'note', array(
          'description' => '<div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->_('This plugin provides deep integration for advertising using the "%1$sAdvertisements / Community Ads Plugin%2$s". Please install and enable this plugin to configure settings for the various ad positions and widgets available. If you do not have this plugin yet, click here to view its details and purchase it: %1$shttp://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin%2$s.'), '<a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">', '</a>') . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
          ))
      ));
    }

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if ($enable_ads) {
      $this
              ->setTitle('Ad Settings')
              ->setDescription('This plugin provides seamless integration with the "Advertisements / Community Ads Plugin". Attractive advertising can be done using the many available, well designed ad positions in this plugin. Below, you can configure the settings for the various ad positions and widgets.');
      $this->addElement('Radio', 'sitestore_adpreview', array(
          'label' => 'Sample Ad Widget',
           'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show a Sample Ad of a Store on its profile in the Info widget? (This widget will only be visible to store admins and will tempt them to create an Ad for their Store. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/ad_preview.png\');"></a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpreview', 1),
      ));
      $this->sitestore_adpreview->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitestore_adcreatelink', array(
          'label' => 'Advertise your Store Widget',
          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show the Advertise your Store widget on Store Profile in the Info widget? (This widget will only be visible to store admins and it displays a catchy phrase to tempt them to create an Ad for their Store. It also has a link to Create an Ad. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitestore/externals/images/ad_content.png\');">here</a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adcreatelink', 1),
      ));
      $this->sitestore_adcreatelink->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitestore_communityads', array(
          'label' => 'Community Ads in this plugin',
          'description' => 'Do you want to show community ads in the various positions available in this plugin? (Below, you will be able to choose for every individual position. If you do not want to show ads in a particular position, then please enter the value "0" for it below.). If you have enabled Packages for Stores from Global Settings, then for each package, you can configure if community ads display should be enabled on their Stores (You can use this to give extra privileges to Stores of special / paid packages by making them free of ads display.).',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showads(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1),
      ));

//       if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
//         $lightbox_photos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.show', 1);
//         if (!empty($lightbox_photos)) {
//           $this->addElement('Radio', 'sitestore_lightboxads', array(
//               'label' => 'Ads in Photos Lightbox',
//               'description' => 'Do you want to show ads in ajax lightbox for viewing photos of albums of stores?',
//               'multiOptions' => array(
//                   1 => 'Yes',
//                   0 => 'No'
//               ),
//               'onclick' => 'showlightboxads(this.value)',
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lightboxads', 1),
//           ));
// 
//           $this->addElement('Radio', 'sitestore_adtype', array(
//               'label' => 'Type of Ads in Photos Lightbox',
//               'description' => 'Select the type of ads you want to show in the ajax lightbox for viewing photos of albums of stores.',
//               'multiOptions' => array(
//                   3 => 'All',
//                   2 => 'Sponsored Ads',
//                   1 => 'Featured Ads',
//                   0 => 'Both Sponsored and Featured Ads'
//               ),
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adtype', 3),
//           ));
//         }
//       }

      $this->addElement('Text', 'sitestore_admylikes', array(
          'label' => 'Stores I Like Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store i like store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admylikes', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adnotewidget', array(
          'label' => 'Store Profile Notes Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile notes widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotewidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adnoteview', array(
          'label' => 'Store Notes View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnoteview', 3),
      ));

      $this->addElement('Text', 'sitestore_adnotebrowse', array(
          'label' => 'Store Notes Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotebrowse', 3),
      ));

      $this->addElement('Text', 'sitestore_adnotecreate', array(
          'label' => 'Store Note\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotecreate', 3),
      ));

      $this->addElement('Text', 'sitestore_adnoteedit', array(
          'label' => 'Store Note\'s Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnoteedit', 3),
      ));

      $this->addElement('Text', 'sitestore_adnotedelete', array(
          'label' => 'Store Note\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotedelete', 1),
      ));


      $this->addElement('Text', 'sitestore_adnoteaddphoto', array(
          'label' => 'Store Note\'s Add Photos Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s add photos store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnoteaddphoto', 1),
      ));

      $this->addElement('Text', 'sitestore_adnoteeditphoto', array(
          'label' => 'Store Note\'s Edit Photo Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s edit photo store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnoteeditphoto', 5),
      ));

      $this->addElement('Text', 'sitestore_adnotesuccess', array(
          'label' => 'Store Note\'s Creation Success Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store note\'s creation success store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotesuccess', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adeventwidget', array(
          'label' => 'Store Profile Events Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile events widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adeventview', array(
          'label' => 'Store Event\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventview', 2),
      ));

      $this->addElement('Text', 'sitestore_adeventbrowse', array(
          'label' => 'Store Event\'s Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventbrowse', 2),
      ));

      $this->addElement('Text', 'sitestore_adeventcreate', array(
          'label' => 'Store Event\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventcreate', 2),
      ));

      $this->addElement('Text', 'sitestore_adeventedit', array(
          'label' => 'Store Event\'s Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventedit', 2),
      ));

      $this->addElement('Text', 'sitestore_adeventdelete', array(
          'label' => 'Store Event\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventdelete', 1),
      ));
        $this->addElement('Text', 'sitestore_adeventaddphoto', array(
          'label' => 'Store Event\'s Add Photos Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s add photos store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventaddphoto', 1),
      ));

      $this->addElement('Text', 'sitestore_adeventeditphoto', array(
          'label' => 'Store Event\'s Edit Photo Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store event\'s edit photo store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventeditphoto', 5),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adalbumwidget', array(
          'label' => 'Store Profile Albums Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile albums widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adalbumview', array(
          'label' => 'Store Album\'s Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store album\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3),
      ));

      $this->addElement('Text', 'sitestore_adalbumbrowse', array(
          'label' => 'Store Album\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store album\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumbrowse', 3),
      )); 

      $this->addElement('Text', 'sitestore_adalbumcreate', array(
          'label' => 'Store Album\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store album\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumcreate', 2),
      ));

      $this->addElement('Text', 'sitestore_adalbumeditphoto', array(
          'label' => 'Store Album\'s Edit Photos Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store album\'s edit photos store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumeditphoto', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_addicussionwidget', array(
          'label' => 'Store Profile Discussions Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile discussions widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addicussionwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_addiscussionview', array(
          'label' => 'Store Discussion\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store dicussion\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addiscussionview', 2),
      ));

      $this->addElement('Text', 'sitestore_addiscussioncreate', array(
          'label' => 'Store Discussion\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store dicussion\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addiscussioncreate', 2),
      ));

      $this->addElement('Text', 'sitestore_addiscussionreply', array(
          'label' => 'Store Discussion\'s Post Reply Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a dicussion\'s post reply store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addiscussionreply', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_addocumentwidget', array(
          'label' => 'Store Profile Documents Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile documents widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_addocumentview', array(
          'label' => 'Store Document\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Store document\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentview', 2),
      ));

      $this->addElement('Text', 'sitestore_addocumentbrowse', array(
          'label' => 'Store Document\'s Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Store document\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentbrowse', 2),
      ));

      $this->addElement('Text', 'sitestore_addocumentcreate', array(
          'label' => 'Store Document\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Store document\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentcreate', 4),
      ));

      $this->addElement('Text', 'sitestore_addocumentedit', array(
          'label' => 'Store Document\'s Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Store document\'s edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentedit', 4),
      ));

      $this->addElement('Text', 'sitestore_addocumentdelete', array(
          'label' => 'Store Document\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Store document\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentdelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_advideowidget', array(
          'label' => 'Store Profile Videos Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile videos widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideowidget', 3),
      ));

      $this->addElement('Text', 'sitestore_advideoview', array(
          'label' => 'Store Video\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store video\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideoview', 2),
      ));

      $this->addElement('Text', 'sitestore_advideobrowse', array(
          'label' => 'Store Video\'s Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store video\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideobrowse', 2),
      ));

      $this->addElement('Text', 'sitestore_advideocreate', array(
          'label' => 'Store Video\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store video\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideocreate', 2),
      ));

      $this->addElement('Text', 'sitestore_advideoedit', array(
          'label' => 'Store Video\'s Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store video\'s edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideoedit', 2),
      ));

      $this->addElement('Text', 'sitestore_advideodelete', array(
          'label' => 'Store Video\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store video\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideodelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adpollwidget', array(
          'label' => 'Store Profile Polls Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile polls widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adpollview', array(
          'label' => 'Store Poll\'s View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store poll\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollview', 2),
      ));

      $this->addElement('Text', 'sitestore_adpollbrowse', array(
          'label' => 'Store Poll\'s Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store poll\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollbrowse', 2),
      ));

      $this->addElement('Text', 'sitestore_adpollcreate', array(
          'label' => 'Store Poll\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store poll\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollcreate', 2),
      ));

      $this->addElement('Text', 'sitestore_adpolldelete', array(
          'label' => 'Store Poll\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store poll\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpolldelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_admemberwidget', array(
          'label' => 'Store Profile Members Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile members widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admemberwidget', 3),
      ));
      $this->addElement('Text', 'sitestore_admemberbrowse', array(
          'label' => 'Store Members Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store member\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admemberbrowse', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adreviewwidget', array(
          'label' => 'Store Profile Reviews Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile reviews widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adreviewcreate', array(
          'label' => 'Store Review\'s Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store review\'s create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewcreate', 2),
      ));

      $this->addElement('Text', 'sitestore_adreviewedit', array(
          'label' => 'Store Review\'s Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store review\'s edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewedit', 2),
      ));

      $this->addElement('Text', 'sitestore_adreviewdelete', array(
          'label' => 'Store Review\'s Delete Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store review\'s delete store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewdelete', 1),
      ));

      $this->addElement('Text', 'sitestore_adreviewview', array(
          'label' => 'Store Reviews Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store review\'s browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewview', 2),
      ));

      $this->addElement('Text', 'sitestore_adreviewbrowse', array(
          'label' => 'Store Reviews View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store review\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewbrowse', 2),
      ));  

    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adofferwidget', array(
          'label' => 'Store Profile Offers Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile offers widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adofferstore', array(
          'label' => 'Store Offers Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on store offers browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferstore', 2),
      ));

      $this->addElement('Text', 'sitestore_adofferlist', array(
          'label' => 'Store Offers List Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on store offers list store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferlist', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adformwidget', array(
          'label' => 'Store Profile Form Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile form widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adformwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adformcreate', array(
          'label' => 'Form Add Question Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on form\'s add question store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adformcreate', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adinvite', array(
          'label' => 'Invite Friends Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on invite friends store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinvite', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adbadgeview', array(
          'label' => 'Badges View Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a badge\'s view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adbadgeview', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_adlocationwidget', array(
          'label' => 'Store Profile Map Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile map widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adlocationwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adoverviewwidget', array(
          'label' => 'Store Profile Overview Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile overview widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adoverviewwidget', 0),
      ));

      $this->addElement('Text', 'sitestore_adinfowidget', array(
          'label' => 'Store Profile Info Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile info widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adinfowidget', 3),
      ));

      $this->addElement('Text', 'sitestore_adclaimview', array(
          'label' => 'Claim Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on claim store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adclaimview', 2),
      ));

      $this->addElement('Text', 'sitestore_adtagview', array(
          'label' => 'Browse Tags Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on browse tags store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adtagview', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitestore_admusicwidget', array(
          'label' => 'Store Profile Music Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in store profile music widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicwidget', 3),
      ));

      $this->addElement('Text', 'sitestore_admusicview', array(
          'label' => 'Store Music View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store music view store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicview', 3),
      ));

      $this->addElement('Text', 'sitestore_admusicbrowse', array(
          'label' => 'Store Music Browse Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store music browse store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicbrowse', 3),
      ));

      $this->addElement('Text', 'sitestore_admusiccreate', array(
          'label' => 'Store Music Create Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store music create store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusiccreate', 3),
      ));

      $this->addElement('Text', 'sitestore_admusicedit', array(
          'label' => 'Store Music Edit Store',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a store music edit store?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicedit', 3),
      ));
    }

	  //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    $sitestoreintegrationModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitestoreintegration');
    if(!empty($sitestoreintegrationModEnabled)&& Engine_Api::_()->getDbtable('modules',
      'core')->isModuleEnabled('communityad')) {
			$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitestoreintegration'
	    )->getIntegrationItems();
			foreach($mixSettingsResults as $modNameValue) {

// 				if(strstr($modNameValue['resource_type'], 'sitereview_listing')) {
// 					$item_title = 'Products';
// 				} else {
// 					$item_title = $modNameValue["item_title"];
// 				}
        $item_title = $modNameValue["item_title"];
				$this->addElement('Text', "sitestore_ad_" . $modNameValue['resource_type']. '_' .$modNameValue['listingtype_id'], array(
					'label' => "Store Profile " . $item_title . " Widget",
					'maxlenght' => 3,
					'description' => "How many ads will be shown in store profile " .  $item_title . "   widget?",
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad." . $modNameValue['resource_type'].".".$modNameValue['listingtype_id'] , 3),
				));
			}
	  }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
	  
	  //START AD WORK FOR TWITTER
    $sitestoretwitterModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitestoretwitter');
		if(!empty($sitestoretwitterModEnabled)&& Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('communityad')) {
			$this->addElement('Text', 'sitestore_adtwitterwidget', array(
					'label' => 'Store Profile Twitter Widget',
					'maxlenght' => 3,
					'description' => 'How many ads will be shown in store profile twitter widget?',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adtwitterwidget', 3),
			));
		}
		//END AD WORK FOR TWITTER

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}

?>
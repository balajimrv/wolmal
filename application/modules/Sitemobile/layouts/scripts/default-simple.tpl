<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: default-simple.tpl 10017 2013-03-27 01:27:56Z jung $
 * @author     John
 */
?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
// Get body identity
if (isset($this->layout()->siteinfo['identity'])) {
    $identity = $this->layout()->siteinfo['identity'];
} else {
    $identity = $request->getModuleName() . '-' .
            $request->getControllerName() . '-' .
            $request->getActionName();
}


$pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName();
$pageTitle = $this->translate($pageTitleKey);
$pageTitleKey = 'mobilepagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName();
$pageTitle = $this->translate($pageTitleKey);
if (($pageTitle && $pageTitle != $pageTitleKey)) {
    $title = $pageTitle;
    if (($this->subject() && $this->subject()->getIdentity()) && $this->subject()->getTitle()) {
        $title = $pageTitle . " - " . $this->subject()->getTitle();
    }

    $sitemapPageHeaderTitle = $title;
} else {

    if ($this->subject() && $this->subject()->getIdentity() && $this->subject()->getTitle()) {
        $sitemapPageHeaderTitle = $title = $this->subject()->getTitle();
    } else {
        $pageTitle = $title = str_replace(array('<title>', '</title>'), '', $this->headTitle()->toString());
        if (empty($title)) {
            $pageTitle = $title = $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'));
        }
        if ($this->subject() && $this->subject()->getIdentity() && $this->subject()->getTitle()) {
            $title = $pageTitle . " - " . $this->subject()->getTitle();
        }
        $sitemapPageHeaderTitle = $title;
    }
}
$viewVars = $this->getVars();
if (!isset($viewVars['sitemapPageHeaderTitle'])) {
    $this->sitemapPageHeaderTitle = $sitemapPageHeaderTitle;
}
if (!Zend_Registry::isRegistered('sitemapPageHeaderTitle'))
    Zend_Registry::set('sitemapPageHeaderTitle', $sitemapPageHeaderTitle);
$contentType = $request->getParam('contentType', null);
$formatType = $request->getParam('formatType', null);
$clear_cache = $request->getParam('clear_cache', null);
if (!isset($viewVars['clear_cache'])) {
    $this->clear_cache = $clear_cache;
}
if (empty($formatType)):
    ?>
    <?php echo $this->doctype()->__toString() ?>
    <?php $locale = $this->locale()->getLocale()->__toString();
    $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' );
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
        <head>
            <base href="<?php echo rtrim('//' . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />
            <?php // ALLOW HOOKS INTO META  ?>
            <?php echo $this->hooks('onRenderLayoutMobileSMDefault', $this) ?>

            <?php // TITLE/META  ?>
            <?php
            $counter = (int) $this->layout()->counter;
            $staticBaseUrl = $this->layout()->staticBaseUrl;


            $this->headMeta()
                    ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
                    ->appendHttpEquiv('Content-Language', $locale);

            $this->headMeta()
                    ->appendName('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0')
                    ->appendName('environment', APPLICATION_ENV);
            ?>

            <?php echo $this->headMeta()->toString() . "\n" ?>
            <?php echo $this->headSplashScreen()->toString() . "\n" ?>
            <?php echo $this->headHomeScreenIcon()->toString() . "\n" ?>
            <?php
            $themes = array();
            if (!empty($this->layout()->themes)) {
                $themes = $this->layout()->themes;
            } else {
                $themes = array('default');
            }
            
            foreach ($themes as $theme) {
              if (APPLICATION_ENV != 'development') {
                $this->headLinkSM()->prependStylesheet($staticBaseUrl . 'application/themes/sitemobile_tablet/' . $theme . '/structure.css');
                $this->headLinkSM()
                        ->prependStylesheet($staticBaseUrl . 'application/themes/sitemobile_tablet/' . $theme . '/theme.css');
              } else {
                $this->headLinkSM()->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/themes/sitemobile_tablet/' . $theme . '/structure.css');
                $this->headLinkSM()
                        ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/themes/sitemobile_tablet/' . $theme . '/theme.css');
              }
            }
      
            //foreach ($themes as $theme) {
               // if (APPLICATION_ENV != 'development') {
               //     $this->headLinkSM()
               // ->prependStylesheet(rtrim($staticBaseUrl, '/') . '/application/modules/Sitemobile/externals/styles/style.css')
               // ->prependStylesheet(rtrim($staticBaseUrl, '/') . '/application/css.php?request=/application/themes/sitemobile_tablet/' . $theme . '/modules.css')
              // ->prependStylesheet(rtrim($staticBaseUrl, '/') . '/application/themes/sitemobile_tablet/' . $theme . '/structure.css')
              // ->prependStylesheet(rtrim($staticBaseUrl, '/') . '/application/themes/sitemobile_tablet/' . $theme . '/theme.css');
              //  } else {
               //     $this->headLinkSM()
               // ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/styles/style.css')
               // ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/css.php?request=/application/themes/sitemobile_tablet/' . $theme . '/modules.css')
              // ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/themes/sitemobile_tablet/' . $theme . '/structure.css')
              // ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/themes/sitemobile_tablet/' . $theme . '/theme.css');
              //  }
                $this->headLinkSM()->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/jqm-icon-pack/jqm-icon-pack.css');
            //}
            // Process
            foreach ($this->headLinkSM()->getContainer() as $dat) {
                if (!empty($dat->href)) {
                    if (false === strpos($dat->href, '?')) {
                        $dat->href .= '?c=' . $counter;
                    } else {
                        $dat->href .= '&c=' . $counter;
                    }
                }
            }
            ?>
            <?php echo $this->headLinkSM()->toString() . "\n" ?>
            <?php echo $this->headStyleSM()->toString() . "\n" ?>

            <?php // TRANSLATE        ?>
            <?php $this->headTranslate(Engine_Api::_()->sitemobile()->translateData()); ?>
            <?php // SCRIPTS   ?>
            <?php //CHECK IF SITETAGCHECKIN PLUGIN ENABLED.. ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')): ?>
                <?php
                //GET API KEY
                $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                $this->headScriptSM()->appendFile("http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=$apiKey");
                ?>
                <?php
                $this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
                $this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/markerclusterer.js");
                ?>
            <?php endif; ?>
            <script type="text/javascript">
    <?php echo $this->headScriptSM()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
                sm4.core.init({
                    baseUrl: '<?php echo $this->url(array(), 'default', true) ?>',
                    requestInfo:<?php echo $this->jsonInline(array('module' => $request->getModuleName(), 'controller' => $request->getControllerName(), 'action' => $request->getActionName(), 'id' => $identity, 'title' => $title, 'contentType' => 'page')); ?>,
                    languageData:<?php echo $this->headTranslate()->render() ?>,
                    defaultPageTransition: 'none',
                    viewData:<?php echo $this->jsonInline(Engine_Api::_()->sitemobile()->viewData($this->getVars())) ?>});
                sm4.core.staticBaseUrl = '<?php echo $this->escape($staticBaseUrl) ?>';

    <?php if ($this->subject()): ?>
                    sm4.core.subject = {
                        type: '<?php echo $this->subject()->getType(); ?>',
                        id: <?php echo $this->subject()->getIdentity(); ?>,
                        guid: '<?php echo $this->subject()->getGuid(); ?>'
                    };
    <?php endif; ?>
    <?php echo $this->headScriptSM()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
            </script>
            <?php //CHECK IF ADVANCEDACTIVITY PLUGIN ENABLED..  ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')): ?>
                <?php $this->headScriptSM()->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/composer_socialservices.js')
                        ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/jquery.form.js');
                ?>
            <?php endif; ?>

            <?php //CHECK IF SUGGESTION PLUGIN ENABLED.. ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('suggestion')): ?>
                <?php $this->headScriptSM()->prependFile($staticBaseUrl . 'application/modules/Suggestion/externals/scripts/friends_mobile.js'); ?>
            <?php endif; ?>

            <?php
            $this->headScriptSM()
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smActivity.js')
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/core.js')
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/iscroll.js')
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/jquery-ui/jquery.ui.js')
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/player/jquery.jplayer.js');?>
            
            <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('nestedcomment')): ?>
            <?php
                    $this->headScriptSM()->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/photoswipe/code.photoswipe-nestedcomment-3.0.5.js');?>
            <?php else:?>
            <?php
                    $this->headScriptSM()->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/photoswipe/code.photoswipe-3.0.5.js');?>
            <?php endif;?>
            
             <?php        $this->headScriptSM()->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/photoswipe/klass.min.js')
                     ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/jquery.mobile-1.4.1' . (APPLICATION_ENV == 'development' ? '' : '.min' ) . '.js')
                    ->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/jquery' . (APPLICATION_ENV == 'development' ? '' : '.min') . '.js')
                    ->prependFile($staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
            //->prependFile($staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/crop.js')
            ;

            // Process
            foreach ($this->headScriptSM()->getContainer() as $dat) {
                if (!empty($dat->attributes['src'])) {
                    if (false === strpos($dat->attributes['src'], '?')) {
                        $dat->attributes['src'] .= '?c=' . $counter;
                    } else {
                        $dat->attributes['src'] .= '&c=' . $counter;
                    }
                }
            }
            ?>
    <?php echo $this->headScriptSM()->toString() . "\n" ?>
        </head>
        <body id="global_page_<?php echo $identity ?>" data-view-mode="<?php echo Engine_API::_()->sitemobile()->checkMode('tablet-mode') ? "tablet" : "mobile" ?>" class="global_page_content_<?php echo Engine_API::_()->sitemobile()->checkMode('tablet-mode') ? "tablet" : "mobile" ?>">
            <!--    Multi-page template structure-->
            <locale date="<?php echo $this->localeDateSM(); ?>"
                    datetime="<?php echo Zend_Locale_Data::getContent($this->locale()->getLocale(), 'datetime', 'long') ?>" ></locale>
                    <?php
                    echo $this->partial(
                            '_pageContent.tpl', 'sitemobile', array_merge($this->getVars(), array(
                        'contentType' => $contentType,
                        'identity' => $identity,
                        'title' => $title,
                        'headeContent' => null,
                        'footerContent' => null,
                        'content' => $this->layout()->content,
                        'headerOptions' => array(
                            'display' => true,
                        )
                            ))
                    )
                    ?>

        </body>
    </html>
<?php elseif($formatType=='html'): ?>
<?php  $content = $this->layout()->content;
    echo $content;
    ?>
<?php else: ?>
    <?php
    $content = $this->layout()->content;
    $responseHTML = ($content) ? $this->partial(
                    '_pageContent.tpl', 'sitemobile', array_merge($this->getVars(), array(
                'contentType' => $contentType,
                'identity' => $identity,
                'title' => $title,
                'headeContent' => null,
                'footerContent' => null,
                'content' => $content,
                'headerOptions' => array(
                    'display' => true,
                )
                    ))
            ) : '';
    $this->responseScripts = $this->headScriptSM()->toString();
    $this->responseLanguageData = $this->headTranslate()->render();
    $this->requestInfo = array('module' => $request->getModuleName(), 'controller' => $request->getControllerName(), 'action' => $request->getActionName(), 'id' => $identity, 'title' => $title, 'contentType' => $request->getParam('contentType', 'page'));
    ?>
    <?php echo $this->jsonInline($this->getVars()) . 'RESPONSE_HTML_SM' . $responseHTML ?>

<?php endif; ?>

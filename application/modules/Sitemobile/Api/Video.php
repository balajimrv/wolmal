<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Video.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Video extends Core_Api_Abstract {

  public function getRichContent($view = false, $params = array(), $video) {
    $mobile = 1;

    // if video type is youtube
    if ($video->type == 1) {
      $videoEmbedded = $this->compileYouTube($video->video_id, $video->code, $view, $mobile);
    }
    // if video type is vimeo
    if ($video->type == 2) {
      $videoEmbedded = $this->compileVimeo($video->video_id, $video->code, $view, $mobile);
    }

    // if video type is uploaded
    if ($video->type == 3) {
      $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
      $videoEmbedded = $this->compileFlowPlayer($video_location, $view, $video);
    }

    // $view == false means that this rich content is requested from the activity feed
    if ($view == false) {

      // prepare the duration
      //
      $video_duration = "";
      if ($video->duration) {
        if ($video->duration >= 3600) {
          $duration = gmdate("H:i:s", $video->duration);
        } else {
          $duration = gmdate("i:s", $video->duration);
        }
        $duration = ltrim($duration, '0:');

        $video_duration = "<span class='video_length'>" . $duration . "</span>";
      }

      // prepare the thumbnail
      $thumb = Zend_Registry::get('Zend_View')->itemPhoto($video, 'thumb.video.activity');

      if ($video->photo_id) {
        $thumb = Zend_Registry::get('Zend_View')->itemPhoto($video, 'thumb.video.activity');
      } else {
        $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
      }

//       if (!$mobile) {
//         $thumb = '<a id="video_thumb_' . $video->video_id . '" style="" href="javascript:void(0);" onclick="javascript:var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
//                   <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
//                   </a>';
//       } else {
        $thumb = '<a id="video_thumb_' . $video->video_id . '" class="video_thumb" href="javascript:void(0);" onclick="javascript: $(\'videoFrame' . $video->video_id . '\').style.display=\'block\'; $(\'videoFrame' . $video->video_id . '\').src = $(\'videoFrame' . $video->video_id . '\').src; var myElement = $(this); myElement.style.display=\'none\'; var next = myElement.getNext(); next.style.display=\'block\';">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
      //}

      // prepare title and description
      $title = "<a href='" . $video->getHref($params) . "'>$video->title</a>";
      $tmpBody = strip_tags($video->description);
      $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

      $videoEmbedded = $thumb . '<div id="video_object_' . $video->video_id . '" class="video_object">' . $videoEmbedded . '</div><div class="video_info">' . $title . $description . '</div>';
    }

    return $videoEmbedded;
  }

  public function getEmbedCode(array $options = null) {
    $options = array_merge(array(
        'height' => '525',
        'width' => '525',
            ), (array) $options);

    $view = Zend_Registry::get('Zend_View');
    $url = '//' . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'video',
                'controller' => 'video',
                'action' => 'external',
                'video_id' => $video->getIdentity(),
                    ), 'default', true) . '?format=frame';
    return '<iframe '
            . 'src="' . $view->escape($url) . '" '
            . 'width="' . sprintf("%d", $options['width']) . '" '
            . 'height="' . sprintf("%d", $options['width']) . '" '
            . 'style="overflow:hidden;"'
            . '>'
            . '</iframe>';
  }

  public function compileYouTube($video_id, $code, $view, $mobile = false) {
    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
    //560 x 340
    //legacy youtube embed code
    if (!$mobile) {
      $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '"">
      <param name="movie" value="'.$newVar.'www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
      <param name="allowFullScreen" value="true"/>
      <param name="allowScriptAccess" value="always"/>
      <embed src="'.$newVar.'www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1' . ($view ? "" : "&autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
    } else {
      $autoplay = !$mobile && !$view;

      $embedded = '
        <iframe
        title="YouTube video player"
        id="videoFrame' . $video_id . '"
        class="youtube_iframe' . ($view ? "_big" : "_small") . '"' .
              /*
                width="'.($view?"560":"425").'"
                height="'.($view?"340":"344").'"
               */'
        src="'.$newVar.'www.youtube.com/embed/' . $code . '?wmode=opaque&modestbranding=1&showinfo=0' . ($autoplay ? "&autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          sm4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = $("videoFrame' . $video_id . '");
              var parent = el.parent();
              var parentSize = parent.size();
              el.attr("width", parentSize.x);
              el.attr("height", parentSize.x / aspect);
            }
            $(window).on("resize", doResize);
            doResize();
          });
        </script>
      ';
    }

    return $embedded;
  }

  public function compileVimeo($video_id, $code, $view, $mobile = false) {
    //640 x 360
    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
    if (!$mobile) {
      $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '"">
      <param name="allowfullscreen" value="true"/>
      <param name="allowscriptaccess" value="always"/>
      <param name="movie" value="//vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />
      <embed src="'.$newVar.'vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1' . ($view ? "" : "&amp;autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="' . ($view ? "640" : "400") . '" height="' . ($view ? "360" : "230") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
    } else {
      $autoplay = !$mobile && !$view;

      $embedded = '
        <iframe
        title="Vimeo video player"
        id="videoFrame' . $video_id . '"
        class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
              /*
                width="'.($view?"640":"400").'"
                height="'.($view?"360":"230").'"
               */'
        src="'.$newVar.'player.vimeo.com/video/' . $code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          sm4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = $("videoFrame' . $video_id . '");
              var parent = el.parent();
              var parentSize = parent.size();
              el.attr("width", parentSize.x);
              el.attr("height", parentSize.x / aspect);
            }
            $(window).on("resize", doResize);
            doResize();
          });
        </script>
        ';
    }

    return $embedded;
  }

  public function compileFlowPlayer($location, $view, $video) {
    //    php echo $video->baseUrl() /externals/flowplayer/flowplayer-3.1.5.swf"
      
             
       $flowplayerSwf = !Engine_Api::_()->sitemobile()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? 'flowplayer-3.1.5.swf' : 'flowplayer-3.2.18.swf';
    $embedded = "
    <div id='videoFrame" . $video->video_id . "'></div>
    <script type='text/javascript'>
    sm4.core.runonce.add(function(){\$('video_thumb_" . $video->video_id . "').unbind('click').bind('click', function(){flashembed('videoFrame$video->video_id',{src: '" . Zend_Registry::get('StaticBaseUrl') . $flowplayerSwf."', width: " . ($view ? "480" : "420") . ", height: " . ($view ? "386" : "326") . ", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: " . ($view ? "false" : "true") . ", duration: '$video->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
    </script>";

    return $embedded;
  }

}

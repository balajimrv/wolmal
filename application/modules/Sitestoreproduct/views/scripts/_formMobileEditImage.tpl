<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formEditImage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php

$product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);
$sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
?>

<?php if ($sitestoreproduct->photo_id !== null): ?>
  <div>
    <?php echo $this->itemPhoto($sitestoreproduct, 'thumb.profile', "", array('id' => 'lassoImg', 'class' => 'thumb_profile_edit')) ?>
  </div>
  <br />
  <div id="preview-thumbnail" class="preview-thumbnail">
    <?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon', "", array('id' => 'previewimage')) ?>
  </div>
  <div id="thumbnail-controller" class="thumbnail-controller">
    <?php if ($sitestoreproduct->getPhotoUrl())
      echo '<a href="javascript:void(0);" onclick="lassoStart();">' . $this->translate('Edit Thumbnail') . '</a>'; ?>
  </div>
  <script type="text/javascript">
    var orginalThumbSrc;
    var originalSize;
    var loader = new Element('img',{ src:en4.core.staticBaseUrl+'application/modules/Seaocore/externals/images/core/loading.gif'});
    var lassoCrop;
        
    var lassoSetCoords = function(coords)
    {
      var delta = (coords.w - 48) / coords.w;

      document.getElementById('coordinates').value =
        coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;
          
      document.getElementById('previewimage').setStyles({
        top : -( coords.y - (coords.y * delta) ),
        left : -( coords.x - (coords.x * delta) ),
        height : ( originalSize.y - (originalSize.y * delta) ),
        width : ( originalSize.x - (originalSize.x * delta) )
      });
    }

    var lassoStart = function()
    {
      if( !orginalThumbSrc ) orginalThumbSrc = document.getElementById('previewimage').src;
      originalSize = document.getElementById("lassoImg").getSize();
      lassoCrop = new Lasso.Crop('lassoImg', {
        ratio : [1, 1],
        preset : [10,10,58,58],
        min : [48,48],
        handleSize : 8,
        opacity : .6,
        color : '#7389AE',
        border : '<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
        onResize : lassoSetCoords,
        bgimage : ''
      });

      document.getElementById('previewimage').src = document.getElementById('lassoImg').src;
      document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes'); ?></a> <?php echo $this->translate('or'); ?> <a href="javascript:void(0);" onclick="lassoCancel();"><?php echo $this->translate('cancel'); ?></a>';
      document.getElementById('coordinates').value = 10 + ':' + 10 + ':' + 58+ ':' + 58;
    }

    var lassoEnd = function() {
      document.getElementById('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/core/loading.gif'/><?php echo $this->string()->escapeJavascript($this->translate('Loading...')); ?></div>";
      lassoCrop.destroy();
      document.getElementById('EditPhoto').submit();
    }

    var lassoCancel = function() {
      document.getElementById('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+orginalThumbSrc+'"/>';
      document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail'); ?></a>';
      document.getElementById('coordinates').value = "";
      lassoCrop.destroy();
    }
        
    var uploadPhoto = function() {
      document.getElementById('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/core/loading.gif'/><?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?></div>";
      document.getElementById('EditPhoto').submit();
      document.getElementById('Filedata-wrapper').innerHTML = "";
    }
  </script>

  <style type="text/css">
    img.thumb_profile_edit {
      max-height: 500px !important;
      max-width: 300px !important;
    }
  </style>  
<?php endif; ?>

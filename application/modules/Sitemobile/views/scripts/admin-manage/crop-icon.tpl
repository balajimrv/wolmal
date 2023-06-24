<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: photo.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>

  <?php
  $key = $this->key;
  $keys = explode('x', $key);
  ?>

<h3>
  <?php echo $this->translate('Recreate Home Screen Icon '); ?><?php echo "(".$key."px)"; ?>
</h3>

<!--<div class="tip">-->
<!--     <span>-->
      <p>
        <?php echo $this->translate("Recreate the icon of size (".$key."px) from the original image by cropping it."); ?>
      </p>
<!--     </span>-->
<!--</div>-->
      <br>

  <div class="sm_crop_icon">
	  <h4><b><?php echo $this->translate('Original Home Screen Icon'); ?></b></h4>
	  <img src="<?php echo $this->photoMain; ?>" id="lassoImg" />
	
		<h4><b><?php echo $this->translate('Preview Icon: Icon Size '); ?><?php echo "(".$key."px)"; ?></b></h4>
		<div id="preview-thumbnail" class="preview-thumbnail" style="width:<?php echo $keys[0]; ?>px;height:<?php echo $keys[1]; ?>px">
		  <img src="<?php echo $this->photoIcon; ?>" id="previewimage"  />
		</div>
		<?php echo $this->form->render($this) ?>
		<?php
		$this->headScript()
		        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
		        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js');
		?>
		<div id="thumbnail-controller" class="thumbnail-controller">
		  <?php echo '<a href="javascript:void(0);" onclick="lassoStart();">' . $this->translate('Edit Thumbnail') . '</a>'; ?>
		</div>

		<div id="cancel_link">
		  &nbsp; or
		  <a href="javascript:void(0);" onclick="cancel_link();"><?php echo $this->translate('Cancel'); ?></a>
		</div>
	</div>
<script type="text/javascript">
  var orginalThumbSrc;
  var originalSize;
  var loader = new Element('img',{ src:'application/modules/Sitemobile/modules/Core/externals/images/loading.gif'});
  var lassoCrop;
   
//Set initial coordinates of preview image or lasso image.   
  var lassoSetCoords = function(coords)
  {
    var delta = (coords.w - <?php echo $keys[0]; ?>) / coords.w;

    $('coordinates').value =
      coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;
        
    $('previewimage').setStyles({
      top : -( coords.y - (coords.y * delta) ),
      left : -( coords.x - (coords.x * delta) ),
      height : ( originalSize.y - (originalSize.y * delta) ),
      width : ( originalSize.x - (originalSize.x * delta) )
    });
  }

//Function to get the preview image same as lasso image( crop imge ) simultaneously. 
  var lassoStart = function()
  {      
//    var confirm_check = confirm('Edit thumbnail will edit the original icon. Are you sure that you want to recreate "Home Screen Icon" from "Original Image"? ');
   // if (confirm_check == true){
      if( !orginalThumbSrc ) orginalThumbSrc = $('previewimage').src;
      originalSize = $("lassoImg").getSize();
      lassoCrop = new Lasso.Crop('lassoImg', {
        ratio : [1, 1],
        preset : [10,10,<?php echo ($keys[0] + 10); ?>,<?php echo ($keys[0] + 10); ?>],
        min : [<?php echo $keys[0]; ?>,<?php echo $keys[0]; ?>],
        handleSize : 8,
        opacity : .6,
        color : '#7389AE',
        border : '<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
        onResize : lassoSetCoords,
        bgimage : ''  
      });
      
     // $('cancel_link').hide();
 
      $('previewimage').src = $('lassoImg').src;
      
      $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes'); ?></a> ';
      $('coordinates').value = 10 + ':' + 10 + ':' + <?php echo ($keys[0] + 10); ?>+ ':' + <?php echo ($keys[0] + 10); ?>;
    }
 // }

// Before save cropped image confirmation.
  var lassoEnd = function() {
    var confirm_check=confirm('Are you sure that you want to recreate "Home Screen Icon" from "Original Image"? It will not be recoverable after being recreated.');
    if (confirm_check==true){
      $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'/><?php echo $this->string()->escapeJavascript($this->translate('Loading...')); ?></div>";
      lassoCrop.destroy();
      $('EditPhoto').submit();
    } 
  }

  var lassoCancel = function() {
    $('cancel_link').show();
    $('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+orginalThumbSrc+'"/>';
    $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail'); ?></a>';
    $('coordinates').value = "";
    lassoCrop.destroy();
  }
      
  function cancel_link(){
    var url = '<?php echo $this->url(array('action' => 'home-icon', 'controller' => 'manage', 'module' => 'sitemobile'), 'admin_default', true) ?>';
    window.location.href = url;
  }
  window.addEvent('domready',function(){
    (function(){lassoStart()}).delay('100');
  });
</script>
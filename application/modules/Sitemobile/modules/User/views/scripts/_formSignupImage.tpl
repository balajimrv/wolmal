<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _formSignupImage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div>
  <?php 
    if (isset($_SESSION['TemporaryProfileImg'])){
      echo '<img src="'.$_SESSION['TemporaryProfileImgProfile'].'" alt="" id="lassoImg"/>';
    }
    else {?>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/User/externals/images/nophoto_user_thumb_profile.png' id="lassoImg" />
    <?php }?>
</div>

<div id="thumbnail-controller" class="thumbnail-controller"></div>

<script type="text/javascript">
	function uploadSignupPhoto() {
		$.mobile.activePage.find('#uploadPhoto').attr('value', true);
//		$('#thumbnail-controller').html("<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/></div>");
      if($.mobile.activePage.find('#nextStep')){
        $.mobile.activePage.find('#nextStep').attr('value', 'upload');
      }
		$.mobile.activePage.find('#SignupForm').submit();
		$.mobile.activePage.find('#Filedata-wrapper').html("");
	}
</script>



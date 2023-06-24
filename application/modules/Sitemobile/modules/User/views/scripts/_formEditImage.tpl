<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _formEditImage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->subject()->photo_id !== null): ?>

  <div>
    <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  </div>

  <div id="thumbnail-controller" class="thumbnail-controller"></div>

  <script type="text/javascript">

    function uploadSignupPhoto() {
      $('#thumbnail-controller').html("<div><img class='loading_icon' src='application/modules/Sitemobile/modules/Core/externals/images/loading.gif'/></div>");
      $('#EditPhoto').submit();
      $('#Filedata-wrapper').html("");
    }
  </script>

<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: compose-upload.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>
<script type="text/javascript">
  $try(function(){
    parent.en4.sitestorealbum.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars()) ?>);
  });
  $try(function() {
    parent._composeSitestorePhotoResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
  });
</script>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: facebook-success.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if(!isset($_GET['returnAppUrl'])): ?>
<script  type="text/javascript">
 /* 
  alert("//<?php echo $this->url()?>");
 window.location.href=window.location.href+"?returnAppUrl=//<?php echo $this->returnUrl;?>";
 //window.location.reload(true);*/
</script>
<?php endif; ?>



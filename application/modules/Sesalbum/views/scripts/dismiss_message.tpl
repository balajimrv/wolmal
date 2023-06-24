<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: dismiss_message.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="text/javascript">
function dismiss(coockiesValue) {
  var d = new Date();
  d.setTime(d.getTime()+(365*24*60*60*1000));
  var expires = "expires="+d.toGMTString();
  document.cookie = coockiesValue + "=" + 1 + "; " + expires;
	$(coockiesValue).style.display = 'none';
}
</script>
<?php if( !isset($_COOKIE["dismiss_developer"])): ?>
  <div id="dismiss_developer" class="tip">
    <span>
      <?php echo "Can you rate our developer profile on <a href='http://www.socialengine.com/customize/se4/developer?dev_id=19313' target='_blank'>socialengine.com</a> to support us? <a href='http://www.socialengine.com/customize/se4/developer?dev_id=19313' target='_blank'>Yes</a> or <a href='javascript:void(0);' onclick='dismiss(\"dismiss_developer\")'>No, not now</a>.";
    ?>
    </span>
  </div>
<?php endif; ?>
<?php if( !isset($_COOKIE["dismiss_sesalbumplugin"])): ?>
  <div id="dismiss_sesalbumplugin" class="tip">
    <span>
      <?php echo "Can you rate our plugin on <a href='http://www.socialengine.com/customize/se4/mod-page?mod_id=1449' target='_blank'>socialengine.com</a> to support our plugin?  <a href='http://www.socialengine.com/customize/se4/mod-page?mod_id=1449' target='_blank'>Yes</a> or <a href='javascript:void(0);' onclick='dismiss(\"dismiss_sesalbumplugin\")'>No, not now</a>.";
    ?>
    </span>
  </div>
<?php endif; ?>
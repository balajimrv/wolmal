<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<script type="text/javascript">
var seaocore_like_url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitestore_like', true);?>';
seaocore_content_type = 'sitestore';
</script>

<?php 
$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
  if(!empty($viewer))
  {
    $MODULE_NAME = 'sitestore';
    $RESOURCE_TYPE = 'sitestore_store';
    $RESOURCE_ID = Engine_Api::_()->core()->getSubject()->getIdentity();

    // Check that for this 'resurce type' & 'resource id' user liked or not.
    $check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
    if( !empty($check_availability) )
    {
      $label = 'Unlike this';
      $unlike_show = "display:block;";
      $like_show = "display:none;";
      $like_id = $check_availability[0]['like_id'];
    }
    else
    {
      $label = 'Like this';
      $unlike_show = "display:none;";
      $like_show = "display:block;";
      $like_id = 0;
    }
  }
  ?>
	<?php if(empty($this->sitestore_like)){ return; } ?>
  <div class="sitestore_like_button" id="sitestore_unlikes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $unlike_show;?>' >
    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
      <i class="sitestore_like_thumbdown_icon"></i>
      <span><?php echo $this->translate('Unlike') ?></span>
    </a>
  </div>
  <div class="sitestore_like_button" id="sitestore_most_likes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $like_show;?>'>
    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
      <i class="sitestore_like_thumbup_icon"></i>
      <span><?php echo $this->translate('Like') ?></span>
    </a>
  </div>
  <input type ="hidden" id = "sitestore_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _addToWishlist.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php 
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>

<?php $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
<?php if($viewer_id): ?>
  <?php $addUrl = $this->url(array('action' => 'add', 'product_id' => $this->item->product_id), 'sitestoreproduct_wishlist_general', true); ?>
  <a title="<?php echo $this->translate('Add to Wishlist'); ?>" class="<?php echo $this->classIcon.' '.$this->classLink ?>" onclick="openWishlistCreationBox('<?php echo $addUrl;?>');" href="javascript:void(0);"><?php echo $this->translate($this->text);?></a>
<?php else: ?>
  <?php 
    $urlO = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    $request_url = explode('/',$urlO);
    empty($request_url['2']) ? $param = 2 : $param = 1;
    $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://";
    $currentUrl = urlencode($urlO);
  ?> 
  <?php 
  
  $addUrl = $this->url(array('action' => 'add', 'product_id' => $this->item->product_id, 'param' => $param,'request_url' => $request_url['1']), "sitestoreproduct_wishlist_general")."?"."return_url=".$return_url.$_SERVER['HTTP_HOST'].$currentUrl;
 ?>
  
    <a title="<?php echo $this->translate('Add to Wishlist'); ?>" class="<?php echo $this->classIcon.' '.$this->classLink ?>" onclick="openWishlistCreationBox('<?php echo $addUrl;?>');" href="javascript:void(0);"><?php echo $this->translate($this->text);?></a>
  
<?php endif;?>

<script type="text/javascript">
  function openWishlistCreationBox(addUrl) {
    parent.Smoothbox.close();
    Smoothbox.open(addUrl);
  }
</script>  

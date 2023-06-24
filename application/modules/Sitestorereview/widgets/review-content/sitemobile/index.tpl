<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Reviews","icon"=>"arrow-r"),
    array("title"=>$this->sitestorereview->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>

<div class="ui-store-content">
  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-author-photo">
      <?php if(!empty($photo_review)):?>
        <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
      <?php else:?>
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), $this->itemPhoto($this->sitestore, 'thumb.normal')) ?>
      <?php endif;?>
    </div>
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
      	<?php echo $this->sitestorereview->title; ?> 
      </div>
      <div class="sm-ui-cont-cont-date">
      	<?php echo $this->translate('Posted by %s ', $this->sitestorereview->getOwner()->toString()); ?>
        -
        <?php echo $this->timestamp($this->sitestorereview->creation_date); ?>
      </div>
      <div class="sm-ui-cont-cont-date"> 
      	<?php echo $this->translate(array('%s view', '%s views', $this->sitestorereview->view_count), $this->locale()->toNumber($this->sitestorereview->view_count)) ?>
      </div>
    </div>
    </div>
  <?php echo $this->content()->renderWidget("sitestorereview.sitestore-review-detail"); ?>
			
    <?php if(false):?>  
		  <div class="tip">
		  	<span>
		  	<?php echo $this->translate("Like this review if you find it useful."); ?>
		  	</span>
		  </div>	
     <?php endif;?>
  </div>
<?php $baseUrl_full = 'http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl(); ?>
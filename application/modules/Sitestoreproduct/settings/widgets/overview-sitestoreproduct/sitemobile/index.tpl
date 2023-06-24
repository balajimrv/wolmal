<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>

<div>
	<?php if(!empty($this->overview)):?>
		<div class="sr_profile_overview">
			<?php echo $this->overview ?>
		</div>
	<?php else:?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You have not composed an overview for your product');?>
			</span>
		</div>
	<?php endif;?>
</div>

<?php if( $this->showComments):?>
		<?php //echo $this->action("list", "comment", "sitemobile", array("type" => $this->sitestoreproduct->getType(), "id" => $this->sitestoreproduct->listing_id)); ?>

		<?php echo $this->content()->renderWidget("sitemobile.comments", array('type' => $this->sitestoreproduct->getType(), 'id' => $this->sitestoreproduct->product_id)); ?>
<?php endif;?>
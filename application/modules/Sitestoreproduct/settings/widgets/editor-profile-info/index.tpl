<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<ul class="seaocore_sidebar_list sr_sitestoreproduct_editor_profile_info">
  <li>
		<div class="sr_sitestoreproduct_editor_profile_details o_hidden">
			<?php if(!empty($this->badge_photo_id)): ?>
				<?php $thumb_path = Engine_Api::_()->storage()->get($this->badge_photo_id, '')->getPhotoUrl(); ?>
				<?php if(!empty($thumb_path)): ?>
					<img width="50px" src='<?php echo $thumb_path?>' alt="" class="fleft" />
				<?php endif; ?>
			<?php endif; ?>
          
      <?php if(!empty($this->editor->details)):  ?>    
				<div class="sr_sitestoreproduct_editor_profile_stats">
					<?php echo $this->viewMore($this->editor->details, 500, 5000); ?>
				</div>          
      <?php endif; ?>    
          
			<?php if(!empty($this->editor->designation) && $this->show_designation): ?>
				<div class="sr_sitestoreproduct_editor_profile_stats o_hidden">
					<span><i><?php echo $this->translate("Designation:"); ?></i></span><br />
					<span><b><?php echo $this->editor->designation; ?></b></span>
				</div>
			<?php endif; ?>
			
		</div>
    <?php echo $this->content()->renderWidget("sitestoreproduct.write-sitestoreproduct", array("removeContent" => true)); ?>
	</li>
</ul>



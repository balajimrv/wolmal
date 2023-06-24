<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _showReview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if(isset ($this->sitestoreproduct->rating_editor) && $this->sitestoreproduct->rating_editor && $this->sitestoreproduct->review_count==1):
  $html_title=$this->translate('1 Editor Review');
elseif(isset ($this->sitestoreproduct->rating_editor) && $this->sitestoreproduct->rating_editor):
   $html_title=$this->translate(array('1 Editor Review and %s User Review', '1 Editor Review and %s User Reviews',($this->sitestoreproduct->review_count-1)), $this->locale()->toNumber(($this->sitestoreproduct->review_count-1)));
 else: 
  $html_title=$this->translate(array('%s User Review', '%s User Reviews', $this->sitestoreproduct->review_count), $this->locale()->toNumber($this->sitestoreproduct->review_count));
endif;
?>

<span title="<?php echo $html_title ?>">
 <?php echo $this->translate(array('%s review', '%s reviews', $this->sitestoreproduct->review_count), $this->locale()->toNumber($this->sitestoreproduct->review_count)) ?>
</span>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _createFormHeading.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _createFormHeading.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $accordian = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.accordian', 0); ?>
<?php if (!empty($accordian)) : ?>
  <?php if ($this->div_close) : ?>
    </div>
  <?php endif; ?>
  <?php if ($this->div_open) : ?>
    <?php if (!empty($this->first_heading)) : ?>
      <h4 id="<?php echo $this->div_id; ?>" onclick="expand(this)" class="<?php echo $this->sub_class; ?>">
        <div class="fleft" id="img_<?php echo $this->div_id; ?>">
          <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/arrow.png" /></div>
        <?php echo $this->heading; ?>
      </h4>
    <?php else: ?>
      <h4 id="<?php echo $this->div_id; ?>" onclick="expand(this)" class="<?php echo $this->sub_class; ?>">
        <div class="fleft" id="img_<?php echo $this->div_id; ?>">
          <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/leftarrow.png" /></div>
        <?php echo $this->heading; ?>
      </h4>
    <?php endif; ?>
    <div class="content">
    <?php endif; ?>

  <?php else: ?>
    <h4>
      <?php echo $this->heading; ?>
    </h4>
  <?php endif; ?>

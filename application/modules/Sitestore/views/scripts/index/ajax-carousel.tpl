<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php if ($this->direction == 1) { ?>
  <?php $j = 0; ?>
  <?php foreach ($this->sitestores as $sitestore): ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitestore', array(
        'sitestore' => $sitestore,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,
        'statistics' => $this->statistics
    ));
    ?>	 
  <?php endforeach; ?>
  <?php if ($j < ($this->sponserdSitestoresCount)): ?>
    <?php for ($j; $j < ($this->sponserdSitestoresCount); $j++): ?>
      <div class="sr_carousel_content_item b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
      </div>
    <?php endfor; ?>
  <?php endif; ?>
<?php } else { ?>

  <?php for ($i = $this->sponserdSitestoresCount; $i < Count($this->sitestores); $i++): ?>
    <?php $sitestore = $this->sitestores[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitestore', array(
        'sitestore' => $sitestore,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth, 
        'statistics' => $this->statistics       
    ));
    ?>	
  <?php endfor; ?>

  <?php for ($i = 0; $i < $this->sponserdSitestoresCount; $i++): ?>
    <?php $sitestore = $this->sitestores[$i]; ?>
    <?php
    echo $this->partial(
            'list_carousel.tpl', 'sitestore', array(
        'sitestore' => $sitestore,
        'title_truncation' => $this->title_truncation,
        'vertical' => $this->vertical,
        'featuredIcon' => $this->featuredIcon,
        'sponsoredIcon' => $this->sponsoredIcon,
        'showOptions' => $this->showOptions,
        'blockHeight' => $this->blockHeight,
        'blockWidth' => $this->blockWidth,
        'statistics' => $this->statistics        
    ));
    ?>	
  <?php endfor; ?>
<?php } ?>


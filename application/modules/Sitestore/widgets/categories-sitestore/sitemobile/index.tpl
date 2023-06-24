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

<ul  class="ui-listview collapsible-listview" >
  <?php foreach ($this->categories[0] as $category): ?>
    <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c">
      <?php if (isset($this->categories[$category->category_id])) : ?>
        <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
      <?php else: ?>
        <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
      <?php endif; ?>
      <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
          <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
            <?php echo $this->translate($category->getTitle(true)); ?></a>
        </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
      <?php if (isset($this->categories[$category->category_id])) : ?>
        <ul class="collapsible">
          <?php foreach ($this->categories[$category->category_id] as $category): ?>
            <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li  ui-btn-up-c">
              <?php if (isset($this->categories[$category->category_id])) : ?>
                <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
              <?php else: ?>
                <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
              <?php endif; ?>
              <div class="ui-btn-inner ui-li"><div class="ui-btn-text">
                  <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
                    <?php echo $this->translate($category->getTitle(true)); ?></a>
                </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
              <?php if (isset($this->categories[$category->category_id])) : ?>
                <ul class="collapsible">
                  <?php foreach ($this->categories[$category->category_id] as $category): ?>
                    <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li  ui-btn-up-c">
                      <?php if (isset($this->categories[$category->category_id])) : ?>
                        <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
                      <?php else: ?>
                        <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
                      <?php endif; ?>
                      <div class="ui-btn-inner ui-li"><div class="ui-btn-text">
                          <a class="ui-link-inherit" href="<?php echo $category->getHref() ?>"  >
                            <?php echo $this->translate($category->getTitle(true)); ?></a>
                        </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
                    </li>
                  <?php endforeach;
                  ?>
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach;
          ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach;
  ?>
</ul>


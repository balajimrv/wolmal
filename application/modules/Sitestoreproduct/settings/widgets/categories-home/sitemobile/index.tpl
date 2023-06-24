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

<ul  class="ui-listview collapsible-listview" >
  <?php $k = 0; ?>
  <?php for ($i = 0; $i <= $this->totalCategories; $i++) : ?>
    <?php
    $category = "";
    if (isset($this->categories[$k]) && !empty($this->categories[$k])) {
      $category = $this->categories[$k];
    }

    $k++;

    if (empty($category)) {
      break;
    }
    ?>
    <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c <?php if (isset($category['count'])): ?>ui-li-has-count<?php endif; ?>">
      <?php $total_subcat = !empty($this->show2ndlevelCategory) ? count($category['sub_categories']) : 0; ?>
      <?php if ($total_subcat) : ?>
        <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
      <?php else: ?>
            <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
      <?php endif; ?>
      <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
          <?php $item = Engine_Api::_()->getItem('sitestoreproduct_category', $category['category_id']); ?>
          <a class="ui-link-inherit" href="<?php echo $item->getHref() ?>"  >
            <?php echo $this->translate($item->getTitle(true)); ?>
            <?php if (isset($category['count'])): ?><span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?php echo $category['count'] ?></span><?php endif; ?></a>
        </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
      <?php if ($total_subcat): ?>
        <ul class="collapsible">
          <?php foreach ($category['sub_categories'] as $subcategory) : ?>
            <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c <?php if (isset($subcategory['count'])): ?>ui-li-has-count<?php endif; ?>">
              <?php $total_subcat_tree = !empty($this->show3rdlevelCategory) && isset($subcategory['tree_sub_cat']) ? count($subcategory['tree_sub_cat']) : 0;
              ?>
              <?php if ($total_subcat_tree) : ?>
                <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
              <?php else: ?>
                    <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
              <?php endif; ?>
              <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
                  <?php $item = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory['sub_cat_id']); ?>
                  <a class="ui-link-inherit" href="<?php echo $item->getHref() ?>"  >
                    <?php echo $this->translate($item->getTitle(true)); ?>
                    <?php if (isset($subcategory['count'])): ?><span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?php echo $subcategory['count'] ?></span><?php endif; ?></a>
                </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
              <?php if ($total_subcat_tree): ?>
                <ul class="collapsible">
                  <?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
                    <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c <?php if (isset($subsubcategory['count'])): ?>ui-li-has-count <?php endif; ?>">
                      <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
                      <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
                          <?php $item = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory['tree_sub_cat_id']); ?>
                          <a class="ui-link-inherit" href="<?php echo $item->getHref() ?>"  >
                            <?php echo $this->translate($item->getTitle(true)); ?>
                            <?php if (isset($subsubcategory['count'])): ?><span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?php echo $subsubcategory['count'] ?></span><?php endif; ?></a>
                        </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>

                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endfor; ?>
</ul>

<?php if (empty($this->showCount)): ?>
  <br>
  <div class="sr_categories_list_link fright">
      [ <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_review_categories', 'showCount' => 1), $this->translate('See item counts')); ?> ]
  </div> 
<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-hide-option.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  <h2><?php echo $this->translate('Edit Activity Feed Settings'); ?></h2>


  <div class="aaf_edit_setting">
    <b><?php echo $this->translate('You have hidden Activity Feeds from:');
?></b>
    <div class="aaf_edit_setting_right">  
      <?php if (count($this->hideItems) > 0): ?>
        <?php foreach ($this->hideItems as $resource_type => $hideItem): ?>    
          <div data-role="collapsible-set" data-theme="a" data-content-theme="c" data-collapsed-icon="arrow-r" data-count-theme="b" >
            <div data-role="collapsible"  data-mini="true"  class="<?php echo $class; ?> ui-li-has-count" data-expanded-icon="arrow-d" data-collapsed="false">
              <h3 class="tabs_title">
                <?php echo $this->translate("AAF_HIDE_" . strtoupper($resource_type) . "_TYPE_TITLE"); ?></h3>



              <ul data-role="listview">
                <?php foreach ($hideItem as $item_id): ?>
                  <li  id="hide_item_<?php echo $resource_type ?>_<?php echo $item_id ?>" data-icon="remove" onclick="selectForUnhideItem('<?php echo $resource_type ?>', '<?php echo $item_id ?>');">
                    <?php $content = Engine_Api::_()->getItem($resource_type, $item_id); ?>
                    <?php echo $content->getTitle(); ?>                  
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div> 
        <?php endforeach; ?>

      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("You have not hidden activity feeds from any sources."); ?>
          </span>
        </div>
      <?php endif; ?>

    </div>
  </div>	
  <div class="seaocore_members_popup_bottom">
    <form action="" method="post">
      <input type="hidden" name="unhide_items" id="unhide_items" value="" />
      <button type="submit" data-theme="b"> <?php echo $this->translate('Save') ?></button> 
    </form>
  </div>

  <script type="text/javascript">
        var hideItem = new Array();
        function selectForUnhideItem(type, id) {
          var content = type + '_' + id;
          var el = document.getElementById('hide_item_' + content);
          if (el)
            el.style.display = 'none';
          hideItem.push(type + '-' + id);
          document.getElementById('unhide_items').value = hideItem;
        }
  </script>

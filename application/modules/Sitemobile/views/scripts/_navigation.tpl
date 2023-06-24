<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _navigation.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $rand=rand(100,999999999); ?>
<div <?php echo $this->dataHtmlAttribs("navigation",  array('data-role'=>"navbar" ,'data-inset'=>"true" )); ?>>
  <ul  >
    <?php
    $count = 0;
    $totalCount = count($this->container);
    foreach ($this->container as $item):
      $count++;
      if ($count > 2 && $totalCount > 3):
        ?>
        <li>
          <a href="#navigationBarMorePopupMenu<?php echo $rand?>" id="navigationBarMore" data-rel="popup" data-position-to="window"  ><?php echo $this->translate('More') ?></a>
        </li>
        <?php
        break;
      else:
        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                    'reset_params', 'route', 'module', 'controller', 'action', 'type',
                    'visible', 'label', 'href'
                )));

        if (!isset($attribs['active'])) {
          $attribs['active'] = false;
        }
        ?>
        <li>
          <?php $attribs['class'] = $attribs['active'] ? $attribs['class'] . ' ui-btn-active ui-state-persist' : $attribs['class']; ?>
          <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>

        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</div>
<?php if ($count > 2 && $totalCount > 3): ?>
    <div data-role="popup" id="navigationBarMorePopupMenu<?php echo $rand?>"  data-tolerance="15" data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
      <ul data-role="listview" style="min-width: 250px" data-icon="false">
      <?php $count_nest = 0;
      foreach ($this->container as $item): ?>
        <?php
        $count_nest++;
        if ($count_nest < $count):
          continue;
        endif;
        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                    'reset_params', 'route', 'module', 'controller', 'action', 'type',
                    'visible', 'label', 'href'
                )));
        if (!isset($attribs['active'])) {
          $attribs['active'] = false;
        }
        ?>

        <?php $attribs['class'] = $attribs['active'] ? $attribs['class'] . ' ui-btn ui-btn-icon-right ui-icon-arrow-r ui-btn-active ui-state-persist' : $attribs['class']; ?>
        <li data-icon="arrow-r"> <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?></li>
      <?php endforeach; ?>
        <?php if(!Engine_Api::_()->sitemobile()->isApp()):?>
        <li><a href="#" class="back_popup" data-rel="back"><?php echo $this->translate('Cancel'); ?></a></li>
        <?php endif; ?>
      </ul>
  </div>
<?php endif; ?>

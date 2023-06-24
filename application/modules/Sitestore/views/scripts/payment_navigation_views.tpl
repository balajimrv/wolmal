<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment_navigation_views.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.navigationtabs', 7); 
$headding = "STORE_NAVIGATION_NAME";
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl'; ?>

<div class="headline">
  <h2>
<?php echo $this->translate($headding); ?>
  </h2>
    <?php if (count($this->navigation)) { ?>
    <div class='tabs'>
    <?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
      <ul class='navigation'>
      <?php $key = 0; ?>
        <?php foreach ($this->navigation as $nav): ?>			
          <?php if ($key < $this->max): ?>
            <li 
            <?php if ($nav->active): echo "class='active'";
            endif; ?> >
              <?php if ($nav->route == 'sitestore_general' || $nav->action): ?>
                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
              <?php else : ?>
                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
              <?php endif; ?>
            </li>
          <?php else: ?>
            <?php break; ?>
          <?php endif; ?>
          <?php $key++ ?>
        <?php endforeach; ?>

        <?php if (count($this->navigation) > $this->max): ?>
          <li class="tab_closed more_tab" onclick="moreTabSwitchSitestore($(this));">
            <div class="tab_pulldown_contents_wrapper">
              <div class="tab_pulldown_contents">          
                <ul>
                  <?php $key = 0; ?>
                  <?php foreach ($this->navigation as $nav): ?>
                    <?php if ($key >= $this->max): ?>
                      <li <?php if ($nav->active): echo "class='active'";
              endif; ?> >
                        <?php if ($nav->route == 'sitestore_general' || $nav->action): ?>
                          <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php else : ?>
                          <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                        <?php endif; ?>
                      </li>
                    <?php endif; ?>
                    <?php $key++ ?>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  <?php } ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() {
   
    var moreTabSwitchSitestore = window.moreTabSwitchSitestore = function(el) {
      el.toggleClass('seaocore_tab_open active');
      el.toggleClass('tab_closed');
    }
  });
</script>

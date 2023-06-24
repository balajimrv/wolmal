<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: navigation_views.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
   
    var storeproductMoreTabSwitch = window.storeproductMoreTabSwitch = function(el) {
      el.toggleClass('seaocore_tab_open active');
      el.toggleClass('tab_closed');
    }
  });
</script>

<?php $showCategriesMenu = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.showcategories.menu', 1);
if ($showCategriesMenu): ?>
  <?php echo $this->content()->renderWidget("sitestoreproduct.producttypes-categories", array('beforeNavigation'=>1)) ?>
<?php endif; ?>

<div class="headline">
  <h2 > <?php echo $this->translate("STORE_NAVIGATION_NAME"); ?> </h2>
  <div class="tabs">
  
    <?php if (count($this->navigation)): ?>
      <?php
      ?>
      <?php 
        $this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.navigationtabs', 7); 
      ?>

      <ul class='navigation sr_sitestoreproduct_navigation_common'>
        <?php $key = 0; ?>
        <?php foreach ($this->navigation as $item):?>			
          <?php if ($key < $this->max): ?>
            <li <?php if ($item->isActive()) : ?> class="active" <?php endif; ?>>
              <?php $name = trim(str_replace('menu_core_main ', '', $item->getClass())); ?>
              <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                <?php echo $this->translate($item->getLabel()); ?>
              </a>
            </li>
          <?php else: ?>
            <?php break; ?>
          <?php endif; ?>
          <?php $key++ ?>
        <?php endforeach; ?>

        <?php if (count($this->navigation) > $this->max): ?>
          <li class="tab_closed more_tab" onclick="storeproductMoreTabSwitch($(this));">
            <div class="tab_pulldown_contents_wrapper">
              <div class="tab_pulldown_contents">          
                <ul>
                  <?php $key = 0; ?>
                  <?php foreach ($this->navigation as $item): ?>
                    <?php if ($key >= $this->max): ?>
                      <li <?php if ($item->isActive()) : ?> class="active" <?php endif; ?>>
                        <?php $name = trim(str_replace('menu_core_main ', '', $item->getClass())); ?>
                        <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                          <?php echo $this->translate($item->getLabel()); ?>
                        </a>
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

    <?php endif; ?>
  </div>
</div>

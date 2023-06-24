<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->search_check): ?>
  <div data-role="dashboard_search" class="db_search">
    <form role="search" data-mini="true" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>">
      <input data-type="search" name="query" <?php if ($this->dashboardContentType !== 'dashboard_grid'): ?>data-theme="d" <?php endif; ?> placeholder="<?php echo $this->translate("Search"); ?>" data-mini="true" data-icon="search" data-role="search" />
    </form>
  </div>
<?php endif; ?>

<?php if ($this->dashboardContentType == 'dashboard_grid'): ?>
  <ul <?php echo $this->dataHtmlAttribs("navigation_dashboard", array('data-role' => "listview", 'data-inset' => "true", 'data-corners' => "false")); ?> class="main-navigation navigation_dashboard_grid">
    <?php
    $count = 0;
    foreach ($this->navigation as $item):
      $count++;
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                  'reset_params', 'route', 'module', 'controller', 'action', 'type',
                  'visible', 'label', 'href'
              )));

      if (!isset($attribs['active'])) {
        $attribs['active'] = false;
      }
      
      if ($attribs['active'] && stripos($attribs['class'], 'sitereview_listtype_') != false) {
        $listingtype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listingtype_id', null);
        $cls=$attribs['class']." ";
        if (stripos($cls, 'sitereview_listtype_' . $listingtype_id." ") == false) {
          $attribs['active'] = false;
        }
      }
      ?>
      <?php if (isset($attribs['isseparator']) && $attribs['isseparator']): ?>
        <li data-role="list-divider"  class="list_divider" class="<?php echo $attribs['class'] ?>" >
          <strong><?php echo $this->translate($item->getLabel()) ?></strong>
          <?php if (isset($attribs['bubole']) && $attribs['bubole']) : ?>
            <span><?php echo $attribs['bubole']; ?></span>  
          <?php endif; ?>
        </li>
      <?php else: ?>
        <li data-corners="true"  data-shadow="false" data-icon="false"    class="list <?php if ($attribs['active']): ?> ui-btn-active<?php endif; ?>"> 
          <?php if ($item->get('icon')): ?>
            <a href="<?php echo $item->getHref() ?>" class="<?php echo $attribs['class'] ?>"
               <?php if ($item->get('data-rel')): ?> data-rel = "<?php echo $item->get('data-rel') ?>" <?php endif; ?>
                <?php if ($item->get('target')): ?> target = "<?php echo $item->get('target') ?>" <?php endif; ?>
 <?php if ($item->get('data-ajax')): ?> data-ajax= "<?php echo $item->get('data-ajax') ?>" <?php endif; ?>>
              <i class="ui-image" style="background-image: url('<?php echo $item->get('icon'); ?>');"></i>
            <?php else: ?>
              <a href="<?php echo $item->getHref() ?>" class="<?php echo $attribs['class'] ?>"                 <?php if ($item->get('target')): ?> target = "<?php echo $item->get('target') ?>" <?php endif; ?>
                <?php if ($item->get('data-rel')): ?> data-rel = "<?php echo $item->get('data-rel') ?>" <?php endif; ?> <?php if ($item->get('data-ajax')): ?> data-ajax= "<?php echo $item->get('data-ajax') ?>" <?php endif; ?>  >
                <i class="ui-menu-icon">
                  <?php if (isset($attribs['bubole']) && $attribs['bubole']) : ?><span class="count-bubble"><?php echo $attribs['bubole']; ?></span><?php endif; ?>
                </i>
              <?php endif; ?>
              <div class="content">
                <?php echo $this->translate($item->getLabel()) ?>
              </div>
            </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <ul <?php echo $this->dataHtmlAttribs("navigation_dashboard", array('data-role' => "listview", 'data-inset' => "true", 'data-theme' => 'd', 'data-corners' => "false")); ?> class="main-navigation navigation_dashboard_list">
    <?php
    $count = 0;
    foreach ($this->navigation as $item):
      $count++;
      // print_r(array_filter($item->toArray()));  die;
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                  'reset_params', 'route', 'module', 'controller', 'action', 'type',
                  'visible', 'label', 'href'
              )));
      if (!isset($attribs['active'])) {
        $attribs['active'] = false;
      }
      if ($attribs['active'] && stripos($attribs['class'], 'sitereview_listtype_') != false) {
        $listingtype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listingtype_id', null);
        $cls=$attribs['class']." ";
        if (stripos($cls, 'sitereview_listtype_' . $listingtype_id." ") == false) {

          $attribs['active'] = false;
        }
      }
      ?>
      <?php if (isset($attribs['isseparator']) && $attribs['isseparator']): ?>
        <li class="ui-li-divider ui-bar-d">
          <?php echo $this->translate($item->getLabel()) ?>
        </li>
      <?php else: ?>
        <li data-corners="false" data-shadow="false" data-icon="false" <?php if ($attribs['active']): ?> class='ui-btn-active'<?php endif; ?> >   
          <a href="<?php echo $item->getHref() ?>" class="<?php echo $attribs['class']; ?>" active="<?php echo $attribs['active']?1:0; ?>"  <?php if ($item->get('data-rel')): ?> data-rel = "<?php echo $item->get('data-rel') ?>" <?php endif; ?>  <?php if ($item->get('data-ajax')): ?> data-ajax= "<?php echo $item->get('data-ajax') ?>" <?php endif; ?>
           <?php if ($item->get('target')): ?> target = "<?php echo $item->get('target') ?>" <?php endif; ?>   > 
            <div class="primarywrap">
              <div class="ui-menu-icon-wrapper">
                <?php if ($item->get('icon')): ?>
                  <i class="ui-image" style="background-image: url('<?php echo $item->get('icon'); ?>');"><?php if (isset($attribs['bubole']) && $attribs['bubole']) : ?><span class="count-bubble"><?php echo $attribs['bubole']; ?></span><?php endif; ?></i>
                <?php else: ?>
                  <i class="ui-menu-icon"><?php if (isset($attribs['bubole']) && $attribs['bubole']) : ?><span class="count-bubble"><?php echo $attribs['bubole']; ?></span><?php endif; ?></i>
                <?php endif; ?>
              </div>
              <div class="content">
                <?php echo $this->translate($item->getLabel()); ?>
              </div>
            </div>    
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

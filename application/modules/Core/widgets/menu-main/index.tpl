<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div class="main_menu_navigation scrollbars">
<?php
  echo $this->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->setPartial(array('_navFontIcons.tpl', 'core'))
    ->render();
?>
</div>
<div class="core_main_menu_toggle panel-toggle "></div>
<script type="text/javascript">
  en4.core.layout.setLeftPannelMenu('<?php echo $this->menuType?>');
</script>

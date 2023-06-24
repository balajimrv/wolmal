<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php echo $this->form->render($this) ?>

<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
$allowHtml = (bool) $settings->getSetting('group_html', 0);
$allowBbcode = (bool) $settings->getSetting('group_bbcode', 0);
?>

<?php if ($allowHtml || $allowBbcode): ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.tinymceditor', 0)): ?>
                        setTimeout(function() {
                          sm4.core.tinymce.showTinymce();
                        }, 100);
  <?php endif; ?>	});
  </script>
<?php endif; ?>
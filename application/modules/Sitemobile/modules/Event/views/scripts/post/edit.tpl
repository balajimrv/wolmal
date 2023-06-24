<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php echo $this->form->render($this) ?>

<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
$allowHtml = (bool) $settings->getSetting('event_html', 0);
$allowBbcode = (bool) $settings->getSetting('event_bbcode', 0);
?>

<?php if ($allowHtml || $allowBbcode): ?>
  <script type="text/javascript">
    $(document).ready(function() {
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.tinymceditor', 0)): ?>
                        setTimeout(function() {
                          sm4.core.tinymce.showTinymce();
                        }, 100);
  <?php endif; ?>
                    });
  </script>
<?php endif; ?>
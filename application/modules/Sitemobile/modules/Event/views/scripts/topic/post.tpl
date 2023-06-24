<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: post.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div data-role="navbar" role="navigation" data-iconpos="right">
  <ul>
    <li><a data-icon="arrow-r" href="<?php echo $this->event->getHref(); ?>"><?php echo $this->event->getTitle(); ?></a></li>
    <li><a data-icon="arrow-d" class="ui-btn-active ui-state-persist"><?php echo $this->translate('Discussions'); ?></a></li>
  </ul>
</div>

<?php if ($this->message)
  echo $this->message ?>

<?php if ($this->form)
  echo $this->form->render($this) ?>

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
<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="global_form">
  <?php if ($this->form->saveSuccessful): ?>
    <h3><?php echo $this->translate('Settings were successfully saved.'); ?></h3>
  <?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div>

<?php if (Zend_Controller_Front::getInstance()->getRequest()->getParam('format') == 'html'): ?>
  <script type="text/javascript">
   sm4.core.runonce.add(function () {
      var req = new Form.Request($$('.global_form')[0], $('global_content'), {
        requestOptions : {
          url : '<?php echo $this->url(array()) ?>'
        },
        extraData : {
          format : 'html'
        }
      });
    });
  </script>
<?php endif; ?>
<script type="text/javascript">
   sm4.core.runonce.add(function () {
    if (typeof $.mobile.activePage.find('#facebook-element').get(0) != 'undefined')
          $.mobile.activePage.find('#facebook-element').find('a').attr('data-ajax', 'false');
       if (typeof $('#twitter-element').get(0) != 'undefined') 
          $.mobile.activePage.find('#twitter-element').find('a').attr('data-ajax', 'false');
    });
  </script>
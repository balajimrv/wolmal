<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
$url = $this->url(array('module' => 'core', 'controller' => 'help', 'action' => 'terms'), 'default', true);
?>

<script type="text/javascript">

    sm4.core.runonce.add(function () {
        setTimeout(function () {
            if ($('#terms-wrapper')) {
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                    $('.global_form').find('#terms-wrapper').after($("<div>" + '<?php echo sprintf($this->string()->escapeJavascript($this->translate("%1sClick here%2s to read the terms of service")), '<a class="ui-link" href="' . $this->url(array('module' => 'core', 'controller' => 'help', 'action' => 'terms'), 'default', true) . '" >', '</a>'); ?>' + '</div>'));
<?php else: ?>
                    $('.global_form').find('#terms-wrapper').after($("<div>" + '<?php echo sprintf($this->string()->escapeJavascript($this->translate("%1sClick here%2s to read the terms of service")), '<a class="ui-link" href="' . $this->url(array('module' => 'core', 'controller' => 'help', 'action' => 'terms'), 'default', true) . '" target="_blank">', '</a>'); ?>' + '</div>'));
<?php endif; ?>
            }
        }, 200);
        if ($('#username') && $('#profile_address')) {
            $('#profile_address').html($('#profile_address')
                    .html()
                    .replace('<?php echo /* $this->translate( */'yourname'/* ) */ ?>',
                            '<span id="profile_address_text"><?php echo $this->translate('yourname') ?></span>'));

            $('#username').bind('keyup', function () {
                var text = '<?php echo $this->translate('yourname') ?>';
                if ($(this).val() != '') {
                    text = $(this).val();
                }

                $('#profile_address_text').html(text.replace(/[^a-z0-9]/gi, ''));
            });
            // trigger on page-load
            if ($('#username').val().length) {
                $('#username').trigger('keyup');
            }
        }
    });
    //<![CDATA[
    //   $(window).bind('load', function() {


    //   });
    //]]>
</script>
<?php
if ($this->form->terms):
    $this->form->terms->setDescription('I have read and agree to the terms of service');
endif;
?>
<?php echo $this->form->render($this) ?>

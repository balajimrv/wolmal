<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: unhidephoto.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->is_ajax) : ?>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php endif; ?>

<style type="text/css">
  .disable button{background-color:#ccc;border-color:#ddd;}
  .global_form_popup > div > div > h3 + p + div{margin-top:5px;}
</style>

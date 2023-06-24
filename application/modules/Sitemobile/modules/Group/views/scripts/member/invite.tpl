<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: invite.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->count > 0): ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function(){
      $('#selectall').bind('click', function(event) {
        if(this.checked) {
          $("input[type='checkbox']").prop("checked",true).checkboxradio("refresh"); 
        } else {
          $("input[type='checkbox']").prop("checked",false).checkboxradio("refresh"); 
        }
      });
    });
  </script>
  <div class="ui-member-invite-popup">
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  </div>  
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have no friends you can invite.'); ?>
    </span>
  </div>
<?php endif; ?>
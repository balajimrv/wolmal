<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<h2>
  <?php echo ( $this->title ? $this->translate($this->title) : '' ) ?>
</h2>

<script type="text/javascript">
  function skipForm() {
    $('#skip').attr('value', 'skipForm');
    $.mobile.activePage.find('#SignupForm').submit();
  }
  function finishForm() {
    $.mobile.activePage.find('#nextStep').attr('value', 'finish');
  }
</script>

<?php
echo $this->partial($this->script[0], $this->script[1], array(
    'form' => $this->form
))
?>

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: error.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var goToContactPageAfterError = function() {
    $.mobile.changePage('<?php echo $this->url(array('controller' => 'help', 'action' => 'contact'), 'default', true) ?>' + '?name=' + '<?php echo urlencode(base64_encode($this->errorName)) ?>' + '&loc=' + '<?php echo urlencode(base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>' + '&time=' + '<?php echo urlencode(base64_encode(time())) ?>');

  }
</script>

<div>

  <h2>
    <?php echo $this->translate('We\'re sorry!') ?>
  </h2>

  <p>
  <?php echo $this->translate('We are currently experiencing some technical ' .
      'issues. Please try again or report this to your site administrator ' .
      'using the %1$scontact%2$s form.',
      '<a href="javascript:void(0);" onclick="goToContactPageAfterError();return false;">',
      '</a>'
      ) ?>
  </p>
  <br />

  <p>
    <?php echo $this->translate('Administrator: Please check the error log in ' .
        'your admin panel for more information regarding this error.') ?>
    <?php //echo $this->translate('Some information is available below:') ?>
  </p>
  <br />
  
  <p>
    <?php printf($this->translate('Error Code: %s'), $this->error_code); ?>
  </p>
  <br />

  <?php /*
  <p class="small">
    Type: <?php echo $this->errorName ?>
    <br />
    Location: <?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>
  </p>
   */ ?>

  <?php if( isset($this->error) && 'development' == APPLICATION_ENV ): ?>
    <br />
    <br />
    <pre><?php echo $this->error; ?></pre>
  <?php endif; ?>

</div>
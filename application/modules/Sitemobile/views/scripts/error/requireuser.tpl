<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: requireuser.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  if( $this->form ):
    echo $this->form->render($this);
  else:
    echo $this->translate('Please sign in to continue.');
  endif;
?>
<script type="text/javascript">
    sm4.core.runonce.add(function() { 
       if (typeof $('#facebook-element').get(0) != 'undefined')
          $('#facebook-element').find('a').attr('data-ajax', 'false');
       if (typeof $('#twitter-element').get(0) != 'undefined') 
          $('#twitter-element').find('a').attr('data-ajax', 'false');
      
    })
    
</script> 
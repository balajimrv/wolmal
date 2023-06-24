<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: requireuser.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php

if ($this->form):
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
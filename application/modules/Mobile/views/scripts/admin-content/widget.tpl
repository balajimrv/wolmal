<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widget.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<div style="padding: 10px;">

  <?php if( $this->form ): ?>

    <script type="text/javascript">
      window.addEvent('domready', function() {
        var params = parent.pullWidgetParams();
        var info = parent.pullWidgetTypeInfo();
        $H(params).each(function(value, key) {
          if( $type(value) == 'array' ) {
            value.each(function(svalue){
							svalue = svalue.replace('.', '');
							svalue =  svalue.replace('-', '');
							svalue =  svalue.replace('-', '');
              if( $(key + '-' + svalue) ) {
                $(key + '-' + svalue).set('checked', true);
              }
            });
          } else if( $(key) ) {
            $(key).value = value;
          } else if( $(key + '-' + value) ) {
            $(key + '-' + value).set('checked', true);
          }
        });
        $$('.form-description').set('html', info.description);
      })
    </script>

    <?php echo $this->form->render($this) ?>

  <?php elseif( $this->values ): ?>

    <script type="text/javascript">
      parent.setWidgetParams(<?php echo Zend_Json::encode($this->values) ?>);
      parent.Smoothbox.close();
    </script>

  <?php else: ?>

    <?php echo $this->translate("Error: no values") ?>
    
  <?php endif; ?>

</div>
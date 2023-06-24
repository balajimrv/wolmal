<?php
$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
if( $this->viewer()->getIdentity() && Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin') ){ ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('#image_path').addEvent('change', function(){
            var src = $$(this).get('value');
            $$('#img_preview').set('src', (src != '') ? src : 'http://placehold.it/1x1');
        });
    });
</script>
<?php if( $this->form ){ ?>

<?php echo $this->form->render($this) ?>

<?php } elseif( $this->status ){ ?>

<div style="padding: 10px;"><?php echo $this->translate("Your changes have been saved.") ?></div>

<script type="text/javascript">
    var landing_action = "<?php echo $this->url(array('module' => 'admin', 'controller' => 'zephyrtheme', 'action' => 'landingpage', 'item_no' => null)) ?>";
    setTimeout(function() {
        parent.window.location.replace(landing_action)
    }, 500);
</script>

<?php } ?>

<?php }  else { echo '<div style="padding: 10px;">'.$this->translate("Only admins can edit this!").'</div>'; } ?>
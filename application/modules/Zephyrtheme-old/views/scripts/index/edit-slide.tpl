<?php
$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
if( $this->viewer()->getIdentity() && Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin') ){ ?>

<?php if( $this->form ){ ?>

<?php echo $this->form->render($this); ?>

<?php } elseif( $this->status ){ ?>

<div style="padding: 10px;"><?php echo $this->translate("Your changes have been saved.") ?></div>

<script type="text/javascript">
    var new_img_path = '<?php echo $this->new_img_path ?>',
        img_id = '<?php echo $this->img_id ?>';

    setTimeout(function() {
        console.log(parent.$$('tr.item_' + img_id).getChildren('.image'));
        parent.$$('tr.item_' + img_id).getChildren('.image')[0].getFirst().set('src', (new_img_path != '') ? new_img_path : 'http://placehold.it/1x1');

        parent.Smoothbox.close();
    }, 500);
</script>

<?php } ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('#image_path').addEvent('change', function(){
            var src = $$(this).get('value');
            $$('#img_preview').set('src', (src != '') ? src : 'http://placehold.it/1x1');
        });

        var src_old = $$('#image_path').get('value');
        $$('#img_preview').set('src', (src_old != '') ? src_old : 'http://placehold.it/1x1');
    });
</script>
<?php }  else { echo '<div style="padding: 10px;">'.$this->translate("Only admins can edit this!").'</div>'; } ?>
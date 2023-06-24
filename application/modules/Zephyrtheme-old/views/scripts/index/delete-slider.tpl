<?php if( $this->form ){ ?>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php } else { ?>

<div style="padding: 10px;">
    <?php
    $slider_id = isset($this->slider_id) ? $this->slider_id : '';
    echo $this->translate("Deleted");
    ?>
</div>

<script type="text/javascript">
    var slider_item = '<?php echo $slider_id ?>',
        new_count = parseInt($$('#slider_items_no').get('value'));
    setTimeout(function() {
        parent.$$('.item_' + slider_item).fade(0).destroy();
        $$('#slider_items_no').set('value', new_count - 1);
        parent.Smoothbox.close();
    }, 500);
</script>

<?php } ?>
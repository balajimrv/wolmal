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
    var slider_item = '<?php echo $slider_id ?>';
    setTimeout(function() {
        parent.$$('.item_' + slider_item).fade(0).destroy();
        parent.Smoothbox.close();
    }, 500);
</script>

<?php } ?>
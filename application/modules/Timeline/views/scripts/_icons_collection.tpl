<div class="icons_collection_activator">
    <?php echo $this->translate('Icons Collection') ?> &darr;
    <ul>
        <?php $iconsDir = $this->baseUrl() . '/application/modules/Timeline/externals/images/icons_collection/';
            foreach ($this->icons_collection as $icon):
        ?>
            <li onclick="javascript:set_icon_collection('<?php echo $this->tab_name ?>', '<?php echo $icon ?>');" >
                <img alt="Icon from collection" src="<?php echo $iconsDir . $icon ?>" />
            </li>
        <?php endforeach;?>
    </ul>
</div>
<?php
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/ysr-crop.js')
                       ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/core.js');
?>
<div style="width: 460px;height: 320px;" id="form_upload_box">
    <?php echo $this->form ?>
</div>
<div id="loading" style="display: none;">
    <img alt="<?php echo $this->translate("Loading..."); ?>"  src="<?php echo $this->baseUrl() . '/application/modules/Core/externals/images/loading.gif' ?>" />
</div>
<div id="crop_div" style="display: none;">
    <h3><?php echo $this->translate("Crop image"); ?></h3>
    <ul>
        <li><?php echo $this->translate("Select the area on your image to create profile cover."); ?></li>
 
         <li><?php echo $this->translate("Hold SHIFT to select a square area."); ?></li>
    </ul>
    <div id="imgouter">
         <div id="cropframe">
             <div id="draghandle"></div>
             <div id="resizeHandleXY" class="resizeHandle"></div>
         </div>
         <div id="imglayer"></div>
    </div>
<div style="clear:both; overflow: hidden; height:10px"></div>
    <button onclick="window.ch.doCrop()">Crop</button> or <a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" id="button_cancel"><?php echo $this->translate("Cancel"); ?></a>
</div>
<div id="success_message_box" style="display: none;">
    <div class="global_form_popup_message">
      <?php echo $this->translate("Cover changed.");?>
    </div>
</div>
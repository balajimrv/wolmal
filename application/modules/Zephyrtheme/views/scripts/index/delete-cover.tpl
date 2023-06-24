<div class="global_form_box">
    <script type="text/javascript">
        window.addEvent('domready', function() {
            $('delete_cover_btn').addEvents({
                click: function() {
                    en4.core.request.send(new Request.JSON({
                        url : en4.core.baseUrl + 'zephyrtheme/index/delete-cover',
                        method : 'POST',
                        data : {
                            format : 'json'
                        },
                        onSuccess : function(r)
                        {
                            if (typeof r.default_cover !== 'undefined')
                            {
                                parent.$('memberprofile_cover').setStyle('background-image', 'url(' + r.default_cover + ')');
                                parent.$('memberprofile_cover_delete').remove();
                                parent.Smoothbox.close();
                            }
                        }
                    }));
                }
            });
        });
    </script>
    <h3>Delete Cover</h3>
    <p class="form-description"><?php echo $this->translate("Are you sure you want to delete your profile cover image?") ?></p>
    <fieldset id="fieldset-buttons">
        <button id="delete_cover_btn" type="button"><?php echo $this->translate('Yes');?></button>
        <button onclick="javascript:parent.Smoothbox.close();" type="button"><?php echo $this->translate('Cancel');?></button>
    </fieldset>
</div>
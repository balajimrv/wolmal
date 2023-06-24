<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
    var taxStatusEnable = 0;
    function selectAll()
    {
        var i;
        var multidelete_form_tax = $('multidelete_form_tax1');
        var inputs = multidelete_form_tax.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
  
    function showsmoothbox(url) 
    {
        Smoothbox.open(url);
    }
  
    function enabletax(id)
    {
        if( taxStatusEnable == 0 )
        {
            taxStatusEnable = 1;
            $('show_tax_status_image_' + id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + "sitestoreproduct/tax/tax-enable/store_id/<?php echo $this->store_id ?>",
                method : 'POST',
                data : {
                    format : 'json',
                    is_ajax : 1,
                    id : id
                },
                onSuccess : function(responseJSON) 
                { 
                    taxStatusEnable = 0;
                    if( responseJSON.activeFlag == '0') 
                        $('show_tax_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif" />';
                    else
                        $('show_tax_status_image_' + id).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif" />';
                }
            })
        ); 
        }
    }

    en4.core.runonce.add(function(){
        var anchor = $('tax_pagination').getParent();
        if(document.getElementById('store_tax_previous')) {
            document.getElementById('store_tax_previous').style.display = '<?php echo ( $this->user_paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        }
        if(document.getElementById('store_tax_next')) {
            document.getElementById('store_tax_next').style.display = '<?php echo ( $this->user_paginator->count() == $this->user_paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
        }

        if(document.getElementById('admin_store_tax_previous')){
            document.getElementById('admin_store_tax_previous').style.display = '<?php echo ( $this->admin_paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        }
        if(document.getElementById('admin_store_tax_next')) {
            document.getElementById('admin_store_tax_next').style.display = '<?php echo ( $this->admin_paginator->count() == $this->admin_paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
        }
        $('multidelete_form_tax1').removeEvents('submit').addEvent('submit', function(e) {
            e.stop();
            $('delete_selected_tax_spinner').addClass('loading');
            var i;
            var tax_id_array = new Array();
            var multidelete_form_tax = $('multidelete_form_tax1');
            var inputs = multidelete_form_tax.elements;
            for (i = 1; i < inputs.length; i++) {
                if (!inputs[i].disabled) {
                    if(inputs[i].checked ){
                        if(inputs[i].value){
                            tax_id_array[i] = inputs[i].value;
                        }
                    }
                }
            }
            if(tax_id_array.length == 0){
                return alert("<?php echo $this->translate("You don't select any tax entry.Please select at least one.") ?>");
            }          
            var cofirmation = confirm("<?php echo $this->translate("Are you sure you want to delete the selected Taxes?") ?>");
            if(cofirmation != 1){
                return;
            }

            en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'sitestoreproduct/tax/multidelete-tax',
                method : 'POST',
                onRequest: function(){
                    $('delete_selected_tax_spinner').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
                },
                data : {
                    format : 'json',
                    method : 0,
                    tax_id : tax_id_array
                },
                onSuccess : function(responseJSON) {
                    $('delete_selected_tax_spinner').innerHTML = '';
                    if(responseJSON.success == 1){
                        window.location.assign('<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'index'), 'sitestore_store_dashboard', true); ?>');
                    }
                }
            })
        ); 
        });


        $('store_tax_previous').removeEvents('click').addEvent('click', function(){
            $('tax_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            var tempTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'index', 'page' => $this->user_paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';
            if(tempTaxPaginationUrl && typeof history.pushState != 'undefined') { 
                history.pushState( {}, document.title, tempTaxPaginationUrl );
            }


            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'sitestoreproduct/tax/index/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    multidelete : 1,
                    adminpage : <?php echo sprintf('%d', $this->admin_paginator->getCurrentPageNumber()) ?>,
                    page : <?php echo sprintf('%d', $this->user_paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
                    $('tax_spinner_prev').innerHTML = '';
                }
            }), {
                'element' : anchor
            })
        });

        $('store_tax_next').removeEvents('click').addEvent('click', function(){
            $('tax_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            var tempTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'index', 'page' => $this->user_paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';
            if(tempTaxPaginationUrl && typeof history.pushState != 'undefined') { 
                history.pushState( {}, document.title, tempTaxPaginationUrl );
            }

            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'sitestoreproduct/tax/index/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    multidelete : 1,
                    adminpage : <?php echo sprintf('%d', $this->admin_paginator->getCurrentPageNumber()) ?>,
                    page : <?php echo sprintf('%d', $this->user_paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
                    $('tax_spinner_next').innerHTML = '';
                }
            }), {
                'element' : anchor
            })
        });
    
        // Admin TAX Pagination
        $('admin_store_tax_previous').removeEvents('click').addEvent('click', function(){
            $('admin_tax_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            var tempAdminTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'index', 'page' => $this->admin_paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';
            if(tempAdminTaxPaginationUrl && typeof history.pushState != 'undefined') { 
                history.pushState( {}, document.title, tempAdminTaxPaginationUrl );
            }


            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'sitestoreproduct/tax/index/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    multidelete : 1,
                    page : <?php echo sprintf('%d', $this->user_paginator->getCurrentPageNumber()) ?>,
                    adminpage : <?php echo sprintf('%d', $this->admin_paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
                    $('admin_tax_spinner_prev').innerHTML = '';
                }
            }), {
                'element' : anchor
            })
        });

        $('admin_store_tax_next').removeEvents('click').addEvent('click', function(){
            $('admin_tax_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            var tempAdminTaxPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'index', 'page' => $this->admin_paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';
            if(tempAdminTaxPaginationUrl && typeof history.pushState != 'undefined') { 
                history.pushState( {}, document.title, tempAdminTaxPaginationUrl );
            }

            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'sitestoreproduct/tax/index/store_id/' + <?php echo sprintf('%d', $this->store_id) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    multidelete : 1,
                    page : <?php echo sprintf('%d', $this->user_paginator->getCurrentPageNumber()) ?>,
                    adminpage : <?php echo sprintf('%d', $this->admin_paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {        
                    $('admin_tax_spinner_next').innerHTML = '';
                }
            }), {
                'element' : anchor
            })
        });
    });
</script>
<div class="sitestoreproduct_manage_store">
  <h3 class="mbot10"><?php echo $this->translate('Manage Taxes') ?></h3>
<div class="mtop10">
    <?php echo $this->translate("Here, you can configure tax depending on order billing / shipping locations. For each tax, you can configure tax percentage / amount for various locations. The amount for the taxes created by you will be payable to you. <br /><strong>Note:</strong> General taxes on %s will be applied on all the products in addition to the taxes created by you.", $this->site_title); ?>
</div>
<?php if( !empty($this->canEdit) ) : ?>
<a href="javascript:void(0);" id="tax_addtax" class="buttonlink seaocore_icon_add mbot10 mtop10" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'add-tax', 'store_id' => $this->store_id), 'default', true) ?>");return false;' ><?php echo $this->translate("Create New Tax") ?></a>
<?php endif; ?>
<div id="tax_pagination">

    <!-- START ADMIN SIDE TAX WORK --> 
    <?php if (count($this->admin_paginator)): ?>
    <h3 class="mbot10"><?php echo $this->translate("General taxes on %s", $this->site_title); ?></h3>
        <div class="product_detail_table sitestoreproduct_data_table fleft">
            <table>
                <tr class="product_detail_table_head">
                    <th><?php echo $this->translate("Title") ?></th>
                    <th><?php echo $this->translate("Rate Depends On") ?></th>
                    <th><?php echo $this->translate("Options") ?></th>
                </tr>
                <?php foreach ($this->admin_paginator as $item): ?>
                    <tr>
                        <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 100) ?></td>
                        <?php if ($item->rate_dependency == 0): ?>
                            <td><?php echo $this->translate("Shipping Address") ?></td>
                        <?php else: ?>
                            <td><?php echo $this->translate("Billing Address") ?></td>
                        <?php endif; ?>
                        <td><a href="javascript:void(0);" onclick='manage_store_dashboard(52, "<?php echo "manage-rate/tax_id/" . $item->tax_id . "/pageno/" . $this->adminpage ?>", "tax");' ><?php echo $this->translate("view tax details") ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <br />
        <div>
            <div id="admin_store_tax_previous" class="paginator_previous"> <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => '',
                    'class' => 'buttonlink icon_previous'
                ));
                ?> <span id="admin_tax_spinner_prev"></span> </div>
            <div id="admin_store_tax_next" class="paginator_next"> <span id="admin_tax_spinner_next"></span> <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => '',
                'class' => 'buttonlink_right icon_next'
            ));
                ?> </div>
        </div><br />
    <?php endif; ?>
    <!-- END ADMIN SIDE TAX WORK --> 


    <!-- START USER SIDE TAX WORK --> 
    <?php if (count($this->user_paginator)): ?>
        <h3 class="mbot10"><?php echo $this->translate("Taxes specific to your store"); ?></h3>
        <form id="multidelete_form_tax1" method="post" >
            <div class="product_detail_table sitestoreproduct_data_table fleft">
                <table>
                    <tr class="product_detail_table_head">
                      <?php if( !empty($this->canEdit) ) : ?>
                        <th><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                      <?php endif; ?>
                        <th><?php echo $this->translate("Title") ?></th>
                        <th><?php echo $this->translate("Rate Depends On") ?></th>
                        <th class="txt_center"><?php echo $this->translate("Status") ?></th>
                        <th><?php echo $this->translate("Options") ?></th>
                    </tr>
                    <?php foreach ($this->user_paginator as $item): ?>
                        <tr>
                          <?php if( !empty($this->canEdit) ) : ?>
                            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->tax_id ?>' value="<?php echo $item->tax_id ?>" <?php echo empty($item->store_id) ? 'DISABLED' : ''; ?>/></td>
                          <?php endif; ?>
                            <td title="<?php echo $item->title ?>"><?php echo $this->string()->truncate($this->string()->stripTags($item->title), 100) ?></td>
                            <?php if ($item->rate_dependency == 0): ?>
                                <td><?php echo $this->translate("Shipping Address") ?></td>
                            <?php else: ?>
                                <td><?php echo $this->translate("Billing Address") ?></td>
                            <?php endif; ?>

                            <!-- SOWING STATUS BUTTON ACCORDING TO STATUS IN DATABASE -->
                            <?php if (!empty($item->store_id)): ?>
                                <?php if (!empty($item->status)): ?>
                                    <td class="txt_center">
                                      <?php if( !empty($this->canEdit) ) : ?>
                                      <a id="show_tax_status_image_<?php echo $item->tax_id ?>" href="javascript:void(0);" onclick="enabletax(<?php echo $item->tax_id ?>)" title="<?php echo $this->translate("Disable Tax") ?>">
                                      <?php endif; ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable1.gif' ?>" />
                                      <?php if( !empty($this->canEdit) ) : ?>
                                      </a>
                                      <?php endif; ?>
                                    </td>
                                <?php else: ?>
                                    <td class="txt_center">
                                      <?php if( !empty($this->canEdit) ) : ?>
                                      <a id="show_tax_status_image_<?php echo $item->tax_id ?>" href="javascript:void(0);" onclick="enabletax(<?php echo $item->tax_id ?>)" title="<?php echo $this->translate("Enable Tax") ?>">
                                      <?php endif; ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/region_enable0.gif' ?>" />
                                      <?php if( !empty($this->canEdit) ) : ?>
                                      </a>
                                      <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($item->store_id)): ?>
                                <td>
                                  <?php if( !empty($this->canEdit) ) : ?>
                                  <a href="javascript:void(0);" onclick='manage_store_dashboard(52, "<?php echo "manage-rate/tax_id/" . $item->tax_id . "/pageno/" . $this->page ?>", "tax");' ><?php echo $this->translate("manage locations") ?></a> | 
                                  <a href="javascript:void(0);" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'edit-tax', 'store_id' => $this->store_id, 'tax_id' => $item->tax_id), 'default', true) ?>");return false;' ><?php echo $this->translate("edit") ?></a> | 
                                  <a href="javascript:void(0);" onclick='showsmoothbox("<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'delete-tax', 'store_id' => $this->store_id, 'tax_id' => $item->tax_id), 'default', true) ?>");return false;' ><?php echo $this->translate("delete") ?></a>
                                  <?php else: ?>
                                  -
                                  <?php endif; ?>
                                </td>
                            <?php else : ?>
                                <td><a href="javascript:void(0);" onclick='manage_store_dashboard(52, "<?php echo "manage-rate/tax_id/" . $item->tax_id . "/pageno/" . $this->page ?>", "tax");' ><?php echo $this->translate("manage locations") ?></a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <br />
            <div>
                <div id="store_tax_previous" class="paginator_previous"> <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                        'onclick' => '',
                        'class' => 'buttonlink icon_previous'
                    ));
                    ?> <span id="tax_spinner_prev"></span> </div>
                <div id="store_tax_next" class="paginator_next"> <span id="tax_spinner_next"></span> <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => '',
                    'class' => 'buttonlink_right icon_next'
                ));
                    ?> </div>
            </div>  
            <br />
            <?php if( !empty($this->canEdit) ) : ?>
            <div class='buttons'>
                <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
                <span id="delete_selected_tax_spinner"></span>
            </div>
            <?php endif; ?>
            <br />
        </form>
    </div>
<?php else: ?>
    <div class="tip"> <span> <?php echo $this->translate("You have not configured any taxes for your store.") ?> </span> </div>
<?php endif; ?>
</div>

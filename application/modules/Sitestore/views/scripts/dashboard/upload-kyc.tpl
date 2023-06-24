<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-kyc.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    //var submitformajax = 1;
</script>
<style>

    .sitestore-table{
        width:100%;
        margin-top: 20px;
        border: 1px solid #e3e3e3;
    }   
    .sitestore-table thead tr{
        background: #e3e3e3; 
        color: #000;
        font-weight: bold;
    }
    .sitestore-table tr td{
        padding: 8px;
    }
    .sitestore-table tbody tr td{
        font-size: 11px;
    }

</style>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php if (empty($this->is_ajax)) : ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

    <div class="layout_middle">
        <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>

        <div class="sitestore_edit_content">
            <div class="sitestore_edit_header">
                <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), $this->translate('VIEW_STORE')) ?>
                <h3><?php echo $this->translate('Dashboard: ') . $this->sitestore->title; ?></h3>
            </div>
            <div id="show_tab_content">
                <div class="sitestore_overview_editor">
                <?php endif; ?>
                <?php echo $this->form->render($this); ?>
                <?php
                if ($this->mangopayuser) :
                    $kycDocuments = $this->adminGateway->getService()->getKycdocuments($this->mangoPayUserId);

                    if (count($kycDocuments) > 0 && !empty($this->mangoPayUserId)):
                        ?>
                    <br />
                        <h3><?php echo $this->translate("Below you can view the list of KYC documents that you have uploaded for your MangoPay account along with its validation status."); ?></h3>
                        <table class="sitestore-table">
                            <thead>
                                <tr>
                                    <td>Sn.</td>
                                    <td>ID</td>
                                    <td>Creation Date</td>
                                    <td>Type</td>
                                    <td>Status</td>
                                    <td>Tag</td>
                                    <td>Message</td>
                                </tr> 
                            </thead>
                            <tbody>
                                <?php
                                foreach ($kycDocuments as $k => $document):
                                    ?>
                                    <tr>
                                        <td><?= ($k + 1) ?></td>
                                        <td><?= $document->Id ?></td>
                                        <td><?= date('d-m-Y H:i:s', $document->CreationDate) ?></td>
                                        <td><?= $document->Type == 'IDENTITY_PROOF' ? 'Proof of identity' : ($document->Type == 'ADDRESS_PROOF' ? 'Proof of address' : 'Unknown') ?></td>
                                        <td><?= $document->Status ?></td>
                                        <td><?= $document->Tag ?></td>
                                        <td><?= $document->RefusedReasonMessage ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                            endif;
                        endif;
                        ?> 
                    </tbody>
                </table>
<?php if (empty($this->is_ajax)) : ?>
                </div>	
            </div>

        </div>

    </div>
<?php endif; ?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-countries-code.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="global_form_popup">
  <a href=<?php echo $this->url(array('action' => 'download')) ?><?php echo '?path=' . urlencode('countries_code.pdf');?> target='downloadframe' class="buttonlink icon_sitestores_download_csv"><?php echo $this->translate('Download Country Codes')?></a>
  <br />
  <br />
  <table class="admin_table sr_sitestoreproduct_statistics_table">
    <tr>
      <th><?php echo $this->translate("Country Name") ?></th>
      <th><?php echo $this->translate("Country Code") ?></th>
    </tr>
    <?php foreach ($this->countriesCode as $keys => $tempCountry) : ?>
          <tr>
            <td><?php echo $tempCountry; ?></td>
            <td><?php echo $keys; ?></td>
          </tr>
    <?php endforeach; ?>
  </table>
  
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function upgradePlugin(url) {
    Smoothbox.open(url);
  }
</script>

<h3>
  <?php echo $this->translate('Latest versions of Extensions for Stores / Marketplace Plugin for your site') ?>
</h3>
<p>
	<?php echo $this->translate('Here, you can upgrade the latest version of these extensions by using ‘Upgrade’ button available in front of all the desired extensions that needs to be upgraded.<br />The latest versions of Extensions for Stores / Marketplace Plugin are also available to you in your SocialEngineAddOns Client Area. Login into your SocialEngineAddOns Client Area here: <a href="http://www.socialengineaddons.com/user/login" target="_blank">http://www.socialengineaddons.com/user/login</a>.'); ?>
</p><br/>

<div class='sociealengineaddons_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<?php
	if( count($this->channel) ):
?>
  <table class='admin_table'>
    <thead>
      <tr>
         <th align="left">
        	<?php echo $this->translate("Extension Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Latest version on SocialEngineAddOns.com"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Version on your website"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Should you Upgrade?"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Upgrade?"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->channel as $item):?>	
			<?php
				$running_version = $item['running_version'];
				$product_version = $item['product_version'];
				$versionInfo = 0;
				$status = $this->translate('No');
				$shouldUpgrade = FALSE;
				if( !empty($running_version) && !empty($product_version) ) {
					$versionInfo = strcasecmp($product_version, $running_version);
					if( $versionInfo > 0 ) {
						$shouldUpgrade = TRUE;
						$status = $this->translate('Yes');
					}
				?>
        <tr>
          <td><?php echo $item['title']; ?></td>
					<td><?php echo $product_version; ?></td>
					<td><?php echo $running_version; ?></td>
					<td><?php echo $status; ?></td>
<td>
  <?php
     $url = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade-plugin', 'name' => $item['name'], 'version' => $item['product_version'], 'ptype' => $item['ptype'], 'key' => $item['key'], 'title' => str_replace("/", "_", $item['title']), 'calling' => 'sitestore'), 'admin_default', true);
     $title = $this->translate("Upgrade '%s' to latest version %s", $item['title'], $product_version);
     if( empty($shouldUpgrade) || empty($this->mod_enabled) ):
      echo '-';
     else:
  ?>
    <button title="<?php echo $title; ?>" style="font-size:11px;padding:2px;" onclick="upgradePlugin('<?php echo $url; ?>')">Upgrade</button>
    <?php endif; ?>
</td>
        </tr>
			<?php } ?>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo '<div class="tip"><span>No plugins by SocialEngineAddOns were found on your site. Click <a href="http://www.socialengineaddons.com/catalog/1/plugins" target="_blank">here</a> to view and purchase them.</span></div>'; ?>
    </span>
  </div>
<?php endif; ?>
<?php if(empty($this->OnSiteModules)) : ?>
  <div class="tip">
    <span>
	<?php echo $this->translate('There are no extensions of "Stores / Marketplace Plugin" available on your site. To see the available extensions for this plugin, please %1$sclick here%2$s', '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">', '</a>.');?>
	    </span>
  </div>
<?php endif; ?>
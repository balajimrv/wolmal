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

<?php  $is_error = 0; ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'log', 'action' => 'index'), $this->translate('Import History'), array('class' => 'buttonlink seaocore_icon_log')) ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'importproduct', 'action' => 'manage'), $this->translate('Manage CSV Import Files'), array('class' => 'buttonlink seaocore_icon_import_manage')) ?><br/><br/>

<div class="importlisting_form">
	<div>
		<h3><?php echo $this->translate('Import Products from a CSV file');?></h3>

		<p>
		 <?php echo $this->translate("This tool allows you to import Products corresponding to the entries from a .csv file. Before starting to use this tool, please read the following points carefully.");?>
		</p>

		<ul class="importlisting_form_list">

			<li>
				<?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done.");?>
			</li>

			<li>
				<?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file . If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in fields like description, price, overview etc. for the entries in the CSV file.");?>
			</li>

     <li>
      <?php echo $this->translate("Product title, description and category are the required fields for all the entries in the file.");?>
    </li>	

    <li>
      <?php echo $this->translate("Categories and sub-categories name should exactly match with the existing categories and sub-categories.");?>
    </li>       

			<li>
				<?php echo $this->translate("Before starting the import process, it is recommended that you should first create Categories, Profile Fields and do Category-Profile mappings from the 'Category-Product Profile Mapping' section.");?>
			</li>

			<li>
				<?php echo $this->translate("In case you want to insert more than one tag for an entry, then the tags string should be separated by hash('#'). For example, if you want to insert 2 tags for an entry - 'tag1' and 'tag2', then tag string for that will be 'tag1#tag2'.");?>
			</li>

			<li>
				<?php echo $this->translate("You can import the maximum of 10,000 Products at a time and if you want to import more, you would have to then repeat the whole process. For example, you have to import 15000 Products. Then, you would have to create 2 CSV files - one having 10,000 entries and another having 5,000 entries corresponding to the Products. After that, just import both the files using 'Import Products' option.");?>
			</li>

			<li>
				<?php echo $this->translate("You can also 'Stop' and 'Rollback' the import process. 'Stop' will just stop the import process going on at that time from that file and 'Rollback' will undo or delete all the Products created from that CSV import file till that time.");?>
			</li>

			<li>
				<?php echo $this->translate("Files must be in the CSV format to be imported. You can also download the demo template below for your reference.");?>
			</li>

		</ul>
		
		<br />
    
    <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
		<a href="<?php echo $this->url(array('action' => 'download')) ?><?php echo '?path=' . urlencode('example_product_import.csv');?>" target='downloadframe' class="buttonlink seaocore_icon_download">
      <?php echo $this->translate('Download the CSV template')?>
    </a>    
		
		<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitestoreproduct', 'controller' => 'admin-importproduct', 'action' => 'import'), $this->translate('Import Products'), array('class' => 'smoothbox buttonlink seaocore_icon_import')) ?>
		<br />
		
	</div>
</div>		


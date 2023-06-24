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

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
?>

<script type="text/javascript">
  var dateAction =function(start_date, end_date){ 
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>

<?php if (count($this->archive_sitestoreproduct)): ?>
  <ul class="seaocore_sidebar_list">
    <?php foreach ($this->archive_sitestoreproduct as $archive): ?>
      <li>
        <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start'] ?>, <?php echo $archive['date_end'] ?>);' <?php if ($this->start_date == $archive['date_start'])
        echo " class='bold'"; ?>><?php echo $archive['label'] ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
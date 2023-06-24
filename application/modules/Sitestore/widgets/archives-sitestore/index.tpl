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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">
  var dateAction =function(start_date, end_date){
   if($("tag"))
     $("tag").value='';
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_archive')){
				form=$('filter_form_archive');
			}
   form.elements['start_date'].value  = start_date;
   form.elements['end_date'].value= end_date;
    form.submit();
  }
</script>
<form id='filter_form_archive' class='global_form_box' method='get' action='<?php echo $this->url(array('module' => 'sitestore', 'action' => 'index'), 'sitestore_general', true) ?>' style='display: none;'>
  <input type="hidden" id="start_date" name="start_date"  value=""/>
  <input type="hidden" id="end_date" name="end_date"  value=""/>
</form>
<?php if (count($this->archive_sitestore)): ?>
  <ul class="sitestore_sidebar_list">
    <?php foreach ($this->archive_sitestore as $archive): ?>
      <li>
        <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start'] ?> , <?php echo $archive['date_end'] ?>);' <?php if ($this->start_date == $archive['date_start'])
      echo " class='bold'"; ?>><?php echo $archive['label'] ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
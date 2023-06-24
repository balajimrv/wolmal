<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
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

  var tagAction = function(tag){
    if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_store')){
				form=$('filter_form_store');
    }   
    form.elements['tag'].value = tag;
    if( $('filter_form'))
    $('filter_form').submit();
		else
		$('filter_form_store').submit();
  }
</script>

<?php if (!empty($this->tag_array)): ?>
	<form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestorevideo_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="tag" name="tag"  value=""/>
  </form>
  <?php $total_maintags = count($this->tag_array) ?>
  <?php if ($total_maintags > 0): ?>
    <h3><?php echo $this->translate('Popular Video Tags'); ?> (<?php echo $total_maintags ?>)</h3>
    <ul class="sitestore_sidebar_list">
      <li>
        <?php foreach ($this->tag_array as $key => $frequency): ?>
          <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
          <?php if($this->tag == $this->tag_id_array[$key]) :?>
            <?php $key =  '<b>'.$key .'</b>';?>
          <?php endif;?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>);' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
        <?php endforeach; ?>
        <br/>
        <b class="explore_tag_link"><?php echo $this->htmlLink(array('route' => 'sitestorevideo_tags', 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;')) ?></b>
      </li>
    </ul>
  <?php endif; ?>
<?php endif; ?>
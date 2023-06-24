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
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<?php
$this->tagstring = "";
if (count($this->userTags)) {
  $count = 0;
  foreach ($this->userTags as $tag) {
    if (!empty($tag->text)) {
      if (empty($count)) {
        $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a>";
        $count++;
      } else {
        $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a>";
      }
    }
  }
}
?>

<?php if ($this->tagstring): ?>
  <h3><?php echo $this->translate('%1$s\'s Tags', $this->htmlLink($this->sitestore->getParent(), $this->sitestore->getParent()->getTitle())) ?></h3>
  <ul class="sitestore_sidebar_list">
    <li> 
      <?php echo $this->tagstring; ?>
    </li>	
  </ul>
<?php endif; ?>
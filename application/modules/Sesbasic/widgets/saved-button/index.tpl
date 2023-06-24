<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script>
  function sesSavedUnsaved(id, type) {

  if ($(type + '_savedunsavedhidden_' + id))
    var saveId = $(type + '_savedunsavedhidden_' + id).value

  en4.core.request.send(new Request.JSON({
    url: en4.core.baseUrl + 'sesbasic/save/index',
    data: {
      format: 'json',
      'subject_id': id,
      'subject_type': type,
      'save_id': saveId
    },
    onSuccess: function(responseJSON) {
      if (responseJSON.save_id) {
        if ($(type + '_savedunsavedhidden_' + id))
          $(type + '_savedunsavedhidden_' + id).value = responseJSON.save_id;
        if ($(type + '_sessaved_' + id))
          $(type + '_sessaved_' + id).style.display = 'none';
        if ($(type + '_sesunsaved_' + id))
          $(type + '_sesunsaved_' + id).style.display = 'inline-block';
      } else {
        if ($(type + '_savedunsavedhidden_' + id))
          $(type + '_savedunsavedhidden_' + id).value = 0;
        if ($(type + '_sessaved_' + id))
          $(type + '_sessaved_' + id).style.display = 'inline-block';
        if ($(type + '_sesunsaved_' + id))
          $(type + '_sesunsaved_' + id).style.display = 'none';
      }
    }
  }));
}
</script>
<?php if (!empty($this->viewer_id)): ?>
  <div id="<?php echo $this->subject_type ?>_sessaved_<?php echo $this->subject_id; ?>" style ='display:<?php echo $this->isSave ? "none" : "block" ?>'>
    <a href="javascript:void(0);" onclick = "sesSavedUnsaved('<?php echo $this->subject_id; ?>', '<?php echo $this->subject_type ?>');">
      <span><?php echo $this->translate("Saved") ?></span>
    </a>
  </div>
  <div id="<?php echo $this->subject_type ?>_sesunsaved_<?php echo $this->subject_id; ?>" style ='display:<?php echo $this->isSave ? "block" : "none" ?>' >
    <a href="javascript:void(0);" onclick = "sesSavedUnsaved('<?php echo $this->subject_id; ?>', '<?php echo $this->subject_type ?>');">
      <span><?php echo $this->translate("Unsaved") ?></span>
    </a>
  </div>
  <input type ="hidden" id="<?php echo $this->subject_type ?>_savedunsavedhidden_<?php echo $this->subject_id; ?>" value = '<?php echo $this->isSave ? $this->isSave : 0; ?>' />
<?php endif; ?>
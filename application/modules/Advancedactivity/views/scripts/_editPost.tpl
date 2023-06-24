<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _aafcomposer.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
      en4.advancedactivity.bindEditFeed(<?php echo $this->action->getIdentity() ?>, {
        lang: {
          'Post Something...': '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
        },
        allowEmptyWithoutAttachment: <?php echo !empty($this->action->attachment_count) ? 1 : 0 ?>
      });
    });
</script>
<?php foreach ($this->composePartials as $partial): ?>
    <?php echo $this->partial($partial[0], $partial[1], array("isAFFWIDGET" => 1,
      'forEdit' => $this->action->getIdentity(),'action' => $this->action)) ?>
<?php endforeach; ?>

<span class="feed_item_body_edit_content <?php echo ( empty($this->action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>" style="display:none;">
    <?php echo $this->content; ?>
<?php echo $this->form->setAttrib('class', 'global_form_edit_post')->render($this) ?>
</span>
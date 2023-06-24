<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  
$action = $this->action;
$canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->editForm) );
?>
<?php 
  include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_activitySettings.tpl';
?>
<div id='edit-activity-item-<?php echo $action->getIdentity() ?>' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper" style="<?php echo $this->translate(($action->commentable) ? 'display:block' : 'display:block;') ?>">

  <?php if ($action->getTypeInfo()->commentable): // Comments - likes  ?>
  
    <div class="" id="showhide-comments-<?php echo $this->comment_id ?>" style="display:block">
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <a href="javascript:void(0);" data-role="button" data-corners="true" data-shadow="true" class="ui-btn-left ui-link ui-btn ui-btn-a ui-shadow ui-corner-all" onclick=" $('.ui-page-active').removeClass('dnone'); $('#editpopup_'+ <?php echo $this->comment_id;?>).remove();$('#comment-option-'+ <?php echo $this->comment_id;?>).css('display', 'none');$('#comment_information-'+ <?php echo $this->comment_id;?>).css('display', 'block');$(window).scrollTop(parentScrollTop)" role="button" style="margin-top:5px;"><?php echo $this->translate('Cancel'); ?></a>
        <h2 class="ui-title" id="count-feedreplies"><?php echo $this->translate('Edit'); ?></h2>
        <a href="javascript:void(0);" data-role="button" data-corners="true" data-shadow="true" class="ui-btn-right ui-link ui-btn ui-btn-a ui-shadow ui-corner-all done" onclick="sm4.activity.attachEdit($('#activity-edit-form-<?php echo $this->comment_id ?>'), '<?php echo $this->comment_id ?>', '<?php echo $this->action->action_id ?>', '<?php echo $this->perform ?>');"><?php echo $this->translate(array('Update', 'Updates', 1),  $this->locale()->toNumber(1)) ?></a>
      </div>
      </div>
    <?php
    if ($canComment) :
      $this->editForm->setActionIdentity($this->comment_id);
      ?>
      <div  class="sm-comments-post-comment-form"  id="hide-commentform-<?php echo $this->comment_id ?>" style="top:42px;">
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="sm-cmf-left">
                <?php echo $this->editForm->render(); ?>
            </td>
          </tr>
        </table>		
        <?php if (!empty($this->comment->attachment_type) && null !== ($attachment = $this->item($this->comment->attachment_type, $this->comment->attachment_id))): ?>
        <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
          <div class="seaocore_comments_attachment_photo">
            <?php if (null !== $attachment->getPhotoUrl()): ?>
              <?php echo $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle()); ?>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if($taggingEnabled):?>  
        <div class="cont-sep t_l b_medium"></div>
            <div id="activitypost-container-temp" action-id='<?php echo "activity-edit-form-$this->comment_id" ;?>'>
            	<div class="compose_buttons">
                
                <?php if($taggingEnabled):?>
                <a id="edit-add-people" href="javascript:void(0);" data-role="none" onclick="sm4.activity.composer.showEditPluginForm(this, 'addpeople', '#activity-edit-body-<?php echo $action->action_id ?>', '<?php echo $this->comment_id ?>');">
                  <i class="cm-icons cm-icon-user"></i>
                </a>
                <?php endif; ?>
              </div>
              <div id="edit_options_box">
                
                <?php if($taggingEnabled):?>
                  <div id="adv_edit_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                      <div id="aff_edit_mobile_aft_search-element">
                        <div class="sm-post-search-fields">
                          <table width="100%">
                            <tr>
                              <td class="sm-post-search-fields-left">
                                <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_edit_mobile_aft_search" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                              </td>
                            </tr>
                          </table>			
                          <span role="status" aria-live="polite"></span>
                        </div>
                        <div id="toEditValues-temp-wrapper" style="border:none;display:none;">
                          <div id="toEditValues-temp-element">
                            <input type="hidden" id="toEditValues-temp" value=""  name="toValues-temp" />
                            <input type="hidden" id="toEditValues" value=""  name="toValues" />
                          </div>
                        </div> 
  
                      </div>
                  </div>
                  
                  <div id="toEditValuesdone-wrapper" style="display:none;"></div>
                  <div class="ui-header o_hidden" id="ui-post-edit-header-addpeople" style="display:none;">
                      <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_edit_mobile_aft_search').val('');
                              sm4.activity.composer.addpeople.addEditFriends('#activity-edit-form-<?php echo $action->action_id ?>');
                              return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                      <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
                      <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_edit_mobile_aft_search').val(''); sm4.activity.toggleEditPostArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
                      <?php else: ?>
                       <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_edit_mobile_aft_search').val(''); sm4.activity.toggleEditPostArea(this, false, 'addpeople');" ></a>
                       <?php endif; ?>
                  </div>
                <?php endif; ?>
               </div>
            </div> 
            <?php endif; ?>
       	
        <div style="display:none;"> 
          <script type="text/javascript">
              sm4.core.runonce.add(function(){
                $('#activity-edit-body-<?php echo $this->comment_id ?>').autoGrow();          
                $('.sm-comments-post-comment-<?php echo $this->comment_id ?>').on('click',function(){
                sm4.activity.toggleEditArea(this, '<?php echo $this->comment_id ?>');
              });
              sm4.activity.toggleEditArea($('.sm-comments-post-comment-<?php echo $this->comment_id ?>'), '<?php echo $this->comment_id ?>');   
              
                $('#activity-edit-body-'+<?php echo $this->comment_id ?>).val('<?php echo $this->body_response;?>');
                $('#activitypost-container-temp').find('#activity-edit-body-'+"<?php echo $this->comment->body?>").focus();
              });                   
          </script>
        </div>
      </div>
      <div class="sm-comments-post-comment sm-comments-post-comment-<?php echo $this->comment_id ?>" >
        <div>
          <input type="text" data-role="none" class="ui-input-field" />
        </div> 
      </div>
  <?php endif;?>
  
  <?php endif; ?>
</div> <!-- End of Comment Likes -->
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content-verify-member-list.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<a id="siteverify_members_anchor" style="position:absolute;"></a>
<div class="seaocore_members_popup" id="verify_members_popup">
  <div class='siteverify_users_block_links'>
    <div class="top">
      <h3>
        <?php echo $this->translate("%s has been verified by:", $this->resource_title);
        ?>
      </h3>
    </div>
      
<?php
      if (COUNT($this->paginator)) :
        foreach ($this->paginator as $item):
          $user = Engine_Api::_()->getItem('user', $item->poster_id);
          ?>
          <div class="cont-sep t_l b_medium"></div>
          <div class="clr o_hidden">
            <div class="sm_profile_item_photo">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array());
              ?>
            </div>
            <div class="sm_profile_item_info">
              <div class="sm_profile_item_title">
                <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array()); ?>
              </div>
              <span class='span_comment' style="margin-left:10px;"> <?php echo $item->comments; ?> </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php
else:
  echo $this->translate('No results were found.');
endif;
?>
     <?php if ($this->paginator->count() > 0):
    echo $this->paginationControl($this->users, null, null, array(
            'pageAsQuery' => true,
           'query' => $this->formValues,
               //'params' => $this->formValues,
       ));
   ?>
  <?php endif; ?>
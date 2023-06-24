<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
<?php if($this->autoContentLoad == 1) : ?>

<?php if(!empty ($this->totalUsers)): ?>
<div class="ui-member-list-head">
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers), $this->locale()->toNumber($this->totalUsers)) ?>
</div>
<?php endif; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<div class="sm-content-list">
  <ul id="browsemembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
  <?php endif;?>
  
    <?php foreach ($this->users as $user): ?>
      <?php
      $table = Engine_Api::_()->getDbtable('block', 'user');
      $select = $table->select()
              ->where('user_id = ?', $user->getIdentity())
              ->where('blocked_user_id = ?', $viewer->getIdentity())
              ->limit(1);
      $row = $table->fetchRow($select);
      ?>
      <li>
        <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
          <div class="ui-item-member-action">
            <?php echo $this->userFriendshipSM($user) ?>
          </div>
        <?php endif; ?>
        <a href="<?php echo $user->getHref() ?>">
          <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
          <div class="ui-list-content">
            <h3><?php echo $user->getTitle() ?></h3>
            <p><?php echo $this->userMutualFriendship($user) ?></p>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  <?php if($this->autoContentLoad == 1):?> 
  </ul>
  <?php if ($this->users): ?>
    <div class='browsemembers_viewmore' id="browsemembers_viewmore" <?php if(Engine_Api::_()->sitemobile()->isApp()) :?> style='display:none;' <?php endif;?>>
      <?php if(Engine_Api::_()->sitemobile()->isApp()) { ?>
      <div class="feeds_loading" id="feed_loading-sitefeed" >
        <i class="icon_loading"></i>
      </div>
      <?php } else {?>
      <?php
       echo $this->paginationControl($this->users, null, null, array(
            'pageAsQuery' => true,
           'query' => $this->formValues,
               //'params' => $this->formValues,
       ));
      }
      ?>
    </div>
<?php endif; ?>
</div>	
<?php endif;?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
          sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : sm4.core.baseUrl + 'members', 'activeRequest' : false, 'container' : 'browsemembers_ul' };  
          });
   <?php } ?>    
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10167 2014-04-15 19:18:29Z lucas $
 * @author     John
 */
 
$user_level = Engine_Api::_()->user()->getViewer()->level_id;
?>

<div class="generic_list_wrapper">
    <ul class="generic_list_widget">
        
      <?php foreach( $this->paginator as $user ): 
      
   
        $inviterTable = Engine_Api::_()->getDbTable('invites', 'inviter');
    	$inviterTableName = $inviterTable->info('name');
    	
    	$userTable = Engine_Api::_()->getItemTable('user');
    	$userTableName = $userTable->info('name');
    	
    	$inviter = Engine_Api::_()->user()->getInviter($user->user_id);
    
      ?>
        <li>
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('class' => 'newestmembers_thumb')) ?>
          <div class='newestmembers_info'>
            <div class='newestmembers_name'>
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
            </div>
            <div class='newestmembers_date'>
              <?php echo $this->timestamp($user->creation_date); ?>
            </div>
            <?php
            if($user_level == 1):
            ?>
            <div style="font-weight:normal;">
                <?php 
                    if($inviter['user_id'] !=""):
                        echo $inviter['displayname']." (".$inviter['user_id'].")"; 
                    endif;
                ?>
            </div>
           <?php endif; ?> 
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    
    <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
      <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'browse'), 'user_general', true),
        'param' => array('orderby' => 'creation_date')
        )); ?>
    <?php endif; ?>
</div>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: pulldown.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if ($this->notifications->getTotalItemCount() > 0): ?>
    <?php foreach ($this->notifications as $notification): ?>
        <?php    
        /* Check for display notifications of only enabled sitemobile modules, If Module is Suggestion then display only 
          suggestions of enabled modules.
         */
        $notification_object_type = explode("_", $notification->object_type);

        if (!in_array($notification_object_type[0], $this->enabledModuleNames)) {
            continue;
        } elseif ($notification_object_type[0] == 'suggestion') {
           
            $suggObj = Engine_Api::_()->getItem('suggestion', $notification->object_id);
            $suggestionModule = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity); 
            foreach($suggestionModule as $sugModName){ 
            $sugModNameEnabled = $sugModName['pluginName'];
            break;
            }
            if(!in_array($sugModNameEnabled, $this->enabledModuleNames))
                continue;
        }/* End Checks */
        ?>
        <li<?php if (!$notification->read): ?> class="sm-ui-lists-highlighted"<?php endif; ?> value="<?php echo $notification->getIdentity(); ?>">
            <div class="ui-btn">
                <?php if ($notification->subject_type == 'user'): ?>
                    <?php $item = Engine_Api::_()->getItem('user', $notification->subject_id); ?>
                    <?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon') ?>
                <?php elseif ($notification->subject_type == 'sitepage_page' || $notification->subject_type == 'sitebusiness_business' || $notification->subject_type == 'sitegroup_group'): ?>
                    <?php if ($notification->subject_type == 'sitepage_page'): ?>
                        <?php $item = Engine_Api::_()->getItem('sitepage_page', $notification->subject_id); ?> 
                    <?php elseif ($notification->subject_type == 'sitebusiness_business'): ?>
                        <?php $item = Engine_Api::_()->getItem('sitebusiness_business', $notification->subject_id); ?> 
                    <?php elseif ($notification->subject_type == 'sitegroup_group'): ?>
                        <?php $item = Engine_Api::_()->getItem('sitegroup_group', $notification->subject_id); ?> 
                    <?php endif; ?>
                    <?php echo $this->itemPhoto($item, 'thumb.icon') ?>
                <?php endif; ?>
                <h3><?php echo $notification->__toString() ?></h3>
                <p class="sm-ui-notification-date">
                    <i class="sm-ui-notification-icon notification_type_<?php echo $notification->type ?>"></i>
                    <?php echo $this->timestamp($notification->date) ?>
                </p>
            </div>
        </li>
        <?php
    endforeach;
    ?>
<?php else : ?>
    <li>
        <div class="tip">
            <span><?php echo $this->translate("You have no notifications.") ?></span>
        </div>	
    </li>
<?php endif; ?>
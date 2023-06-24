<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pulldown-reuqest.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php  if ($this->requests->getTotalItemCount() > 0): ?>
    <?php foreach ($this->requests as $notification): ?>
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
        
        try {
            $parts = explode('.', $notification->getTypeInfo()->handler);
            echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
        } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
                echo $e->__toString();
            }
            continue;
        }
        ?>
    <?php endforeach; ?>
<?php else: ?>
    <li>
        <div class="tip">
            <span><?php echo $this->translate("You have no requests.") ?></span>
        </div>	
    </li>
<?php endif; ?>
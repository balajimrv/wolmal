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
<h3>
  <?php echo $this->translate('Manage Extensions for Stores / Marketplace Plugin') ?>
</h3>
<p>
	<?php echo $this->translate('Below you can choose to add / remove the admin links for Stores / Marketplace extensions from the "Plugins" dropdown list of admin panel of the site. All the extensions of Stores / Marketplace Plugin available on your site are listed below and you can choose whether or not you want that extension\'s link to be displayed in "Plugins" dropdown list. The links provided below will also lead you to the admin panel of the respective extensions and allow you to configure settings for them. Thus, after removing a link from the "Plugins" dropdown, you can visit its extension\'s admin panel from this section.'); ?>
</p><br/>

<div class='sociealengineaddons_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<?php
	if( count($this->channel) ):
?>
  <table class='admin_table'>
    <thead>
      <tr>
         <th align="left">
        	<?php echo $this->translate("Admin Links of Stores / Marketplace Extensions"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Add/Remove from Plugins dropdown"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->channel as $item):?>
        <?php $result_menuitem = Engine_Api::_()->sitestore()->getModulelabel($item);?>
        <?php $isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting("$item.isActivate", 0);?>
        <tr>
          <?php if($item != 'sitestorediscussion' && $item != 'sitestoreurl'):?>
            <?php if($item != 'sitestoreadmincontact'):?>
            <?php if(($item == 'sitestoreinvite') && !empty($isActive)):?>
								<td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'global','action' => 'global'), $result_menuitem->label);?></td>
						<?php elseif($item == 'sitestoreinvite'):?>
								<td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'global','action' => 'readme'), $result_menuitem->label);?></td>
						<?php endif;?>
						<?php if(($item != 'sitestoreinvite') && !empty($isActive)):?>
							<td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'settings'), $result_menuitem->label);?></td>
						<?php elseif($item != 'sitestoreinvite'):?>
							<td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'settings','action' => 'readme'), $result_menuitem->label);?></td>
						<?php endif;?>
           <?php else:?>
              <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'mails'), $result_menuitem->label);?></td>
           <?php endif;?>  
          <?php endif;?>
          <?php if($item == 'sitestoreurl'):?>
             <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => $item, 'controller' => 'settings'), $result_menuitem->label);?></td>
          <?php endif;?>
          <?php if($item != 'sitestorediscussion'):?>
						<?php if(!empty($result_menuitem->enabled)):?>
						<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default','module' => 'sitestore','controller' => 'extension','action' => 'deletemodule', 'modulename' => $item), $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/sitestore_approved1.gif', '', array('title'=> $this->translate('Remove from Plugins dropdown')))) ?> 
						</td> 
						<?php else:?>
							<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default','module' => 'sitestore','controller' => 'extension','action' => 'deletemodule', 'modulename' => $item), $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/sitestore_approved0.gif', '', array('title'=> $this->translate('Add to the Plugins dropdown')))) ?> 
						</td> 
						<?php endif;?>
          <?php endif;?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There is no anyone extension available on this site.'); ?>
    </span>
  </div>
<?php endif; ?>
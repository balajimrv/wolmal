<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'profilemaps','action'=>'manage'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'profilemaps','action'=>'manage'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<div class='clear'>
	<div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category to Store Profile Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate('This mapping will associate a Store Profile Type with a Category. After such a mapping for a category, store admins of stores belonging to that category will be able to fill profile information fields for that profile type. With this mapping, you will also be able to associate a profile type with multiple categories.<br />For information on store profile types, profile fields and to create new profile types or profile fields, please visit the "Profile Fields" section.<br />An example use case of this feature would be associating category books with profile type having profile fields related to books and so on.<br />(Note: Availability of Profile fields to stores also depends on their package; if packages are disabled, then it depends on the member level settings for the store owner.)') ?>
        </p>
        <?php if(count($this->paginator)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th align="left"><?php echo $this->translate("Category Name") ?></th>
								<th align="left"><?php echo $this->translate("Associated Profile") ?></th>
								<th align="left"><?php echo $this->translate("Mapping") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->paginator as $category): ?>
								<tr>
									<td><?php echo $this->translate($category->category_name) ?></td>
									<?php if(!empty($category->label)):?>
										<td><?php echo $this->translate($category->label) ?></td>
									<?php else: ?>
										<td>---</td>
									<?php endif; ?>
									<td width="150">
										<?php if(empty($category->profilemap_id)):?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category->category_id), $this->translate('Add'), array(
												'class' => 'smoothbox',
											)) ?>
										<?php else: ?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'profilemaps', 'action' => 'delete', 'profilemap_id' =>$category->profilemap_id), $this->translate('Remove'), array(
												'class' => 'smoothbox',
											)) ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else:?>
					<br/>
					<div class="tip">
						<span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
					</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>

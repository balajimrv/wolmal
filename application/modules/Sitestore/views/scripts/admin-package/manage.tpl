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
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'package','action'=>'index'), $this->translate('Manage Packages'), array())
    ?>
    </li>
    <li class="active">
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'package','action'=>'manage'), $this->translate('Packages - Plans Mapping'), array())
    ?>
    </li>			
  </ul>
</div>

<div class='clear'>
	<div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Packages - Plans Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate('') ?>
        </p>
        <?php if(count($this->plans)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th align="left"><?php echo $this->translate("Subscription Plans") ?></th>
								<th align="left"><?php echo $this->translate("Associated Package") ?></th>
								<th align="left"><?php echo $this->translate("Mapping") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->plans as $plan): ?>
								<tr>
									<td><?php echo $this->translate($plan->title) ?></td>
									<?php if(!empty($plan->store_package_title)):?>
										<td><?php echo $this->translate($plan->store_package_title) ?></td>
									<?php else: ?>
										<td>---</td>
									<?php endif; ?>
									<td width="150">
										<?php if(empty($plan->planmap_id)):?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'package', 'action' => 'map', 'plan_id' => $plan->package_id), $this->translate('Add'), array('class' => 'smoothbox',)) ?>
										<?php else: ?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'package', 'action' => 'delete', 'planmap_id' => $plan->planmap_id), $this->translate('Remove'), array('class' => 'smoothbox',)) ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else:?>
					<br/>
					<div class="tip">
						<span><?php echo $this->translate("There are currently no subscription to be mapped.") ?></span>
					</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>

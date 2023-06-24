<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestorereview/views/scripts/_navigationAdmin.tpl'; ?>

<div class='seaocore_settings_form'>
	<div class='settings'>
		<form class="global_form">
			<div>
				<h3><?php echo $this->translate("Category Based Rating Parameters") ?> </h3>
				<p class="form-description">
					<?php echo $this->translate('Below, you can configure rating parameters for the various Store categories. By clicking on "Add", "Edit" and "Delete" respectively, you can add multiple new parameters, or edit and delete existing rating parameters. Hence, when a user would go to rate and review a Store belonging to a category, he will be able to rate the Store on the parameters configured by you for that category.<br /> This extremely useful feature enables gathering of refined ratings, reviews and feedback for the Stores in your community.') ?>
				</p>
				<?php if(count($this->paginator)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th align="left" width="40%"><?php echo $this->translate("Category Name") ?></th>
								<th align="left" width="40%"><?php echo $this->translate("Review Parameters") ?></th>
								<th align="left" width="20%"><?php echo $this->translate("Options") ?></th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($this->reviewcat_cat_array as $key => $reviewcat): ?>
								<tr>
									<td width="40%"><?php echo $this->translate($reviewcat[0]);?></td>
									<td width="40%">
										<ul class="admin-review-cat">
											<?php $reviewcat_exist = 0;?>
											<?php foreach ($reviewcat as $reviewcat_key => $reviewcat_name): ?>
												<?php if($reviewcat_key != 0):?>
													<?php $reviewcat_exist = 1;?>
													<li><?php echo $this->translate($reviewcat_name); ?></li>
												<?php endif;?>
											<?php endforeach; ?>
										</ul>
										<?php if($reviewcat_exist == 0):?>
											---
										<?php endif;?>
									</td>

									<td width="20%">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorereview', 'controller' => 'ratingparameter', 'action' => 'create', 'category_id' => $key), $this->translate('Add'), array(
											'class' => 'smoothbox',
										)) ?> 

										<?php if($reviewcat_exist == 1):?>	
											| <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorereview', 'controller' => 'ratingparameter', 'action' => 'edit', 'category_id' => $key), $this->translate('Edit'), array(
												'class' => 'smoothbox',
											)) ?>

											| <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorereview', 'controller' => 'ratingparameter', 'action' => 'delete', 'category_id' => $key), $this->translate('Delete'), array(
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
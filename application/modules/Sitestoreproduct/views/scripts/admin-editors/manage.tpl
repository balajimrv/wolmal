<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
  
  var previewFileForceOpen;
  var previewFile = function(event)
  {
    event = new Event(event);
    element = $(event.target).getParent('.admin_file').getElement('.admin_file_preview');

    // Ignore ones with no preview
    if( !element || element.getChildren().length < 1 ) {
      return;
    }

    if( event.type == 'click' ) {
      if( previewFileForceOpen ) {
        previewFileForceOpen.setStyle('display', 'none');
        previewFileForceOpen = false;
      } else {
        previewFileForceOpen = element;
        previewFileForceOpen.setStyle('display', 'block');
      }
    }
    if( previewFileForceOpen ) {
      return;
    }

    var targetState = ( event.type == 'mouseover' ? true : false );
    element.setStyle('display', (targetState ? 'block' : 'none'));
  }

  window.addEvent('load', function() {
    $$('.slideshow-image-preview').addEvents({
      click : previewFile,
      mouseout : previewFile,
      mouseover : previewFile
    });
    $$('.admin_file_preview').addEvents({
      click : previewFile
    });
  });
  
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to remove selected Editors?")) ?>');
	}

	function selectAll()
	{
	  var i;
	  var multidelete_form = $('multidelete_form');
	  var inputs = multidelete_form.elements;
	  for (i = 1; i < inputs.length - 1; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = inputs[0].checked;
    	}
  	}
	}
</script>
  
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-review/_navigationAdmin.tpl'; ?>

<div class='clear seaocore_settings_form'>
	<div class='settings'>
		<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()">
      <div>
        <h3><?php echo $this->translate("Manage Editors") ?> </h3>

          <p class="form-description">
            <?php echo $this->translate("Editor reviews are helpful in displaying accurate, trusted and unbiased reviews that will showcase products' quality, features, and value. This will bring more user engagement to your site, as editor reviews provide reviews from expert people (editors) on the products of their interest.<br />
Below, you can add new editor by using 'Add New Editor' link. You can also edit and remove editors added by you by clicking on the links for each. You can also make an editor as Super Editor, who will be assigned all the reviews if other editors delete their user accounts from your site.
<br /><br />
<b>Badge:</b> You can assign badge to the editors. You can add / manage editor badges by clicking on 'Manage Editor Badges' link.
<br /><br />
<b>Note:</b> You can not remove Super Editor from editor of your site. To do so, you have to first make some other editor as Super Editor. You can disable editor reviews by using 'Allow Reviews' field from the 'Products Global Settings' section of this plugin.") ?>
          </p>            

        
				<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'editors', 'action' => 'create'), $this->translate('Add New Editor'), array('class' => 'buttonlink seaocore_icon_add')) ?> <t/><t/>
        
				<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'badge', 'action' => 'manage'), $this->translate('Manage Editor Badges'), array('class' => 'buttonlink', 'style'=>'background-image: url('.$this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/badge.png);')) ?> <br /><br />        

        <?php if(Count($this->paginator) > 0):?>

					<table class='admin_table' width="100%">
						<thead>
							<tr>
<!--								<th style='width: 1%;' class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>-->
								<th width="1%" align="left"><?php echo $this->translate("Editor Photo") ?></th>

								<?php $class = ( $this->order == 'engine4_users.username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
								<th width="1%" align="left" class="<?php echo $class ?>"><?php echo $this->translate("User Name") ?></th>
                
                <?php $class = ( $this->order == 'engine4_sitestoreproduct_editors.designation' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
								<th width="1%" align="left" class="<?php echo $class ?>"><?php echo $this->translate("Designation") ?></th>  
                
                <th width="1%" align="left"><?php echo $this->translate("About Editor") ?></th>
                
                <th width="1%" align="center"><?php echo $this->translate("Reviews") ?></th>
                
                <th width="%" align="left"><?php echo $this->translate("Badge") ?></th>
                
                <th width="1%" align="left"><?php echo $this->translate("Super Editor") ?></th>

								<th width="1%" align="left"><?php echo $this->translate("Options") ?></th>
							</tr>
						</thead>					
						<tbody>
							<?php foreach ($this->paginator as $editor): $i = 0; $i++; $id = 'admin_file_' . $i; ?>
								<tr>
                  
									<td class='admin_table_user'><?php echo $this->htmlLink(array('route' => 'sitestoreproduct_review_editor_profile', 'username' => $editor->username, 'user_id' => $editor->user_id), $this->itemPhoto($editor, 'thumb.icon'), array('target' => '_blank', 'title' => $editor->getTitle())) ?></td>					
                  
									<td class='admin_table_user'><?php echo $this->htmlLink(array('route' => 'sitestoreproduct_review_editor_profile', 'username' => $editor->username, 'user_id' => $editor->user_id), $editor->getTitle(), array('target' => '_blank')) ?></td>	
                  
                  <?php if($editor->designation): ?>
                    <td class='admin_table_user'><?php echo $editor->designation; ?></td>
                  <?php else: ?>
                    <td class='admin_table_user'>---</td>
                  <?php endif; ?>
                    
                  <?php if($editor->details): ?>
                    <td class='admin_table_user'><span title="<?php echo $editor->details; ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($editor->details, 90); ?></span></td>
                  <?php else: ?>
                    <td class='admin_table_user'>---</td>
                  <?php endif; ?>  
                  <?php 
										$params = array();
										$params['owner_id'] = $editor->user_id;
										$params['type'] = 'editor';
                  ?>  
                  <td class='admin_table_centered'><?php echo Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->totalReviews($params); ?></td>  
                    
                  <td>
                    <?php if(!empty($editor->badge_id)): ?>
                      <?php $badge = Engine_Api::_()->getItem('sitestoreproduct_badge', $editor->badge_id); ?>
                      <?php if(isset($badge->badge_main_id) && !empty($badge->badge_main_id)): ?>
                        <?php $main_path = Engine_Api::_()->storage()->get($badge->badge_main_id, '')->getPhotoUrl();?>
                        <?php if(!empty($main_path)): ?>
                          <div class="admin_file admin_file_type_image" id="<?php echo $id ?>">
                            <div class="slideshow-image-preview">
                              
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'badge', 'action' => 'assign-badge', 'editor_id'=> $editor->editor_id), '<img src="'. $main_path .'" class="photo" width="50" />', array('class' => 'smoothbox', 'title' => 'Click to change or remove badge')) ?>
                              
                            </div>
                          </div>
                        <?php endif; ?>
                      <?php endif; ?>    
                    <?php else: ?>
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'badge', 'action' => 'assign-badge', 'editor_id'=> $editor->editor_id), $this->translate('Assign'), array('class' => 'smoothbox', 'title' => 'Click to assign badge')) ?>
                    <?php endif; ?>
                  </td>          
                                    
                  <?php if($editor->super_editor == 1):?>
                    <td align="center" class="admin_table_centered"> 
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif'); ?></td>
                  <?php else: ?>
                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'editors', 'action' => 'super-editor', 'editor_id' => $editor->editor_id, 'super_editor' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Super Editor'))), array('class' => 'smoothbox')) ?></td>
                  <?php endif; ?>
                  
									<td align="left">
                    
                    <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_review_editor_profile', 'username' => $editor->username, 'user_id' => $editor->user_id), $this->translate("Profile"), array('target' => '_blank')) ?> | 

                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'editors', 'action' => 'edit', 'editor_id'=> $editor->editor_id), $this->translate('Edit'), array('class' => 'smoothbox')) ?> | 
                    
                  <?php if($editor->super_editor == 1):?>  
                    <span><?php echo $this->translate('Remove'); ?></span>
                  <?php else: ?>                
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'editors', 'action' => 'delete', 'editor_id'=> $editor->editor_id), $this->translate('Remove'), array('class' => 'smoothbox',)) ?>
                  <?php endif; ?>                    

									</td>
								</tr>
							<?php  endforeach; ?>							
						</tbody>
					</table>

					<br />

				<?php else:?>
					<div class="tip">
						<span><?php echo $this->translate("There are currently no editor has been added by site admin.") ?></span>
					</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>
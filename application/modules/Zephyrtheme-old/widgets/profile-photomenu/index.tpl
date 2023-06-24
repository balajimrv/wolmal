<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */
 
// Photo
if(empty($this->mpmShowphoto)) { $this->mpmShowphoto = 1; } else { $this->mpmShowphoto = $this->mpmShowphoto; }
// Profile Options Menu
if(empty($this->mpmShowmenu)) { $this->mpmShowmenu = 1; } else { $this->mpmShowmenu = $this->mpmShowmenu; }
?>

<?php if($this->mpmShowphoto == 1 || $this->mpmShowmenu == 1): ?>
<div id="memberphotomenu_container">
  <?php if($this->mpmShowphoto == 1): ?>
  <div id="memberphotomenu_photo">
    <div>
      <span class="memberphotomenu_photo_linkimg">
        <?php echo $this->itemPhoto($this->subject()); ?>
      </span>
      
      <?php if( $this->subject()->isSelf($this->viewer()) ): ?>
      <a href="<?php echo $this->url(array('module'=>'members','controller' => 'edit','action'=>'photo'), 'default', true) ;?>" class="memberphotomenu_photo_edit">
        <span class="memberphotomenu_photo_edit_icon"></span>
        <span class="memberphotomenu_photo_edit_text"><?php echo $this->translate('Edit My Photo'); ?></span>
      </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
  
  <?php if(Engine_Api::_()->core()->hasSubject() || $viewer->getIdentity()): ?>
  <?php if($this->mpmShowmenu == 1): ?>
  <div id="memberphotomenu_menu" class="quicklinks">
    <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->setPartial(array('_navIcons.tpl', 'core'))
        ->render()
    ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php endif; ?>


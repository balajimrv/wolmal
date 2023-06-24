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
?>


<div id="memberprofile_cover_container">
  <div id="memberprofile_cover" style="background-image: url('<?php echo $this->current_cover; ?>');">
    <div id="memberprofile_cover_details">
      <div id="memberprofile_cover_img">
        <span class="memberprofile_cover_linkimg">
          <?php echo $this->itemPhoto($this->subject()); ?>
        </span>
        
        <?php if( $this->subject()->isSelf($this->viewer()) ): ?>
        <a href="<?php echo $this->url(array('module'=>'members','controller' => 'edit','action'=>'photo'), 'default', true) ;?>" class="memberprofile_cover_edit" title="<?php echo $this->translate('Edit My Photo'); ?>">
          <span class="memberprofile_cover_edit_icon"></span>
        </a>
        <?php endif; ?>
      </div>
      
      <div id='memberprofile_cover_status'>
        <h2><?php echo $this->subject()->getTitle() ?></h2>
        
        <?php if( $this->auth ) { ?>
          <span class="profile_status_text" id="user_profile_status_container">
            <?php echo $this->viewMore($this->subject()->status); ?>
            <?php if( !empty($this->subject()->status) && $this->subject()->isSelf($this->viewer())) { ?>
              <a class="profile_status_clear" href="javascript:void(0);" onclick="en4.user.clearStatus();">(<?php echo $this->translate('clear'); ?>)</a>
            <?php } ?>
          </span>
        <?php } ?>
        
        <?php if( !$this->auth ): ?>
          <span><?php echo $this->translate('This profile is private.');?></span>
        <?php endif; ?>
        
      </div>
      
    </div>

    <?php if(Engine_Api::_()->core()->hasSubject() && $this->viewer->getIdentity()): ?>
    <div id="memberprofile_cover_menu">
      <a href="javascript:void(0);" id="memberprofile_cover_menu_toggle" title="<?php echo $this->translate('Profile Options'); ?>"><span class="memberprofile_cover_settings_icon"></span></a>
      <div id="memberprofile_cover_menu_menu" class="quicklinks">
        <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
          echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->setPartial(array('_navIcons.tpl', 'core'))
            ->render()
        ?>
        <?php
          if ($this->subject()->isSelf($this->viewer()))
          {
          	echo '<div class="sep"></div>';
          	echo '<ul>';
            echo '<li>'.$this->htmlLink($this->url(array('module' => 'zephyrtheme',
              'controller' => 'index',
              'action' => 'add-cover'), 'default'),
            $this->translate('Upload Cover'), array('class' => 'smoothbox buttonlink menu_user_profile user_profile_uploadcover', 'id' => 'memberprofile_cover_upload', 'style' => '')).'</li>';
    
            if (Engine_Api::_()->zephyrtheme()->hasProfileCover($this->viewer()))
            {
              echo '<li>'.$this->htmlLink($this->url(array('module' => 'zephyrtheme', 'controller' => 'index', 'action' => 'delete-cover'),
              'default'), $this->translate('Delete Cover'), array('class' => 'smoothbox buttonlink menu_user_profile user_profile_deletecover', 'id' => 'memberprofile_cover_delete', 'style' => '')).'</li>';
            }
            echo '<ul>';
          }
        ?>

      </div>
    </div>
    <?php endif; ?>
  </div>
</div>


<script type='text/javascript'>
Element.Events.clickout = {
  base : 'click',
  condition : function(event) { event.stopPropagation(); return false; },
  onAdd : function(fn) { this.getDocument().addEvent('click', fn); },
  onRemove : function(fn) { this.getDocument().removeEvent('click', fn); }
};

window.addEvent('domready', function() {
  var optionsclicked = 0;
  $('memberprofile_cover_menu_toggle').addEvents({
	click: function(event, element, user_id){
	  if(optionsclicked == 0)
	  {
		$('memberprofile_cover_menu_menu').show();
		$('memberprofile_cover_menu').addClass('active')
		optionsclicked = 1;
	  }
	  else
	  {
		$('memberprofile_cover_menu_menu').hide();
		$('memberprofile_cover_menu').removeClass('active');
		optionsclicked = 0;
	  }
	},
  }); 
  $('memberprofile_cover_menu').addEvents({
	  clickout: function(){
		if(optionsclicked == 1)
		{
		  $('memberprofile_cover_menu_menu').hide();
		  $('memberprofile_cover_menu_menu').removeClass('active');
		  optionsclicked = 0;
		}
	  },
	}); 
});
</script>


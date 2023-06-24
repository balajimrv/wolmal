<?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) { ?> 
<div id="mobile_menu_container" class="mobile_menu_container" style="display: none;">
  <div class="mobile_menu_top">
    <?php if( $this->search_check ) { ?>
      <div class="mobile_search">
        <form id="global_search_form_mobile" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field_mobile' alt='<?php echo $this->translate('Search for...') ?>' value='<?php echo $this->translate('Search for...') ?>' autocomplete="off"  />
        </form>
      </div>
    <?php } ?>
    
	<?php if( $this->viewer->getIdentity() ) { ?>
    <?php if( $this->minimenushow_profileimg == true || $this->minimenushow_logout == true ) { ?>
      <ul class="mobile_minimenu">
        <?php if($this->minimenushow_logout == true) { ?>
        <li class="mobile_minimenu_logout">
          <a href="<?php echo $this->url(array('module'=>'','controller' => '','action'=>'logout'), 'default', true); ?>" title="<?php echo $this->translate("Logout"); ?>">
            <span class="mobile_minimenu_logout_icon mobile_minimenu_icon_type"></span>
            <span><?php echo $this->translate("Logout"); ?></span>
          </a>
        </li>
        <?php } ?> 

        <?php if($this->minimenushow_profileimg == true) { ?>
        <li class="mobile_minimenu_profile_button">
          <?php echo $this->htmlLink($this->viewer()->getHref(), 
                    $this->itemPhoto($this->viewer(), 'thumb.icon'), array('title'=>$this->translate('My Profile'))); ?>
        </li>
        <?php } ?>
      </ul>
    <?php } ?>
    <?php } ?>
  </div>
  
  <?php 
  echo $this->navigation()
	->menu()
	->setContainer($this->navigation)
	->setPartial(null)
	->setUlClass('mobile_menu')
	->render();
  ?>
  
  <a href="javascript:void(0);" class="toggle_closer">&#215;</a>
</div>
<?php } ?>
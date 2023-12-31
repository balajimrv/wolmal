<?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) {
if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('dropmenu')) { 
  echo $this->content()->renderWidget('dropmenu.drop-menu');
} 
else { ?>
<!-- START - MAIN MENU -->
<div class="generic_layout_container layout_core_menu_main">
  <ul id="navigation">
	<?php
	$s = 0; $total = 0; $more = array();
	foreach($this->navigation as $item) { ?>
	<?php
		$label = $item->getLabel();
		$class = $item->getClass();
		
		if($item->get('icon')) { $item_icon = $this->htmlImage($item->get('icon'), array('alt'=>$item->get('icon')), array('style'=>'width: '.$header_iconssize.'px; height: '.$header_iconssize.'px;')); }
		else { $item_icon = ''; }
		
		
		if($s < $this->mainmenu_dropdown) {
			if((strstr(strtolower($label), $module_name) != "") || ($module_name == "core" && $label == "Home") || ($module_name == "user" && $label == "Home" && $action_name == "home") || ($module_name == "user" && $label == "Members" && $action_name == "browse")) { ?>
			<li class="active">
				<?php echo $this->htmlLink($item->getHref(), $item_icon.'<span>'.$this->translate($label).'</span>', array(
					'class' => $class,
					'target' => $item->get('target')
				)) ?>
			</li>
			<?php
				$total += 1;
				$s += 1;
			} else {
			?>
            <?php
			if($this->translate($label) == "LOCKER"){
				$viewer = Engine_Api::_()->user()->getViewer();
				$userid = $viewer->getIdentity(); 
				
				//Get Level ID
				$usertbl = Engine_Api::_()->getDbTable('users', 'user');
				$selectrow = $usertbl->select('level_id')->where('user_id = ?', $userid);
				$query = $usertbl->fetchRow($selectrow);
				$level_id = $query['level_id'];
				
				if($level_id !=4 && $level_id !=5){
				
			?>
			<li>
				<?php echo $this->htmlLink($item->getHref(), $item_icon.'<span>'.$this->translate($label).'</span>', array(
					'class' => $class,
					'target' => $item->get('target')
				)) ?>
			</li>
			<?php
				}
			}else{
				?>
                <li>
				<?php echo $this->htmlLink($item->getHref(), $item_icon.'<span>'.$this->translate($label).'</span>', array(
					'class' => $class,
					'target' => $item->get('target')
				)) ?>
			</li>
                <?php
			}
				$s += 1;
			}
		} else {
			if(strstr(strtolower($label), $module_name) != "") {
				$more[$s] = '<li class="active">'.$this->htmlLink($item->getHref(), $item_icon.'<span>'.$this->translate($label).'</span>', array( 'class' => $class, 'target' => $item->get('target'))).'</li>';
				$s += 1;
			} else {
				$more[$s] = '<li>'.$this->htmlLink($item->getHref(), $item_icon.'<span>'.$this->translate($label).'</span>', array( 'class' => $class, 'target' => $item->get('target'))).'</li>';
				$s += 1;
			}
		}
	?>
	<?php };
	if($s > $this->mainmenu_dropdown) {
	?>
	<li class="submenu_explore">
		<a href="javascript:void(0);" id="submenu_toggle">
			<span><?php echo $this->translate('More'); ?><span class="submenu_explore_plus">+</span></span>
		</a>
		<ul>
			<?php foreach($more as $more_item) { echo $more_item; } ?>
		</ul>
	</li>
	
	<?php } ?>
  </ul>
</div>
<!-- END - Main Menu -->
<?php } 
} ?>
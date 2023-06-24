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

// Settings
if(empty($this->tbbpopular)) { $this->tbbpopular = 1; } else { $this->tbbpopular = $this->tbbpopular; }
if(empty($this->tbbnewest)) { $this->tbbnewest = 1; } else { $this->tbbnewest = $this->tbbnewest; }
if(empty($this->tbbwomen)) { $this->tbbwomen = 1; } else { $this->tbbwomen = $this->tbbwomen; }
if(empty($this->tbbmen)) { $this->tbbmen = 1; } else { $this->tbbmen = $this->tbbmen; }
if(empty($this->tbbbrowse)) { $this->tbbbrowse = 1; } else { $this->tbbbrowse = $this->tbbbrowse; }
if(empty($this->tbbnavposition)) { $this->tbbnavposition = 2; } else { $this->tbbnavposition = $this->tbbnavposition; }

$container_id = "tabbed_members_container_".rand();
?>

<script type="text/javascript">
window.addEvent('domready', function() {
  function mootab(_navtabbed, _bodytabbed) {
	var tabs = $$('#<?php echo $container_id; ?> #tabs-navtabbed > li');
	var containers = $$('#<?php echo $container_id; ?> #tabs-bodytabbed > li');
	tabs.each(function(tab, index) {
	  tab.addEvents({
		click: function(event) {
		  event.stop();
		  tabs.removeClass('active');
		  tabs[index].addClass('active');
		  containers.removeClass('active');
		  containers[index].addClass('active');
		}
	  });
	});
  }
  mootab($('tabs-navtabbed'),$('tabs-bodytabbed'));
});
</script>

<div id="<?php echo $container_id; ?>" class="tabbed_members_container">

<?php
$tabs_nav  = '<ul id="tabs-navtabbed" class="'. ($this->tbbnavposition == 1 ? 'tabs_navtabbed_top' : 'tabs_navtabbed_bottom') .' clearfix">';
  if($this->tbbpopular == 1) {
	$tabs_nav .= '<li class="active">';
    $tabs_nav .= '<a href="javascript:void(0);">'.$this->translate('Popular Members').'</a>';
    $tabs_nav .= '</li>';
} if($this->tbbnewest == 1) {
	$tabs_nav .= '<li '. ($this->tbbpopular == 2 ? 'class="active"' : '') .'>';
	$tabs_nav .= '<a href="javascript:void(0);">'.$this->translate('Newest Members').'</a>';
	$tabs_nav .= '</li>';
} if($this->tbbwomen == 1) {
	$tabs_nav .= '<li '. ($this->tbbpopular == 2 && $this->tbbnewest == 2 ? 'class="active"' : '') .'>';
	$tabs_nav .= '<a href="javascript:void(0);">'.$this->translate('Women').'</a>';
	$tabs_nav .= '</li>';
} if($this->tbbmen == 1) {
	$tabs_nav .= '<li '. ($this->tbbpopular == 2 && $this->tbbnewest == 2 && $this->tbbwomen == 2 ? 'class="active"' : '') .'>';
	$tabs_nav .= '<a href="javascript:void(0);">'.$this->translate('Men').'</a>';
	$tabs_nav .= '</li>';
} if($this->tbbbrowse == 1) {
	$tabs_nav .= '<div class="view_more_links">';
	$tabs_nav .= '<a href="'.$this->url(array('module'=>'user','controller' => 'index','action'=>'browse'), 'default', true).'">'.$this->translate('Browse Members').'</a>';
	$tabs_nav .= '</div>';
}
$tabs_nav .= '</ul>';
?>

<?php if($this->tbbnavposition == 1) { echo $tabs_nav; } ?>

<ul id="tabs-bodytabbed">
  <?php if($this->tbbpopular != 2) { ?>
  <!-- Popular Members -->
  <li class="active">
    <div class="pop_members tabbed_members">
      <?php 
      $i=0;
      $i++;
      if($i==1) echo '<ul>';
      foreach($this->popular_user as $user) {
          echo '<li>';
          echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')). '<div class="newestmembers_info"><div class="newestmembers_name">' . $this->htmlLink($user->getHref(), $user->getTitle()) . '</div></div></li>';
      }
      if($i) echo '</ul>';
      ?>
    </div>
  </li>
  <?php } if($this->tbbnewest != 2) { ?>
  <!-- Newest Members -->
  <li <?php if($this->tbbpopular == 2) { echo 'class="active"'; } ?> >
    <div class="new_members tabbed_members">
      <?php 
      $i=0;
      $i++;
      if($i==1) echo '<ul>';
      foreach($this->new_user as $user) {
          echo '<li>';
          echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')). '<div class="newestmembers_info"><div class="newestmembers_name">' . $this->htmlLink($user->getHref(), $user->getTitle()) . '</div></div></li>';
      }
      if($i) echo '</ul>';
      ?>
    </div>
  </li>
  <?php } if($this->tbbwomen != 2) { ?>
  <!-- Women Members -->
  <li <?php if($this->tbbpopular == 2 && $this->tbbnewest == 2) { echo 'class="active"'; } ?> >
    <div class="men_members tabbed_members">
      <?php 
      $i=0;
      $i++;
      if($i==1) echo '<ul>';
      foreach($this->memberWomen as $itemWomenMember) {
          echo '<li>';
          echo $this->htmlLink($itemWomenMember->getHref(), $this->itemPhoto($itemWomenMember, 'thumb.icon')). '<div class="newestmembers_info"><div class="newestmembers_name">' . $this->htmlLink($itemWomenMember->getHref(), $itemWomenMember->getTitle()) . '</div></div></li>';
      }
      if($i) echo '</ul>';
      ?>
    </div>
  </li>
  <?php } if($this->tbbmen != 2) { ?>
  <!-- Men Members -->
  <li <?php if($this->tbbpopular == 2 && $this->tbbnewest == 2 && $this->tbbwomen == 2) { echo 'class="active"'; } ?> >
    <div class="women_members tabbed_members">
      <?php 
      $i=0;
      $i++;
      if($i==1) echo '<ul>';
      foreach($this->memberMen as $itemMenMember) {
          echo '<li>';
          echo $this->htmlLink($itemMenMember->getHref(), $this->itemPhoto($itemMenMember, 'thumb.icon')). '<div class="newestmembers_info"><div class="newestmembers_name">' . $this->htmlLink($itemMenMember->getHref(), $itemMenMember->getTitle()) . '</div></div></li>';
      }
      if($i) echo '</ul>';
      ?>
    </div>
  </li>
  <?php } ?>
</ul>

<?php if($this->tbbnavposition == 2) { echo $tabs_nav; } ?>

</div>
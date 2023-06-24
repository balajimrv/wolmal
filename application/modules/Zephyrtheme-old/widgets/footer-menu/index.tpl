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
 
// Set logo
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->footer_logo;
$route = $this->viewer()->getIdentity() ? array('route'=>'user_general', 'action'=>'home') : array('route'=>'default');

// Select Footer Layout
if($this->viewer->getIdentity()) {
  if($this->footer_logged == 1) {
    include 'footer_2.php'; 
  }
  else {
    include 'footer_1.php'; 
  }
} elseif(!$this->viewer->getIdentity()) {
  if($this->footer_notlogged == 1) {
    include 'footer_2.php'; 
  }
  else {
    include 'footer_1.php'; 
  }
}
?>

<?php if($this->footer_gototop == 1){ ?>
<style type="text/css">
#gototop {
  background: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/images/gototop.png' ?>) no-repeat center center;
  position: fixed;
  bottom: 40px;
  right: 20px !important;
  overflow: hidden;
  width: 64px;
  height: 64px;
  border: none;
  text-indent: -999px;
  display: none;
  text-decoration: none;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
  filter: alpha(opacity=50);
  -moz-opacity: 0.5;
  -khtml-opacity: 0.5;
  opacity: 0.5;
}
html[dir="rtl"] #gototop {
  left: 20px !important;
  right: auto !important;
}
#gototop:hover {
  opacity: 1 !important;
  -moz-opacity: 1 !important;
  -khtml-opacity: 1 !important;
  filter: alpha(opacity=100) !important;
  -ms-filter:"progid:DXImageTransform.Microsoft.Alpha"(Opacity=100) !important;
}
</style>
<script type='text/javascript'>
if (!(Browser.name == 'ie' && Browser.version == 6)) {
	window.addEvent((Browser.name == 'ie' ? 'load' : 'domready'), function(){
		var scrollUp = new Fx.Scroll(window);
		var target_opacity = 0.5;
		new Element('span', {
			'id': 'gototop', 
			'styles': {
				opacity: target_opacity,
				display: 'none',
				position: 'fixed',
				bottom: 40,
				right: 20,
				cursor: 'pointer'
			},
			'text': <?php echo $this->translate("'Go To Top'"); ?>,
			'title': <?php echo $this->translate("'Go To Top'"); ?>,
			'tween': {
				duration: 600,
				onComplete: function(el) { if (el.get('opacity') == 0) el.setStyle('display', 'none')}
			},
			'events': {'click': function() {
				scrollUp.toTop();
			}}
		}).inject(document.body);
		window.addEvent('scroll', function() {
			var visible = window.getScroll().y > (window.getSize().y * 0.3);
			if (visible == arguments.callee.prototype.last_state) return;
			if (Fx && Fx.Tween) {
				if (visible) $('gototop').fade('hide').setStyle('display', 'inline').fade(target_opacity);
				else $('gototop').fade('out');
			} else {
				$('gototop').setStyle('display', (visible ? 'inline' : 'none'));
			}
			arguments.callee.prototype.last_state = visible
		});
	});
}
</script>
<?php } ?>

<script type='text/javascript'>
window.addEvent('domready', function() {
  // Give Social Icons a distance from top of the browser
  var headerHeight = $('global_header').getSize();
  $$('.social_icons').setStyles({'top': headerHeight.y + 18});
});
</script>

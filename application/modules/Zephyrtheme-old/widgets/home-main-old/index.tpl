<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  CPixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

$home_imgposition = ($this->home_imgposition) ? $this->home_imgposition : 0;
$home_usesignup = ($this->home_usesignup) ? $this->home_usesignup : '1';
$home_uselogin = ($this->home_uselogin) ? $this->home_uselogin : 1;
$home_img = ($this->home_img) ? $this->home_img : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_img.png');
$home_video = ($this->home_video) ? $this->home_video : '';
$home_bgimg = ($this->home_bgimg) ? $this->home_bgimg : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_bg.jpg');
$home_h1 = ($this->home_h1) ? $this->home_h1 : 1;
$home_h2 = ($this->home_h2) ? $this->home_h2 : 1;
$home_signup = ($this->home_signup) ? $this->home_signup : 1;
$home_eltop = ($this->home_eltop) ? $this->home_eltop : '10%';
$home_imageheight = ($this->home_imageheight) ? $this->home_imageheight : '520px';

// Head scripts
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.min.js')  
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.backstretch.min.js');  
?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function () {
jQuery(function() { jQuery('.layout_theme_home').backstretch([
   "<?php echo $home_bgimg; ?>" 
], {duration: 3000, fade: 200}); }); });
</script>

<div class="layout_theme_home <?php if($home_imgposition == 1){echo 'img_right';} elseif($home_imgposition == 2){echo 'text_center';} else {echo 'img_left';}  if($home_usesignup == '2') { echo ' has_signupform'; } ?>"><div class="width_main clearfix">
  <?php if($home_img == 0 || $this->home_h1 == 1 || $this->home_h2 == 1 || $this->home_signup == 1): ?>
    <div class="home_headlines">
      <?php if($this->home_h1 == 1): ?>
      <h1 class="home_headline_first"><?php echo $this->translate('Take the power to make it perfect'); ?></h1>
      <?php endif; ?>
      <?php if($this->home_h2 == 1): ?>
      <h2 class="home_headline_second"><?php echo $this->translate('Share your findings, discover new people, find your better half. Everything in the same place!'); ?></h2>
      <?php endif; ?>
      
      <?php if($this->home_signup == 1 || $this->home_uselogin == 1): ?>
      <div class="home_account_buttons">
        <?php if($this->home_signup == 1): ?>
        <div class="home_signup_button">
          <a href="<?php echo ($this->header_signupbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'signup'), 'default', true); ?>" class="button_signup home_signup_button_link button"><?php echo $this->translate("Register for Free"); ?></a>
        </div>
        <?php endif; ?>
        
        <?php if($this->home_uselogin == 1): ?>
        <div class="home_login_button">
          <a href="<?php echo ($this->header_loginbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'login'), 'default', true); ?>" class="button_login"><?php echo $this->translate("Member Login"); ?></a>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      
    </div>
    
    <?php if($home_img == 0 && $home_imgposition != 2): ?>
    <div class="home_img">
    
      <?php if($home_usesignup != '2') { ?>
      
        <?php if(empty($home_video)) { ?>
          <img src="<?php echo $home_img; ?>" alt="" />
        <?php } else { echo $home_video; }
        
      } else { ?>
      
        <div id="connect_signup_box" class="connect_box_form clearfix">
          <?php
          $signupForm = new User_Form_Signup_Account();
          $signupForm->setDescription('');
          foreach ($signupForm->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Text || $element instanceof Zend_Form_Element_Textarea || $element instanceof Zend_Form_Element_Password) {
              $element->setAttrib('autofocus', null);
            }
          }
          echo $signupForm;
          ?>
        </div>
        <!-- START - Social Connections -->
        <?php
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if($settings->getSetting('core_facebook_enable', 'none') != 'none' || $settings->getSetting('core_twitter_enable', 'none') != 'none' || $settings->getSetting('core_janrain_enable', 'none') != 'none')
        {
          echo '<div id="social_login_box_home">';
          $loginForm = new User_Form_Login();
          echo $loginForm->getElement('facebook');
          echo $loginForm->getElement('twitter');
          echo $loginForm->getElement('janrain');	
          echo '</div>';	
        }
        ?>
        <!-- END - Social Connections -->
        
      <?php } ?>
      
    </div>
    <?php endif; ?>
    
  <?php endif; ?> 
</div></div>

<script type="text/javascript">
//<![CDATA[
window.addEvent('load', function() {
  if( $('username') && $('profile_address') ) {
	$('profile_address').innerHTML = $('profile_address').innerHTML.replace('yourname', '<span id="profile_address_text">yourname</span>');
	$('username').addEvent('keyup', function() { var text = 'yourname'; if( this.value != '' ) { text = this.value; } $('profile_address_text').innerHTML = text.replace(/[^a-z0-9]/gi,''); });
	if( $('username').value.length ) { $('username').fireEvent('keyup'); }
  }
});
//]]>
window.addEvent('domready', function() {
  $$('#connect_signup_box .form-element > input').addEvents({
    focus: function () { this.getParent('.form-element').addClass('form_element_active') },
    blur: function () { this.getParent('.form-element').removeClass('form_element_active'); }
  });
});
</script>

<style type="text/css">
.layout_theme_home { height: <?php echo $home_imageheight; ?>; position: relative; overflow: hidden; }
.layout_theme_home > div > div { padding-bottom: 30px; }
.layout_theme_home.img_left > div > div.home_headlines, .layout_theme_home.img_right > div > div.home_headlines { width: 46% }
.layout_theme_home > div > div.home_img { width: 50%; }
.layout_theme_home.img_left > div > div.home_headlines { float: right; text-align: left; }
html[dir="rtl"] .layout_theme_home.img_left > div > div.home_headlines { float: left; text-align: right; }
.layout_theme_home.img_left > div > div.home_img { float: left; text-align: left; }
html[dir="rtl"] .layout_theme_home.img_left > div > div.home_img { float: right; text-align: right; }
.layout_theme_home.img_right > div > div.home_headlines { float: left; text-align: left; }
html[dir="rtl"] .layout_theme_home.img_right > div > div.home_headlines { float: right; text-align: right; }
.layout_theme_home.img_right > div > div.home_img { float: right; text-align: right; }
html[dir="rtl"] .layout_theme_home.img_right > div > div.home_img { float: left; text-align: left; }
.layout_theme_home .home_img > img { max-width: 100%; margin-top: -20px; }
.layout_theme_home .home_img > iframe { width: 100%; }
.layout_theme_home .home_headlines, .layout_theme_home > div > div.home_img { padding-top: <?php echo $home_eltop; ?>; text-align: center; }
.layout_theme_home .home_headlines > * { clear: both; }
.layout_theme_home .home_headline_first { color: #FFF; text-shadow: 1px 1px 0px rgba(0,0,0,0.5); font-size: 32px; line-height: 38px; padding: 0px 0px; margin: 0px; }
.layout_theme_home .home_headline_second { color: #FFF; text-shadow: 1px 1px 0px rgba(0,0,0,0.5); font-size: 20px; line-height: 24px; padding: 5px 0px; }
.layout_theme_home .home_account_buttons > div, .layout_theme_home_bottom .homebottom_signup_button { display: inline-block; margin: 5px; }
.layout_theme_home .home_headline_first + .home_account_buttons, .layout_theme_home .home_headline_second + .home_account_buttons { margin-top: 30px; }
.layout_theme_home.text_center .home_account_buttons { text-align: center; }
.layout_theme_home .home_login_button > a, .layout_theme_home_bottom .homebottom_signup_button > a { display: block; background: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Zephyrtheme/externals/images/black_50.png); color: #FFF; padding: 0px 30px; line-height: 50px; text-decoration: none; font-size: 20px; border-radius: 3px; letter-spacing: 1px; }
.layout_theme_home .home_login_button > a:hover { background: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Zephyrtheme/externals/images/black_80.png); }
.layout_theme_home_bottom .homebottom_signup_button > a { background: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Zephyrtheme/externals/images/black_80.png); }
.layout_theme_home_bottom .homebottom_signup_button > a:hover { background: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Zephyrtheme/externals/images/black_50.png); }
.layout_theme_home .home_signup_button > a { display: block; padding: 0px 30px; line-height: 50px; text-decoration: none; font-size: 20px; border-radius: 3px; letter-spacing: 1px; box-shadow: none; }
.layout_theme_home .home_signup_button > a:hover { box-shadow: inset 0px -50px 0px 0px rgba(0, 0, 0, 0.2); }

<?php if($home_usesignup = '2') { ?>
.layout_theme_home.has_signupform > div > div.home_img { width: 30%; }
.layout_theme_home.img_left.has_signupform > div > div.home_headlines, .layout_theme_home.img_right.has_signupform > div > div.home_headlines { width: 60%; }
#connect_signup_box, #connect_signup_box .global_form > div { width: 100%; }
#connect_signup_box .global_form > div > div { text-align: left; border: none; background: none; filter: none; padding: 0px; box-shadow: 0px 0px 0px 0px transparent; }
html[dir="rtl"] #connect_signup_box .global_form > div > div { text-align: right; }
#connect_signup_box .global_form > div > div > h3 { color: #FFFFFF; text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5); font-size: 32px; line-height: 38px; padding: 0px 0px; margin: 0px 0px 7px; }
#connect_signup_box .global_form .form-elements { padding: 0px 0px !important; margin: 0px 0px !important; }
#connect_signup_box .global_form div.form-wrapper { float: left; width: 100%; clear: both; }
html[dir="rtl"] #connect_signup_box .global_form div.form-wrapper { float: right; }
#connect_signup_box .global_form div.form-element { overflow: visible; position: relative; margin-bottom: 5px; max-width: 100%; width: 100%; }
#connect_signup_box .global_form div.form-label { text-align: left; float: none; color: #FFF; padding: 0px; margin: 3px; }
html[dir="rtl"] #connect_signup_box .global_form div.form-label { text-align: right; }
#connect_signup_box .global_form input, #connect_signup_box .global_form select { border: none; background: #FFF; max-width: 100%; width: 96%; padding: 6px 2%; }
#connect_signup_box .global_form select { width: 100%; }
#connect_signup_box .global_form input[type=checkbox] { width: auto; }
#connect_signup_box .global_form input:focus, #connect_signup_box .global_form select:focus { box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.7); }
#connect_signup_box .global_form input + label, #connect_signup_box .global_form input + label * { color: #FFF; }
#connect_signup_box .global_form input + label * { text-decoration: underline; }
#connect_signup_box .global_form button[type=submit] { padding: 10px 30px; margin-top: 10px; }
#connect_signup_box .global_form p.description { display: none; }
#connect_signup_box .form-element p.description { display: none; background: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Zephyrtheme/externals/images/black_80.png); color: #FFF; padding: 6px 10px; border: 1px solid #000; margin: 0px 5px; max-width: 100%; font-size: 90%; white-space: nowrap; position: absolute; top: -1px; left: 100%; border-radius: 4px; }
#connect_signup_box .form-element.form_element_active p.description { display: inline-block; }
#connect_signup_box .form-element.form_element_active p.description #profile_address_text { color: #FC6; }
html[dir="rtl"] #connect_signup_box .form-element p.description { left: auto; right: 100%; }
.img_right #connect_signup_box .form-element p.description { left: auto; right: 100%; }
html[dir="rtl"] .img_right #connect_signup_box .form-element p.description { left: 100%; right: auto; }
#connect_signup_box .global_form, #connect_signup_box .global_form > div, #connect_signup_box .global_form > div > div, #connect_signup_box .global_form div.form-wrapper, #connect_signup_box .global_form #fieldset-buttons { overflow: visible; }
#connect_signup_box #name-wrapper, #connect_signup_box .global_form div.form-label#terms-label, #connect_signup_box .global_form div.form-label#submit-label { display: none; }
#social_login_box_home { margin: 10px 0px 0px; }
#social_login_box_home > div { float: left; margin: 0px; }
html[dir="rtl"] #social_login_box_home > div { float: right; }
#social_login_box_home > div + div { margin-left: 5px; }
#social_login_box_home .form-wrapper > .form-label { display: none; }
#social_login_box_home span.janrainEngageLabel { color: #FFF; }
<?php } ?>

</style>

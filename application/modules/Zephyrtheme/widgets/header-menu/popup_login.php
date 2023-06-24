<?php if(!$this->viewer->getIdentity()) { ?>
<?php if($this->header_loginbutton == 0 || $this->header_signupbutton == 0) { ?>
  <!-- START - Connections -->
  <div id="top_connection_box_container" class="clearfix" style="display: none;">
  
	<div>
	  <h3>
		<span id="login_titlex" style="display: none;"><?php echo $this->translate("Member Sign In"); ?></span>
		<span id="signup_titlex" style="display: none;"><?php echo $this->translate("Create Account"); ?></span>
		<div class="close_button">
		  <a href="javascript:void(0);" id="top_button_close"><span class="top_connection_button_text"><?php echo $this->translate("Close"); ?></span><span class="top_connection_button_icon">&#215;</span></a>
		</div>
	  </h3>
	  <!-- START - Login Box - Hidden on Login Page -->
	  <div class="connect_box_form_login">
		<!-- START - Social Connections -->
		<?php
		$settings = Engine_Api::_()->getApi('settings', 'core');
		if($settings->getSetting('core_facebook_enable', 'none') != 'none' || $settings->getSetting('core_twitter_enable', 'none') != 'none' || $settings->getSetting('core_janrain_enable', 'none') != 'none')
		{
		  echo '<div id="social_login_box"><h4><span>'.$this->translate('Login with a social network').'</span></h4>';
		  $socialForm = new User_Form_Login();
		  echo $socialForm->getElement('facebook');
		  echo $socialForm->getElement('twitter');
		  echo $socialForm->getElement('janrain');	
		  echo '</div>';	
		}
		?>
		<!-- END - Social Connections -->
		
		<!-- START - Login Box - Hidden on Login Page -->
		<div id="login_popup" class="connect_box_form_login_form" style="display: none;">
		  <h4><span><?php echo $this->translate('Login with email and password'); ?></span></h4>
		  <?php
		  $loginForm = new User_Form_Login();
		  $loginForm->setTitle('');
		  $loginForm->setDescription('');
		  $loginForm->removeElement('forgot');
		  $loginForm->removeElement('facebook');
		  $loginForm->removeElement('twitter');
		  $loginForm->removeElement('janrain');
		  echo $loginForm;
		  ?>
		  
		  <div class="forgot_register">
			<div class="forgot_pass"><a href="<?php echo $this->url(array('module'=>'user','controller' => 'auth','action'=>'forgot'), 'default', true); ?>"><?php echo $this->translate('Forgot Password?'); ?></a></div>
			<div class="register_now hide_on_signup"><span><?php echo $this->translate('No account yet?'); ?></span> <a href="<?php echo ($this->header_signupbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'signup'), 'default', true); ?>" class="button_signup_2"><?php echo $this->translate('Register Now'); ?></a></div>
		  </div>
		  
		</div>
		<!-- END - Login Box -->
		
        <?php if($this->header_signupbutton == 0) { ?>
		<!-- START - Signup Box - Hidden on Signup Page -->
		<div id="signup_popup" style="display: none;">
		  <h4><span><?php echo $this->translate('Create Your Account'); ?></span></h4>
		  <?php
		  $signupForm = new User_Form_Signup_Account();
		  $signupForm->setTitle('');
		  $signupForm->setDescription('');
		  foreach ($signupForm->getElements() as $element) {
			if ($element instanceof Zend_Form_Element_Text || $element instanceof Zend_Form_Element_Textarea || $element instanceof Zend_Form_Element_Password) {
			  $element->setAttrib('autofocus', null);
			}
		  }
		  echo $signupForm;
		  ?>
		  
		  <div class="forgot_register">
			<div class="register_now hide_on_login"><span><?php echo $this->translate('Already a member?'); ?></span> <a href="<?php echo ($this->header_loginbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'login'), 'default', true); ?>" class="button_login_2"><?php echo $this->translate('Login Now'); ?></a></div>
		  </div>
		</div>
		<!-- END - Signup Box -->
        <?php } ?>

		
	  </div>
	</div>
		  
  </div>  
  <!-- END - Connections -->
<?php } ?>
<?php } ?>
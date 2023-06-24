<?php //
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: appconfig.tpl 2011-04-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
//<?php //if (count($this->navigation)): ?>
<!--  <div class='tabs'>
    //<?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>-->
//<?php //endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'sitestoreinvite', 'controller' => 'global'), 'sitestoreinvite_global_global', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreinvite/externals/images/back.png);"><?php echo $this->translate("Back to Global Settings") ?></a>
  <div class='settings' style="margin-top:15px;">
    <h3><?php echo $this->translate("Guidelines to configure the applications for Contact Importer Settings") ?></h3>
    <p><?php echo $this->translate('Please click on the application names given below and follow the corresponding steps.') ?></p><br />
    <div class="admin_files_wrapper" style="width:930px;">
      <ul class="admin_files guidelines_faq" style="max-height:none;">

        <li>
          <a href="javascript:void(0);" onClick="guideline_show('guideline_2');"><?php echo $this->translate("Yahoo Application"); ?></a>
          <div class='faq' style='display: none;' id='guideline_2'>
            <?php echo $this->translate('Getting Yahoo API Key :-'); ?>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('yahoostep-1');"><?php echo $this->translate("Step 1") ?></a>
              <div id="yahoostep-1" style='display: none;'>
                <p>
                  <?php echo $this->translate("Go here to register your application:"); ?> <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_blank" style="color:#5BA1CD;">https://developer.apps.yahoo.com/dashboard/createKey.html</a><br />

                <div class="bold"><?php echo $this->translate("About creating your application :-"); ?></div>

                <?php echo $this->translate("a) Write your application name. You can make this the same as your site name."); ?><br />
                <?php echo $this->translate("b) In the 'Kind of Application' field, choose : 'Web-based'."); ?><br />
                <?php echo $this->translate("c) Write a short description about your application/website."); ?><br />
                <?php echo $this->translate("d) In the 'Application URL' field, write the URL of your site (example : http://www.mysite.com)."); ?><br />

                <div class="bold"><?php echo $this->translate("Security &amp; Privacy:-"); ?></div>
                <?php echo $this->translate("a) In the 'Application Domain' field, write your site's domain (example : http://www.mysite.com)."); ?><br />
                <?php echo $this->translate("b) Choose the 'Access Scopes' field to : 'This app requires access to private user data.'. Selecting this option will give you a list of Yahoo services. Among them, choose 'Yahoo! Contacts' in 'Read' mode."); ?><br />
                <?php echo $this->translate("c) Check on the 'Terms of Use' checkbox."); ?><br />
                <?php echo $this->translate("d) Now click on the 'Get API Key' button. After clicking on this button you will be redirected to a store where it would ask you to verify your domain. So, verify your domain."); ?><br />


                </p>
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/yahst1.gif" alt="Step 1" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('yahoostep-2');"><?php echo $this->translate("Step 2") ?></a>
              <div id="yahoostep-2" style='display: none;'>
                <p>
                  <?php echo $this->translate("a) Verify your domain."); ?><br />
                  <?php echo $this->translate("b) After verifying your domain successfully, you will be redirected to a success store where you will get your 'API Key' and 'Shared Secret Key'. Copy them and paste these values in your site's Yahoo contact importer settings fields."); ?><br />

                </p>
                <img src="https://lh4.googleusercontent.com/_nEoXA-sO-_M/TZxrEpPl3HI/AAAAAAAAABY/T7z5wR0Hh7c/yahst2.gif" alt="Step 2" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('yahoostep-3');"><?php echo $this->translate("Step 3") ?></a>
              <div id="yahoostep-3" style='display: none;'>
                <p><?php echo $this->translate("Copy below 'Consumer Key (API Key)' and 'Consumer Secret (Shared Secret Key)' and paste these values in your site's Yahoo contact importer settings fields."); ?><br /></p>
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/yahst3.gif" alt="Step 3" />
              </div>
            </div>

          </div>
        </li>

        <li>
          <a href="javascript:void(0);" onClick="guideline_show('guideline_3');"><?php echo $this->translate("Google Application"); ?></a>
          <div class='faq' style='display: none;' id='guideline_3'>
            <?php echo $this->translate('Getting Google to work :-'); ?>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('googlestep-1');"><?php echo $this->translate("Step 1") ?></a>
              <div id="googlestep-1" style='display: none;'>
                <p>

                  <?php echo $this->translate("Go here to register your domain:"); ?> <a href="https://www.google.com/accounts/ManageDomains" target="_blank">https://www.google.com/accounts/ManageDomains</a><br />

                <div class=bold"><?php echo $this->translate("About registering your domain :-"); ?></div>
                <?php echo $this->translate("In the 'Add a New Domain' section, write your site's domain (example : www.example.com) and then click on 'Add domain' button."); ?><br />

                </p>
                <img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrH5gRfuI/AAAAAAAAACU/XLHeLhYo-28/googlest1.gif" alt="Step 1" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('googlestep-2');"><?php echo $this->translate("Step 2") ?></a>
              <div id="googlestep-2" style='display: none;'>
                <p>
                  <?php echo $this->translate("Click on the domain link being shown in the 'Manage registration' section."); ?><br />

                </p>
                <img src="https://lh4.googleusercontent.com/_nEoXA-sO-_M/TZxrH6ectTI/AAAAAAAAACY/FjtLIVfU6xk/googlest2.gif" alt="Step 2" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('googlestep-3');"><?php echo $this->translate("Step 3") ?></a>
              <div id="googlestep-3" style='display: none;'>
                <p>
                  <?php echo $this->translate("Verify your domain."); ?><br />
                  <?php echo $this->translate("Select the first option 'Upload an HTML file to your server' and follow the instructions given below the options to verify your domain."); ?><br />

                </p>
                <img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrHvf-BzI/AAAAAAAAACQ/VtWxy1e2d7M/s640/googlest3.gif" alt="Step 3" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('googlestep-4');"><?php echo $this->translate("Step 4") ?></a>
              <div id="googlestep-4" style='display: none;'>
                <p>
                  <?php echo $this->translate("Click on the 'I agree to the Terms and Service' button."); ?><br />

                </p>
                <img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrHZfXVkI/AAAAAAAAACM/4_rIeKAVOPM/googlest4.gif" alt="Step 4" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('googlestep-5');"><?php echo $this->translate("Step 5") ?></a>
              <div id="googlestep-5" style='display: none;'>
                <p>
                  <?php echo $this->translate("Fill the 'Target URL path prefix' and 'Domain description' fields and then click on the 'Save' button."); ?><br />
                </p>
                <img src="https://lh4.googleusercontent.com/_nEoXA-sO-_M/TZxrHPz3eTI/AAAAAAAAACE/EjVLV2D5YRE/googlest5.gif" alt="Step 5" />
              </div>
            </div>

          </div>
        </li>

        <li>
          <a href="javascript:void(0);" onClick="guideline_show('guideline_4');"><?php echo $this->translate("Windows Live Application"); ?></a>
          <div class='faq' style='display: none;' id='guideline_4'>
            <?php echo $this->translate('Getting your Windows Live Application ID :-'); ?>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-1');"><?php echo $this->translate("Step 1") ?></a>
              <div id="msnstep-1" style='display: none;'>
                <p><?php echo $this->translate("To register your application, go here :"); ?> <a href="http://msdn.microsoft.com/en-us/library/cc287659.aspx" target="_blank" style="color:#5BA1CD;">http://msdn.microsoft.com/en-us/library/cc287659.aspx.</a><br /><br />
                  <?php echo $this->translate("Click on the 'Windows Live application management site' in the 'Registering Your Application' section."); ?><br />
                </p>
                <img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrHAY0KxI/AAAAAAAAACA/_sCm06Mf8Vs/s720/msnst1.gif" alt="Step 1" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-2');"><?php echo $this->translate("Step 2") ?></a>
              <div id="msnstep-2" style='display: none;'>
                <p>
                  <?php echo $this->translate("Click on the 'Add an application' link."); ?><br/>
                </p>
                <img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrG25O-tI/AAAAAAAAACI/GuXBM-M6Uy0/msnst2.gif" alt="Step 2" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-3');"><?php echo $this->translate("Step 3") ?></a>
              <div id="msnstep-3" style='display: none;'>
                <p>
                  <?php echo $this->translate("a) Write your application name. You can make this the same as your site name."); ?><br/>
                  <?php echo $this->translate("b) In the 'Application type' field, choose : 'Web application'."); ?><br/>
                  <?php echo $this->translate("c) In the 'Domain' field, write your site's domain (example : www.mysite.com)."); ?><br/>

                </p>
                <img src="https://lh3.googleusercontent.com/_nEoXA-sO-_M/TZxrGqxE8GI/AAAAAAAAAB8/qgJc3o1QZ3w/msnst3.gif" alt="Step 3" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-4');"><?php echo $this->translate("Step 4") ?></a>
              <div id="msnstep-4" style='display: none;'>
                <p>
                  <?php echo $this->translate("Click on the 'Essentials' link."); ?><br/>
                </p>
                <img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrGdQUa5I/AAAAAAAAABw/7zhBSudu_xo/msnst4.gif" alt="Step 4" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-5');"><?php echo $this->translate("Step 5") ?></a>
              <div id="msnstep-5" style='display: none;'>
                <p>

                  <?php echo $this->translate("For verifying your domain, click on the 'Verify now' link."); ?><br/>
                </p>
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreinvite/externals/images/msnst5.gif" alt="Step 5" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-6');"><?php echo $this->translate("Step 6") ?></a>
              <div id="msnstep-6" style='display: none;'>
                <p>
                  <?php echo $this->translate("Verify your domain. Follow the instructions given on this store for verifying your domain."); ?><br/>

                </p>
                <img src="https://lh3.googleusercontent.com/_nEoXA-sO-_M/TZxrF_HHayI/AAAAAAAAABo/t3jzzj_gZvk/msnst6.gif" alt="Step 6" />
              </div>
            </div>

            <div class="steps">
              <a href="javascript:void(0);" onClick="guideline_show('msnstep-7');"><?php echo $this->translate("Step 7") ?></a>
              <div id="msnstep-7" style='display: none;'>
                <p>
                  <?php echo $this->translate("After verifying your domain successfully, you will be redirected to the below success store where you will get your 'Client ID' and 'Secret Key'. Copy them and paste these values in your site's Windows Live contact importer settings fields."); ?><br/>
                </p>
                <img src="https://lh6.googleusercontent.com/_nEoXA-sO-_M/TZxrFYGQMFI/AAAAAAAAABk/KgAdqm8qJdo/s800/msnst7.gif" alt="Step 7" />
              </div>
            </div>

          </div>
        </li>

      </ul>
    </div>
  </div>
</div>
<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
	<?php if(!empty($this->success)): ?>
		<ul class="form-notices" >
			<li>
				<?php echo $this->translate($this->success); ?>
			</li>
		</ul>
  <?php endif; ?>
	<?php echo $this->form->render($this); ?>
  <?php if ($this->languageCount > 1 && $this->multiLanguage): ?>
  <div id="multiLanguageOverviewLinkShow" class="form-wrapper">
    <div class="form-element">
      <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Overview in the multiple languages supported by this website."); ?></b></a>
    </div>
  </div>
        
  <div id="multiLanguageOverviewLinkHide" class="form-wrapper">
    <div class="form-element">
      <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Edit Overview in the primary language of this website."); ?></b></a>
    </div>
  </div>
<?php endif;?>
	<script type="text/javascript">
		var catdiv1 = $('overview-label');
		var catdiv2 = $('save-label');  
		var catarea1 = catdiv1.parentNode;
		catarea1.removeChild(catdiv1);
		var catarea2 = catdiv2.parentNode;
		catarea2.removeChild(catdiv2);
	</script>
    
  <script type="text/javascript">
        
    en4.core.runonce.add(function() {
      var multiLanguage = '<?php echo $this->multiLanguage; ?>';
      var languageCount = '<?php echo $this->languageCount; ?>';
      var overviewParent = $('<?php echo $this->add_show_hide_overview_link; ?>').getParent().getParent();
      if (multiLanguage == 1 && languageCount > 1) {
        $('multiLanguageOverviewLinkShow').inject(overviewParent, 'after');
        $('multiLanguageOverviewLinkHide').inject(overviewParent, 'after');
        multiLanguageOverviewOption(1);
      }
    }); 
        
        
    var multiLanguageOverviewOption = function(show) {
          
<?php
foreach ($this->languageData as $value):
  
    if ($value == 'en') :
    $value = '';
  else :
    $value = "_$value";
  ?>
      $('overview' + '<?php echo $value; ?>' + '-element').setStyle('max-width', 'none');
      <?php
  if ($this->defaultLanguage == $value) {    
    continue;
  }
    ?> 
            if (show == 1) {
              $('overview' + '<?php echo $value; ?>' + '-wrapper').setStyle('display', 'none');
              $('multiLanguageOverviewLinkShow').setStyle('display', 'block');
              $('multiLanguageOverviewLinkHide').setStyle('display', 'none');
            }
            else {
              $('overview' + '<?php echo $value; ?>' + '-wrapper').setStyle('display', 'block');
              $('multiLanguageOverviewLinkShow').setStyle('display', 'none');
              $('multiLanguageOverviewLinkHide').setStyle('display', 'block');
            }
  <?php endif; ?>
<?php endforeach; ?>
    }
  </script>
  
  
  
</div>
</div>
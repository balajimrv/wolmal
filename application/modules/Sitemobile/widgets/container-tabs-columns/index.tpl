<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<!--TAB COLLAPSIBLE VIEW-->
<?php if ($this->layoutContainer == 'tab'): ?>
  <div class='tabs_alt tabs_parent tabs-ui-collapsible'>
    <div data-role="collapsible-set" data-theme="c" data-content-theme="c" data-collapsed-icon="arrow-r" data-count-theme="b">
      <?php foreach ($this->tabs as $key => $tab): ?>
        <?php
        $class = array();
        $class[] = 'tab_' . $tab['id'];
        $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
        if ($this->activeTab == $tab['id'] || $this->activeTab == $tab['name'])
          $class[] = 'active';
        $class = join(' ', $class);
        ?>
        <div data-role="collapsible" data-collapsed="<?php echo $tab['collapsible']; ?>" data-mini="true"  class="<?php echo $class; ?> ui-li-has-count" data-expanded-icon="arrow-d">
          <h3 class="tabs_title">
            <?php echo $this->translate($tab['title']); ?>
            <?php if ($tab['childCount']): ?>
              <span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?php echo $tab['childCount']; ?></span>
            <?php endif; ?>
          </h3>
          <?php echo $tab['childrenContent']; ?>
        </div>  
      <?php endforeach; ?>
    </div>
  </div>

  <!--VERTICAL COLUMN VIEW-->
<?php elseif ($this->layoutContainer == 'vertical'): ?>

  <div class='tabs_alt tabs_parent tabs-ui-vertical'>
    <div class="ui-grid-b ui-responsive">
      <?php $count = 0; ?>
      <?php foreach ($this->tabs as $key => $tab): ?>
        <?php
        $class = array();
        $class[] = 'tab_' . $tab['id'];
        $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
        if ($this->activeTab == $tab['id'] || $this->activeTab == $tab['name'])
          $class[] = 'active';
        $class = join(' ', $class);
        ?>
        <?php
        if ($count % 3 == 0):
          $class = "ui-block-a";
        elseif ($count % 3 == 1):
          $class = "ui-block-b";
        else:
          $class = "ui-block-c";
        endif;
        $count++;
        ?>
        <div class="<?php echo $class ?>">
          <h3><?php echo $this->translate($tab['title']); ?></h3>
          <p><?php echo $tab['childrenContent']; ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!--HORIZONTAL VIEW-->
  <!--HORIZONTAL ICON VIEW-->
<?php elseif ($this->layoutContainer == 'horizontal'  || $this->layoutContainer == 'horizontal_icon'): ?>
  <?php $random=rand(10000000, 99999999);?> 
  <div class="tabs_alt tabs_parent tabs-ui-horizontal sm-ui-profile-tabs" id="tab_wrapper_<?php echo $this->identity."_".$random;?>">
    <?php $isFirst=true;?>
    <ul class="localnav" data-corners="false" style="width: 2000px;">
      <?php $count=0;?>
    <?php foreach ($this->tabs as $key => $tab): ?>
      <?php
      $class = array();
      $class[] = 'tab_' . $tab['id'];
      $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
      if ($this->activeTab == $tab['id'] || $this->activeTab == $tab['name'])
        $class[] = 'active ui-btn-active';
      $class = join(' ', $class);
      $params=array('tab' => $tab['id']);

//      if ($isFirst):
//      unset($params['tab']);
//      unset($params['tabstype']);
//     $isFirst=false;
//      endif;
      if(!empty ($this->action_id)): 
        $params['action_id']=0; endif;
      ?>
      <li class="<?php echo $class ?>">
        <a href="<?php echo $this->subject() ? $this->subject()->getHref($params):$this->url($params); ?>" class="ui-link-inherit">
          <?php if($this->layoutContainer == 'horizontal_icon'):?>
          	<span class="tab-icon"><i></i></span>
          <?php endif; ?>
					<span class="tab-title"><?php echo $this->translate($tab['title']) ?> <?php if (!empty($tab['childCount'])): ?>(<?php echo $tab['childCount'] ?>)<?php endif; ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
  <?php echo $this->activeChildrenContent; ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
        var pageShowScrollTab = function(event){          
        if($.mobile.activePage.find('.tabs-ui-horizontal').length)
         $("html, body").animate({ scrollTop: ($.mobile.activePage.find('.layout_sitemobile_container_tabs_columns').offset().top -80)}, 10); 
         $(document).off('pageshow',pageShowScrollTab);
         };
         
         $.mobile.activePage.find('.localnav li').on('click',function(){
           $(document).on('pageshow',pageShowScrollTab);
         });
        setTimeout(function(){
           sm4.core.tab.setScroll('tab_wrapper_<?php echo $this->identity."_".$random;?>');
        },100);
    });
  
  </script>
  <!--PANEL VIEW-->
<?php elseif ($this->layoutContainer == 'panel'): ?>

  <?php $activeTabTitle = '';
  $activeTabChildCount; ?>
  <?php if (count($this->tabs) > 1): ?> 
      <script type="text/javascript">
    sm4.core.runonce.add(function() {
        var pageShowScrollPanelTab = function(event){
         $("html, body").animate({ scrollTop: $.mobile.activePage.find('.tabs-ui-panel-a').offset().top }, 10); 
         $(document).off('pageshow',pageShowScrollPanelTab);
         };
         
         $.mobile.activePage.find('.tabPanelUl li').on('click',function(){
           $(document).on('pageshow',pageShowScrollPanelTab);
         });
    });
  
  </script>
    <div data-role="popup" id="tabPanel<?php echo $this->identity;?>" data-corners="true" data-theme="none" data-shadow="false" data-tolerance="0,0">
      <ul data-role="listview" data-count-theme="a" data-inset="true" class="tabPanelUl">
        <?php foreach ($this->tabs as $key => $tab): ?>
          <?php
          $class = array();
          $class[] = 'tab_' . $tab['id'];
          $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
          if ($this->activeTab == $tab['id'] || $this->activeTab == $tab['name']) {
            $class[] = 'active ui-btn-active';
            $activeTabTitle = $tab['title'];
            if (!empty($tab['childCount'])):
              $activeTabChildCount = $tab['childCount'];
            endif;
          }
          $class = join(' ', $class);
          ?>
          <li class="<?php echo $class ?>"  data-mini="true" data-icon="false" data-allcorners="true"  data-shadow="true">
             <?php $params=array('tab' => $tab['id']); if(!empty ($this->action_id)): $params['action_id']=0; endif;?>
            <a href="<?php echo $this->subject() ? $this->subject()->getHref($params):$this->url($params); ?>">
            <?php echo $this->translate($tab['title']) ?><?php if (!empty($tab['childCount'])): ?>
                <span class="ui-li-count">(<?php echo $tab['childCount'] ?>)</span><?php endif; ?>
            </a>
          </li>
    <?php endforeach; ?>
        <li data-icon="none" class="pop_close"><a href="#" data-rel="back"><?php echo $this->translate('Cancel'); ?></a></li>
      </ul>
    </div>  
  <?php endif; ?>




  <div  class="ui-collapsible ui-collapsible-inset">  
  	<?php if (count($this->tabs) > 1): ?> 
			<a class="tabs-ui-panel-a" href="#tabPanel<?php echo $this->identity;?>" data-rel="popup" data-transition="slide"><?php endif; ?>
				<h3 class="ui-collapsible-heading">
					<span class="ui-btn ui-fullsize  ui-corner-top ui-btn-up-a sm_ui_panel"  data-theme="a" data-mini="true">
						<span class="ui-btn-inner ui-corner-top">
							<span class="sm_ui_button_ops">
								<span class="sm_ui_button_ops_name"><?php echo $this->translate($activeTabTitle) . " "; ?></span>
                <?php if ($activeTabChildCount): ?><span class="sm_ui_button_ops_count">(<?php echo $activeTabChildCount ?>)</span><?php endif; ?>
                <?php if (count($this->tabs) > 1): ?> 
								<span class="ui-icon ui-icon-reorder ui-icon-shadow">&nbsp;</span>
                <?php endif; ?>
							</span>
						</span>
					</span>
				</h3>
				<?php if (count($this->tabs) > 1): ?> 
			</a>
		<?php endif; ?>
  	<div class="ui-collapsible-content ui-body-c ui-corner-bottom" aria-hidden="false">		
  		<?php echo $this->activeChildrenContent; ?>			
  	</div>
	</div>
<?php endif; ?>

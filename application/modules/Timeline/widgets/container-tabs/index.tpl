<script type="text/javascript">
  en4.core.runonce.add(function() {
    new Tips($$('.Tips'));
    var tabContainerSwitch = window.tabContainerSwitch = function(element) {
      if( element.tagName.toLowerCase() == 'a' ) {
        element = element.getParent('li');
      }

      var myContainer = $('content_div');

      myContainer.getChildren('div:not(.un_tabs)').setStyle('display', 'none');
      $('main_tabs').getElements('li').removeClass('active');
      element.get('class').split(' ').each(function(className){
        className = className.trim();
        if( className.match(/^tab_[0-9]+$/) ) {
          myContainer.getChildren('div.' + className).setStyle('display', null);
          element.addClass('active');
        }
      });
    }
    var moreTabSwitch = window.moreTabSwitch = function(el) {
      el.toggleClass('tab_open');
      el.toggleClass('tab_closed');
    }
  });
</script>
<div class="layout_core_container_tabs">
<div class='un_tabs tabs_alt tabs_parent <?php echo $this->main_div_class ?>'>
  <ul id='main_tabs' class="<?php echo $this->main_tabs_class ?>">
    <?php foreach( $this->tabs as $key => $tab ): ?>
      <?php
        $class   = array();
        $class[] = 'tab_' . $tab['id'];
        $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
        if( $this->activeTab == $tab['id'] || $this->activeTab == $tab['name'] )
          $class[] = 'active';
        $class = join(' ', $class);
        $file_icon = 'tab_' . $tab['name'] . '.png';
      ?>
      <?php if( $key < $this->max ): ?>
        <li class="<?php echo $class ?> <?php if (!$this->show_title):?>Tips<?php endif; ?>" <?php if (!$this->show_title):?>title="<?php echo $this->translate($tab['title']) ?>"<?php endif; ?>>
            <a href="javascript:void(0);" onclick="tabContainerSwitch($(this), '<?php echo $tab['containerClass'] ?>');">
                <?php if (file_exists(APPLICATION_PATH . '/public/timeline/tab_icons/' . $file_icon)): ?>
                    <img alt="Tab icon" src="<?php echo $this->baseUrl();?>/public/timeline/tab_icons/<?php echo $file_icon;?>" />
                <?php else: ?>
                    <img alt="Tab Icon" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/default.png"/>
                <?php endif;?>
                <?php if ($this->show_title):?><?php echo $this->translate($tab['title']) ?><?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?><?php endif; ?>
            </a>
        </li>
      <?php endif;?>
    <?php endforeach; ?>
    <?php if (count($this->tabs) > $this->max):?>
    <li class="tab_closed more_tab" onclick="moreTabSwitch($(this));">
      <div class="tab_pulldown_contents_wrapper">
        <div class="tab_pulldown_contents">
          <ul>
          <?php foreach( $this->tabs as $key => $tab ): ?>
            <?php
              $class   = array();
              $class[] = 'tab_' . $tab['id'];
              $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
              if( $this->activeTab == $tab['id'] || $this->activeTab == $tab['name'] ) $class[] = 'active';
              $class = join(' ', array_filter($class));
              $file_icon = 'tab_' . $tab['name'] . '.png';
            ?>
            <?php if( $key >= $this->max ): ?>
              <li class="<?php echo $class ?> <?php if (!$this->show_title):?>Tips<?php endif; ?>" onclick="tabContainerSwitch($(this), '<?php echo $tab['containerClass'] ?>')" <?php if (!$this->show_title):?>title="<?php echo $this->translate($tab['title']) ?>"<?php endif; ?>>
                <?php if (file_exists(APPLICATION_PATH . '/public/timeline/tab_icons/' . $file_icon)): ?>
                    <img alt="Tab icon" src="<?php echo $this->baseUrl();?>/public/timeline/tab_icons/<?php echo $file_icon;?>" />
                <?php else: ?>
                    <img alt="Tab Icon" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/default.png"/>
                <?php endif;?>
                <?php if ($this->show_title):?><?php echo $this->translate($tab['title']) ?><?php if( !empty($tab['childCount']) ): ?><span> (<?php echo $tab['childCount'] ?>)</span><?php endif; ?><?php endif; ?>
              </li>
            <?php endif;?>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php $file_icon = 'tab_more.png';?>
      <a href="javascript:void(0);" >
        <?php if (file_exists(APPLICATION_PATH . '/public/timeline/tab_icons/' . $file_icon)): ?>
            <img alt="Tab icon" src="<?php echo $this->baseUrl();?>/public/timeline/tab_icons/<?php echo $file_icon;?>" />
        <?php else: ?>
            <img alt="Tab Icon" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/more.png"/>
      	<?php endif;?>
        <?php if ($this->show_title) echo $this->translate('More +') ?>
        <span></span>
      </a>
    </li>
    <?php endif;?>
    <div style="clear:both"></div>
  </ul>
  <div style="clear:both"></div>
  </div>
</div>

<div class="un_tabs_content" id="content_div">
	<?php echo $this->childrenContent ?>
</div>

<?php if (empty($this->is_ajax)) :
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl';
endif; ?>


<?php if (empty($this->is_ajax)) : ?>
  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
    <?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
    ?>
    <?php
    /* Include the common user-end field switching javascript */
    echo $this->partial('_jsSwitch.tpl', 'fields', array(
            //'topLevelId' => (int) @$this->topLevelId,
            //'topLevelValue' => (int) @$this->topLevelValue
    ))
    ?>

    <div class="sitestore_edit_content">
      <div class="sitestore_edit_header">
        <a class="sitestoreproduct_buttonlink" href='<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->store_id)), 'sitestore_entry_view', true) ?>'><?php echo $this->translate('VIEW_STORE'); ?></a>
        <h3><?php echo $this->translate('Dashboard: ') . $this->sitestore->title; ?></h3>
      </div>
			
      <div id="show_tab_content">
      <?php endif; ?>

      <div class="seaocore_tbs_cont" id="dynamic_menus_content"></div>

      <?php if (empty($this->is_ajax)) : ?>	
      </div>
    </div>	
  </div>
<?php endif; ?>
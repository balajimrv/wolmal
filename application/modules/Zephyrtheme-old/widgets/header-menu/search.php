<?php if( $this->search_check ) { ?>
<!-- START - Search Box -->
<div id="global_search_form_container">
  <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
	<input type='text' class='text suggested' name='query' id='global_search_field' alt='<?php echo $this->translate('Search for...') ?>' value='<?php echo $this->translate('Search for...') ?>' autocomplete="off" />
	<button id="search_field_clear" type="button" style="visibility: hidden;"><?php echo $this->translate("clear") ?></button>
	<button id="global_search_button" type="submit" ><?php echo $this->translate("Search") ?></button>
  </form>
</div>
<!-- END - Search Box -->
<?php } ?>
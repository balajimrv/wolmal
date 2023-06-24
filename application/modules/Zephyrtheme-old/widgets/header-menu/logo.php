<div class="generic_layout_container layout_core_menu_logo <?php echo ($logo) ? 'img_logo' : 'txt_logo'; ?>">
  <?php echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title))) : $this->htmlLink($route, $title); ?>
</div>
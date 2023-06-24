<div class="footer_1 footer_menu_container clearfix">
  <div class="footer_copyright"><?php echo $this->translate('Copyright &copy; %s', date('Y')); ?></div>  
  
  <ul class="footer_menu">
    <?php foreach( $this->navigation as $item ):
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href'
      )));
      ?>
      <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?></li>
    <?php endforeach; ?>
  </ul>
  
  <?php include 'social_icons.php'; ?>
    
  <?php if( 1 !== count($this->languageNameList) ): ?>
  <div class="footer_language">
    <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
  </div>
  <?php endif; ?>
</div>
<div class="footer_2 footer_menu_container">
  <div class="footer_top clearfix">
  
    <div class="footer_col col_1"><div>
      <div class="footer_logo <?php echo ($logo) ? 'img_logo' : 'txt_logo'; ?>">
        <?php echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title))) : $this->htmlLink($route, $title); ?>
      </div>
      
      <div class="footer_copyright"><?php echo $this->translate('Copyright &copy;%s', date('Y')); ?></div>  
      
	  <?php if( 1 !== count($this->languageNameList) ): ?>
      <div class="footer_language">
        <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>">
          <?php $selectedLanguage = $this->translate()->getLocale() ?>
          <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
          <?php echo $this->formHidden('return', $this->url()) ?>
        </form>
      </div>
      <?php endif; ?>
  
      <?php include 'social_icons.php'; ?>
      
    </div></div>
    
    <div class="footer_col col_2"><div>
      <h3><?php echo $this->translate('Useful Links'); ?></h3>
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
    </div></div>
    <?php if(0): ?>
    <div class="footer_col col_3"><div>
      <h3><?php echo $this->translate('More Info'); ?></h3>
      <div><?php echo $this->translate('Footer Description'); ?></div>
     <?php endif; ?>
    </div></div>
    
  </div>
  
</div>
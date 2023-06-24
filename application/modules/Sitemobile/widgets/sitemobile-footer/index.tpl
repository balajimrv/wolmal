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
<?php $flagMenuSeprater= false; ?>
<?php if (in_array('copyright', $this->showsFooterOptions)): ?>
<?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
<?php $flagMenuSeprater = true; ?>
<?php endif; ?>
<?php if($this->navigation):?>
<?php foreach( $this->navigation as $item ):
  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
    'reset_params', 'route', 'module', 'controller', 'action', 'type',
    'visible', 'label', 'href'
  )));
  ?>
<?php if($flagMenuSeprater): ?>
  &nbsp;-&nbsp;
  <?php endif; ?>
  <?php $flagMenuSeprater= true; ?>
  <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if(in_array('languageChooser', $this->showsFooterOptions) && 1 !== count($this->languageNameList) ): ?>
<form method="post" id="select_language_form"  action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" class="select_language">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => 'var $this=$(this);setTimeout(function(){ $this.closest(\'form\').submit(); },200)'), $this->languageNameList) ?>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
<?php endif; ?>

<?php if( !empty($this->affiliateCode) ): ?>
  <div class="affiliate_banner">
    <?php 
      echo $this->translate('Powered by %1$s', 
        $this->htmlLink('http://www.socialengine.net/?source=v4&aff=' . urlencode($this->affiliateCode), 
        $this->translate('SocialEngine Community Software'),
        array('target' => '_blank')))
    ?>
  </div>
<?php endif; ?>

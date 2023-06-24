<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _navIcons.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php     
	$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
  $checkActionName= "";
  if((isset($p['module']) && $p['module'] == 'user') && (isset($p['controller']) && $p['controller'] == 'index') &&  (isset($p['action']) && $p['action'] == 'index') && Engine_Api::_()->sitemobile()->isApp())
  $p['module'] = 'messages';
  if((isset($p['module']) && $p['module'] == 'user') && (isset($p['controller']) && $p['controller'] == 'profile') &&  (isset($p['action']) && $p['action'] == 'index')) {
		$checkActionName = $p['module'] . '_' .  $p['controller'] . '_' . $p['action']; 
  }
?>

<?php 

 $cond = Engine_Api::_()->sitemobile()->isApp() ? (count($this->container)>1 || ((!isset($p['module']) || (isset($p['module']) && $p['module'] != 'user') && $p['module'] !='messages' && $p['module'] !='album' && $p['module'] !='video' && $p['module'] !='event' && $p['module'] !='blog' && $p['module'] !='music' && ($p['module'] !='group' || $p['action'] != 'profile' )) && count($this->container) > 0)) : (count($this->container)>1 || ((!isset($p['module']) || (isset($p['module']) && $p['module'] != 'user') && $p['module'] !='messages') && count($this->container) > 0));
 
  if(!isset($p['module']))
    $p['module'] ='core';
   if(count($this->container)>1 || $this->subject() /*&& $p['module'] != 'user' && $p['module'] !='messages'*/):?>
	<?php $id_unq=rand(10, 99); ?>
	<a data-role="button" class="header_options_menu" href="#icons_options_<?php echo $id_unq; ?>" data-rel="popup" data-icon="ellipsis-vertical" data-iconpos="notext" data-transition="pop"><?php //echo $this->translate('Options');?></a>
	<div data-role="popup" id="icons_options_<?php echo $id_unq; ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
		<div data-inset="true" style="min-width:150px;" class="sm-options-popup">
			<?php foreach ($this->container as $link): ?>
      <?php
          $label = strtolower($link->getLabel());
          $addDanger = false;
          foreach (array('delete', 'remove') as $find):
            $pos = strpos($label, $find);
            if ($pos !== false):
              $addDanger = true;
              break;
           endif;
          endforeach;
          $class = $addDanger ? 'ui-btn-danger' : 'ui-btn-action'
          ?>
				<?php  $attribs = array('class' => 'ui-btn-default buttonlink ' .$class. ( $link->getClass() ? ' ' . $link->getClass() : '' )); ?>
				<?php if ($link->get('target')): $attribs['target'] = $link->get('target'); endif; ?>
				<?php  echo $this->htmlLink($link->getHref(), ''.$this->translate($link->getLabel()),$attribs);?>
			<?php endforeach; ?>   
      <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
        <a href="#" data-rel="back" class="ui-btn-default ui-btn-main">
          <?php echo $this->translate('Cancel'); ?>
        </a>
      <?php endif?>
		</div>
	</div>
<?php else: ?>
	<?php foreach ($this->container as $link): ?>
  <?php  $attribs = array_diff_key(array_filter($link->toArray()), array_flip(array(
                  'reset_params', 'route', 'module', 'controller', 'action', 'type',
                  'visible', 'label', 'href'
              )));
      if (!isset($attribs['active'])) {
        $attribs['active'] = false;
      }
  ?>
  <?php $attribs['class'] = $attribs['active'] ? $attribs['class'] . ' ui-btn-active ui-state-persist' : $attribs['class'] ?>

  <?php  if($p['module'] . '_' .  $p['controller'] . '_' . $p['action'] != 'blog_index_view' && $p['module'] . '_' .  $p['controller'] . '_' . $p['action'] != 'sitepage_index_view' && $p['module'] . '_' .  $p['controller'] . '_' . $p['action'] != 'sitebusiness_index_view' && $p['module'] . '_' .  $p['controller'] . '_' . $p['action'] != 'sitegroup_index_view' && $p['module'] . '_' .  $p['controller'] . '_' . $p['action']  != 'sitelike_index_memberlike' && $p['module'] . '_' .  $p['controller'] . '_' . $p['action'] != 'sitefaq_index_view'): ?>
		<a href="<?php echo $link->getHref();?>" class="<?php echo $attribs['class']?>" data-role="button" data-iconpos="notext"  data-icon="<?php echo ($p['module']=='messages'||$p['module'] . '_' .  $p['controller'] . '_' . $p['action'] == 'user_profile_index')?'pencil':'plus' ?>"  data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="<?php echo $link->getLabel();?>" ></a>
  <?php else:?>
  <?php if($p['module'] . '_' .  $p['controller'] . '_' . $p['action'] == 'blog_index_view') : ?>
		<a href="<?php echo $link->getHref();?>" class="<?php echo $attribs['class']?>" data-role="button" data-iconpos="notext" data-icon="icon-book" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="<?php echo $link->getLabel();?>"></a>
  <?php elseif($p['module'] . '_' .  $p['controller'] . '_' . $p['action'] == 'sitelike_index_memberlike') : ?>
		<a href="<?php echo $link->getHref();?>" class="<?php echo $attribs['class']?>" data-role="button" data-iconpos="notext" data-icon="envelope" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="<?php echo $link->getLabel();?>"></a>
  <?php elseif($p['module'] . '_' .  $p['controller'] . '_' . $p['action'] == 'sitefaq_index_view') :?>
		<a href="<?php echo $link->getHref();?>" class="<?php echo $attribs['class']?>" data-role="button" data-iconpos="notext" data-icon="share" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="<?php echo $link->getLabel();?>"></a>
    <?php else:?>
		<a href="<?php echo $link->getHref();?>" class="<?php echo $attribs['class']?>" data-role="button" data-iconpos="notext" data-icon="comment-alt" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" title="<?php echo $link->getLabel();?>"></a>
    <?php endif;?>
  <?php endif;?>
	<?php endforeach; ?>
<?php endif; ?>
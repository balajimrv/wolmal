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

<?php if ($this->buttonType == 'notext'):$var_iconpos = "notext";
  $data_icon = "arrow-l" ?>
<?php elseif ($this->buttonType == 'text'):$var_iconpos = "left";
  $data_icon = "false" ?>
<?php elseif ($this->buttonType == 'both'):$var_iconpos = "false";
  $data_icon = "arrow-l" ?>
<?php endif; ?>

<a href="javascript:void(0);" data-role="button" data-icon="<?php echo $data_icon; ?>" data-iconpos="<?php echo $var_iconpos; ?>"  data-rel="back" data-mini="true" data-inline="true"><?php echo $this->translate('SM_GO_BACK_BUTTON'); ?></a>

<!--"javascript:void(0);"-->
<!--<a  <?php //echo $this->dataHtmlAttribs("go_back_button", array('data-role' => "button", 'data-rel' => "back", 'data-corners' => "true", 'data-shadow' => "true", 'data-iconshadow' => "true",'data-theme'=>"b","data-icon"=>"chevron-left"));  ?>  > <?php //echo $this->translate('Go Back')  ?></a>-->
<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _share.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $href = $this->href; 
      $action = $this->action;
      $isShareEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.socialshare','1');
      $AdvShare = $this->AdvShare;
      if(!$isShareEnable)
        return;
?>
<div class="sesadvcmt_hoverbox"> 
 <span> 
    <span class="sesadvcmt_hoverbox_btn" onClick="openSmoothBoxInUrl('<?php echo !empty($AdvShare) ? $AdvShare : $href; ?>');">
      <div class="sesadvcmt_hoverbox_btn_icon"> <i class="like" style="background-image:url(application/modules/Sesadvancedcomment/externals/images/share.png)"></i> </div>
    </span>
    <div class="text">
      <div><?php echo $this->translate("Share on %s", $_SERVER['HTTP_HOST']); ?></div>
    </div>
  </span> 
  <span> 
    <span class="sesadvcmt_hoverbox_btn"  onClick="socialSharingPopUp('<?php echo Engine_Api::_()->sesbasic()->facebookShareUrl($href,$action); ?>','Facebook');">
      <div class="sesadvcmt_hoverbox_btn_icon"> <i class="love" style="background-image:url(application/modules/Sesadvancedcomment/externals/images/facebook.png); "></i> </div>
    </span>
    <div class="text">
      <div><?php echo $this->translate("Facebook"); ?></div>
    </div>
  </span> 
  <span> 
    <span class="sesadvcmt_hoverbox_btn"  onClick="socialSharingPopUp('<?php echo Engine_Api::_()->sesbasic()->twitterShareUrl($href,$action); ?>','Twitter');">
      <div class="sesadvcmt_hoverbox_btn_icon"> <i class="anger" style="background-image:url(application/modules/Sesadvancedcomment/externals/images/twitter.png); "></i> </div>
    </span>
    <div class="text">
      <div><?php echo $this->translate("Twitter"); ?></div>
    </div>
  </span>
  <span> 
    <span class="sesadvcmt_hoverbox_btn"  onClick="socialSharingPopUp('<?php echo Engine_Api::_()->sesbasic()->googlePlusShareUrl($href,$action); ?>','Google Plus');">
      <div class="sesadvcmt_hoverbox_btn_icon"><i class="haha" style="background-image:url(application/modules/Sesadvancedcomment/externals/images/google-plus.png); "></i> </div>
    </span>
    <div class="text">
      <div><?php echo $this->translate("Google Plus"); ?></div>
    </div>
  </span> 
  <span> 
    <span class="sesadvcmt_hoverbox_btn"  onClick="socialSharingPopUp('<?php echo Engine_Api::_()->sesbasic()->LinkedinShareUrl($href,$action); ?>','Linkedin');">
      <div class="sesadvcmt_hoverbox_btn_icon"><i class="wow" style="background-image:url(application/modules/Sesadvancedcomment/externals/images/linkedin.png); "></i> </div>
    </span>
    <div class="text">
      <div><?php echo $this->translate("Linkedin"); ?></div>
    </div>
  </span>  
 </div>

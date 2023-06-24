<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class='ui-page-content' id='browsemembers_results'>
  <?php echo $this->render('_browseFriends.tpl') ?>
</div>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php //show confirmation popup ?>
<!--<div data-role="popup" id="popupDialog-RemoveFriend" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
    <div data-role="header" data-theme="a" class="ui-corner-top">
      <h1><?php echo $this->translate('Remove Friend?'); ?></h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
      <h3 class="ui-title"></h3>
      <p><?php echo $this->translate('Are you sure you want to remove this member as a friend?'); ?></p>              

      <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.activity.addFriend()"><?php echo $this->translate("Delete"); ?></a>
      <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c"><?php echo $this->translate("Cancel"); ?></a>
    </div>
</div>-->

<script type="text/javascript">
  var url = '<?php echo $this->url() ?>';
  var requestActive = false;
  var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams, smFriendAction;

  sm4.core.runonce.add(function() {

    $(window).bind('onChangeFields', function() {
      var firstSep = $('li.browse-separator-wrapper');
      var lastSep;
      var nextEl = firstSep;

      var allHidden = true;
      do {
        nextEl = nextEl.next();
        if( nextEl.attr('class') == 'browse-separator-wrapper' ) {
          lastSep = nextEl;
          nextEl = false;
        } else {
          allHidden = allHidden && ( nextEl.css('display') == 'none' );
        }
      } while( nextEl );
      if( lastSep ) {
        lastSep.css('display', (allHidden ? 'none' : ''));
      }
    });

  });
</script>
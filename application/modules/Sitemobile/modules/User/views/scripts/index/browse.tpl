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
  <?php echo $this->render('_browseUsers.tpl') ?>
</div>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
))
?>

<script type="text/javascript">
  var url = '<?php echo $this->url() ?>';
  var requestActive = false;
  var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams;

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
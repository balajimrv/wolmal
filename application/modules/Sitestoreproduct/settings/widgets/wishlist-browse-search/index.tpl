<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if($this->viewType == 'horizontal'): ?>
  <div class="sr_sitestoreproduct_search_criteria sr_sitestoreproduct_wishlist_browse_search">
    <?php echo $this->form->setAttrib('class', 'sr_sitestoreproduct_item_filters')->render($this) ?>
  </div>
<?php else: ?>
  <div class="sr_sitestoreproduct_search_criteria">
    <?php echo $this->form->render($this) ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  showMemberNameSearch();
  function showMemberNameSearch() {
    if($('search_wishlist')) {
      $('text-wrapper').setStyle('display', ($('search_wishlist').get('value') == '' ?'block':'none'));
      $('text').value = $('search_wishlist').get('value') == '' ? $('text').value : '';
    }
  }
</script>  

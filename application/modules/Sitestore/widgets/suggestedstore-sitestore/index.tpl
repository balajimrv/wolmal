<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<ul class="sitestore_sidebar_list jq-sitestore_tooltip">
  <?php foreach ($this->suggestedsitestore as $sitestore): ?>
    <li>
      <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.icon')) ?>

      <div class="suggestsitestore_tooltip" style="display:none;">
        <div class="suggestsitestore_tooltip_content_outer">
          <div class="suggestsitestore_tooltip_content_inner">
            <div class="suggestsitestore_tooltip_arrow">
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/tooltip_arrow.png' alt="" />
            </div>
            <div class='suggestsitestores_tooltip_info'>
              <div class="title">
                <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $sitestore->getTitle()) ?>
                <span>
                  <?php if ($sitestore->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                </span>
                <span>
                  <?php if ($sitestore->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                </span>
              </div>
              <?php if ($this->ratingShow): ?>
                <?php if (($sitestore->rating > 0)): ?>

                  <?php
                  $currentRatingValue = $sitestore->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>

                  <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              <?php endif; ?>
              
							<div class='suggestsitestores_tooltip_info_date clr'>
								<?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> 
								<?php if($postedBy):?>
									- <?php echo $this->translate('posted by'); ?>
									<?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>
								<?php endif;?>
							</div>
              <div class='suggestsitestores_tooltip_info_date'>
                <?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>, 

								<?php $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview'); ?>
								<?php if ($sitestorereviewEnabled): ?>
									<?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)) ?>,
								<?php endif; ?>

                <?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>, 
                <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='sitestore_sidebar_list_info'>
        <div class='sitestore_sidebar_list_title'>
          <?php $sitestore_title = strip_tags($sitestore->title);
          $sitestore_title = Engine_String::strlen($sitestore_title) > 40 ? Engine_String::substr($sitestore_title, 0, 40) . '..' : $sitestore_title; ?>   
          <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $sitestore_title) ?>
        </div>
      </div>

    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">
  /* moo style */
  window.addEvent('domready',function() {
    //opacity / display fix
    $$('.suggestsitestore_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitestore_tooltip li').each(function(el,i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });

  });
</script>
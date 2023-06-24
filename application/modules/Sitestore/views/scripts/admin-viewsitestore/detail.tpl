<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitestore_admin_popup"> 
  <div>
    <h3><?php echo $this->translate('Store Details'); ?></h3>
    <br />
    <table cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" width="350">
            <tr>
              <td width="120"><b><?php echo $this->translate('Title:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->item('sitestore_store', $this->sitestoreDetail->store_id)->getHref(), $this->translate($this->sitestoreDetail->title), array('target' => '_blank')) ?>&nbsp;&nbsp;
              </td>
            <tr>
              <td><b><?php echo $this->translate('Owner:'); ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->sitestoreDetail->getOwner()->getHref(), $this->sitestoreDetail->getOwner()->getTitle(), array('target' => '_blank')) ?>
              </td>
            </tr>

            <?php if ($this->manageAdminEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Admins:'); ?></b></td>
                <td><?php echo $this->admin_total; ?></td>
              </tr>
            <?php endif; ?>

            <?php //if ($this->sitepageDetail->member_count): ?>
            <?php $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember'); ?>
            <?php if ($sitestorememberEnabled): ?>
              <tr>
                <td><b><?php echo $this->translate('Total Members:'); ?></b></td>
                <td><?php echo $this->sitestoreDetail->member_count; ?></td>
              </tr>
            <?php endif; ?>
            <?php //endif; ?>

            <tr>
              <?php if ($this->category_name != '') : ?>
                <td><b><?php echo $this->translate('Category:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>	
            <tr>
              <?php if ($this->subcategory_name != '') : ?>
                <td><b><?php echo $this->translate('Subcategory:'); ?></b></td> 
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name), array('target' => '_blank')) ?>
                </td>	    
              <?php endif; ?>
            </tr>
            <tr>
              <?php if ($this->subsubcategory_name != '') : ?>
                <td><b><?php echo $this->translate('3%s Level Category:', "<sup>rd</sup>"); ?></b></td>
                <td>
                  <?php echo $this->htmlLink($this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subsubcategory'), $this->translate($this->subsubcategory_name), array('target' => '_blank')) ?>
                </td>
              <?php endif; ?>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Creation Date:'); ?></b></td>
              <td>
                <?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($this->sitestoreDetail->creation_date))); ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved:'); ?></b></td>
              <td>
                <?php
                if ($this->sitestoreDetail->approved)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");
                ?>
              </td>
            </tr>
            <tr>
              <td><b><?php echo $this->translate('Approved Date:'); ?></b></td>
              <td>
                <?php if (!empty($this->sitestoreDetail->aprrove_date)): ?>
                  <?php echo $this->translate(date('M d,Y g:i A', strtotime($this->sitestoreDetail->aprrove_date))); ?>
                <?php else: ?>
                  <?php echo $this->translate('-'); ?>
                <?php endif; ?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Featured:'); ?></b></td>
              <td> <?php
                if ($this->sitestoreDetail->featured)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Sponsored:'); ?></b></td>
              <td> <?php
                if ($this->sitestoreDetail->sponsored)
                  echo $this->translate('Yes');
                else
                  echo $this->translate("No");?>
              </td>
            </tr>
            <?php $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
            <?php if ($this->sitestoreDetail->price && $enablePrice): ?>
              <tr>
                <td><b><?php echo $this->translate('Price:'); ?></b></td>
                <td><?php echo $this->locale()->toCurrency($this->sitestoreDetail->price,Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'));?></td>
              </tr>
            <?php endif; ?>
            <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
            <?php if ($this->sitestoreDetail->location && $enableLocation): ?>
              <tr>
                <td><b><?php echo $this->translate('Location:'); ?></b></td>
                <td><?php echo $this->sitestoreDetail->location ?></td>
              </tr>
            <?php endif; ?>

            <tr>
              <td><b><?php echo $this->translate('Views:'); ?></b></td>
              <td><?php echo $this->translate($this->sitestoreDetail->view_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Comments:'); ?></b></td>
              <td><?php echo $this->translate($this->sitestoreDetail->comment_count); ?> </td>
            </tr>

            <tr>
              <td><b><?php echo $this->translate('Likes:'); ?></b></td>
              <td><?php echo $this->translate($this->sitestoreDetail->like_count); ?> </td>
            </tr>

            <?php if ($this->isEnabledSitestorereview && $this->sitestoreDetail->rating > 0): ?>

            <tr>
              <td><b><?php echo $this->translate('Reviews:'); ?></b></td>
              <td><?php echo $this->translate($this->sitestoreDetail->review_count); ?> </td>
            </tr>

              <?php
              $currentRatingValue = $this->sitestoreDetail->rating;
              $difference = $currentRatingValue - (int) $currentRatingValue;
              if ($difference < .5) {
                $finalRatingValue = (int) $currentRatingValue;
              } else {
                $finalRatingValue = (int) $currentRatingValue + .5;
              }
              ?>

              <tr>           
                <td><b><?php echo $this->translate('Rating:'); ?></b></td>
                <td> <?php for ($x = 1; $x <= $this->sitestoreDetail->rating; $x++): ?>
                    <span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>"></span>
                  <?php endfor; ?>
                  <?php if ((round($this->sitestoreDetail->rating) - $this->sitestoreDetail->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half" title="<?php echo $this->sitestoreDetail->rating . $this->translate(' rating'); ?>"></span>
                  <?php endif; ?>
                  <?php if (empty($this->sitestoreDetail->rating))
                  echo $this->translate("-"); ?>
                </td>
              </tr>
              <?php endif; ?>
          </table>
        </td>
        <td align="right">
           <?php echo $this->htmlLink($this->sitestoreDetail->getHref(), $this->itemPhoto($this->sitestoreDetail, 'thumb.icon', '', array('align' => 'right')), array('target' => '_blank')) ?>
        </td>	
      </tr>
    </table>		
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
</div>	

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
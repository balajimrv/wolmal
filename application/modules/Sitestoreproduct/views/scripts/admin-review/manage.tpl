<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-review/_navigationAdmin.tpl'; ?>

<h3><?php echo $this->translate('Manage Reviews'); ?></h3>
<p>
  <?php echo $this->translate('This page lists all the reviews posted by members, editors and visitors of your site. From "Review Settings" sub-section, you can choose if visitors should be able to review products. Here, you can monitor reviews, delete them if necessary and you can also approve visitor reviews by clicking on "Take Action" link along with the visitor reviews. Entering criteria into the filter fields will help you find specific review entries. Leaving the filter fields blank will show all the review entries on your social network. Here, you can also make reviews featured / un-featured by clicking on the corresponding icons.'); ?>
</p>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected Product reviews ?")) ?>');
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<br />

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
        <label>
          <?php echo $this->translate("Review Title") ?>
        </label>
        <input type="text" name="review_title" value="<?php echo $this->review_title; ?>"/>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Type") ?>
        </label>
        <select id="" name="review_type">
          <option value="" ></option>
          <option value="user" <?php if ($this->review_type == 'user')
            echo "selected"; ?> ><?php echo $this->translate("User") ?></option>
          <option value="visitor" <?php if ($this->review_type == 'visitor')
            echo "selected"; ?> ><?php echo $this->translate("Visitor") ?></option>
          <option value="editor" <?php if ($this->review_type == 'editor')
            echo "selected"; ?> ><?php echo $this->translate("Editor") ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Name") ?>
        </label>
        <input type="text" name="name" value="<?php echo $this->name; ?>"/>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Email") ?>
        </label>
        <input type="text" name="email" value="<?php echo $this->email; ?>"/>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Product Title") ?>
        </label>
        <input type="text" name="product_title" value="<?php echo $this->product_title; ?>"/>
      </div>   

      <div class="buttons">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />
<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $reviewHelpful = Engine_Api::_()->getDbtable('helpful', 'sitestoreproduct'); ?>
<?php if (count($this->paginator)): ?>
  <div class='admin_members_results'>
    <div>
  <?php echo $this->translate(array('%s review found.', '%s reviews found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
    </div>
  </div>
  <br />
  <div class="admin_table_form">
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
	    <table class='admin_table seaocore_admin_table'>
	      <thead>
	        <tr>
	          <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
	          <th style='width: 1%;' align="center" title="<?php echo $this->translate('ID'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Review Title'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Review Title'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Type'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('type', 'ASC');"><?php echo $this->translate('Type'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Reviewer'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Reviewer'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Email'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate('Email'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Product Title'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('product_title', 'ASC');"><?php echo $this->translate('Product Title'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Overall Rating'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_rating', 'ASC');"><?php echo $this->translate('Overall Rating'); ?></a></th>
						<th style='width: 1%;' class='admin_table_centered' title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>
	          <th align="center" title="<?php echo $this->translate('Helpful (%)'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('helpful_count', 'ASC');"><?php echo $this->translate('Helpful (%)'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Date'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Date'); ?></a></th>
	          <th align="center" title="<?php echo $this->translate('Status'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'DESC');"><?php echo $this->translate('Status'); ?></a></th>
	          <th class='admin_table_options' align="left" title="<?php echo $this->translate('Options'); ?>"><?php echo $this->translate('Options'); ?></th>
	        </tr>
	      </thead>
	      <tbody>
	          <?php if (count($this->paginator)): ?>
	            <?php foreach ($this->paginator as $item): ?>
	              <?php $editor_content_id = Engine_Api::_()->sitestoreproduct()->existWidget('editor_reviews_sitestoreproduct', 0); ?>
	            <tr>
	              <td><input name='delete_<?php echo $item->review_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->review_id ?>"/></td>
	              <td class="admin_table_centered"><?php echo $item->review_id ?></td>
	                <?php if ($item->type == 'user' || $item->type == 'visitor'): ?>
	                <td class='admin_table_bold'><?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 13), array('title' => $item->title, 'target' => '_blank')) ?></td>
	              <?php elseif ($item->type == 'editor'): ?>
	                <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitestoreproduct_product', $item->resource_id)->getHref() . '/tab/' . $editor_content_id, Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 10), array('title' => $item->title, 'target' => '_blank')) ?>  </td>
	              <?php endif; ?>
	              <td class='admin_table_bold' title="<?php echo ucfirst($item->type); ?>">
	              <?php echo ucfirst($item->type); ?>
	              </td>

	              <td>
                  <?php if (empty($item->owner_id)): ?>
	                  <span title="<?php echo $item->anonymous_name . ' - ' . $this->translate('Anonymous') ?>"><font color="red"><?php echo $item->anonymous_name ?></font></span>
	                <?php else: ?>
	                  <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank', 'title' => $item->getOwner()->getTitle())) ?>
	                <?php endif; ?>
	              </td>
	
	              <td>
                  <?php if (empty($item->owner_id)): ?>
	                  <span><a href='mailto:<?php echo $item->anonymous_email ?>' title="<?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->anonymous_email, 17)?>" ><?php echo $item->anonymous_email ?></a></span>

                  <?php else: ?>
	
                    <span><a href='mailto:<?php echo $item->email ?>' title="<?php echo $item->email ?>" ><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->email, 17) ?></a></span>
                  <?php endif; ?>

	              </td>
	
	              <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitestoreproduct_product', $item->resource_id)->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->product_title, 10), array('title' => $item->product_title, 'target' => '_blank')) ?></td>
	
                <?php if ($item->type == 'user') : ?>
                  <td>
                    <div>
                      <span title="<?php echo $item->review_rating . $this->translate(' rating '); ?>">
                        <?php echo $this->showRatingStarSitestoreproduct($item->review_rating, 'user', 'big-star'); ?>
                      </span>
                    </div>
                  </td>
                <?php elseif($item->type == 'visitor'):?>
	
	                <td>
	                  <div>
	                    <span title="<?php echo $item->review_rating . $this->translate(' rating '); ?>">
                        <?php echo $this->showRatingStarSitestoreproduct($item->review_rating, 'user', 'big-star'); ?>
	                    </span>
	                  </div>
	                </td>
	
                <?php else: ?>
	                <td>
	                  <div>
	                    <span title="<?php echo $item->review_rating . $this->translate('rating '); ?>">
	                <?php echo $this->showRatingStarSitestoreproduct($item->review_rating, 'editor', 'big-star'); ?>
	                    </span>
	                  </div>
	                </td>
	              <?php endif; ?>
                  
                <?php if($item->featured == 1):?>
                  <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'featured', 'review_id' => $item->review_id, 'resource_id' => $item->resource_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('title'=> $this->translate('Make Un-featured')))) ?> 
                  </td>       
                <?php else: ?>  
                  <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'featured', 'review_id' => $item->review_id, 'resource_id' => $item->resource_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.png', '', array('title'=> $this->translate('Make Featured')))) ?>
                  </td>
                <?php endif; ?>
          
                <?php if ($item->helpful_count > -1): ?>
                  <?php $totalHelpsData = $reviewHelpful->countHelpfulPercentage($item->review_id, 0); ?>
	                <td align="center" class="admin_table_centered"><span title="<?php echo $this->translate("%1s out of %2s marked as helpful.", $totalHelpsData['total_yes'], $totalHelpsData['total_marks']) ?>"><?php echo $item->helpful_count ?></span></td>
	              <?php else: ?>
	                <td align="center" class="admin_table_centered"><span title="<?php echo $this->translate('No member marked this PRODUCT as helpful !'); ?>">---</span></td>
	              <?php endif; ?>
	
                <td title="<?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($item->creation_date))) ?>"><?php echo $this->translate(gmdate('M d,Y', strtotime($item->creation_date))) ?></td>
                
	              <td class="admin_table_centered" align="middle">					
	                <?php if ($item->status == 0 && $item->type == 'visitor') : ?>
                    <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'take-action', 'review_id' => $item->review_id), "admin_default", true);?>"><span title="<?php echo $this->translate("Approval Pending"); ?>"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/pending.png"/></span></a>
	                <?php elseif ($item->status == 0 && $item->type == 'editor') : ?>
	                  <span title="<?php echo $this->translate("Draft"); ?>"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/draft.png"/></span>
	                <?php elseif ($item->status == 1) : ?>
	                  <span title="<?php echo $this->translate("Published"); ?>"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/approved.gif"/></span>
	                <?php else: ?>
	                  <span title="<?php echo $this->translate("Dis-Approved"); ?>"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/disapproved.gif"/></span>
	                <?php endif; ?>
	              </td>
	
	              <td class='admin_table_options' align="left">
	                <?php echo $this->htmlLink($item->getHref(), $this->translate('View'), array('target' => '_blank')) ?>	 |					
	                <?php if (empty($item->owner_id) && ($item->status != '1' && $item->status != '2' )): ?> 
	                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'take-action', 'review_id' => $item->review_id), $this->translate('Take Action'), array('class' => 'smoothbox',)) ?>
	                  |
	                <?php endif; ?>
	               <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'review', 'action' => 'delete', 'review_id' => $item->review_id), $this->translate('Delete'), array('class' => 'smoothbox')) ?> 
	              </td>
	            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
	      </tbody>
	    </table> 
      <br />
      <?php echo $this->paginationControl($this->paginator); ?>
      <div class='buttons'>
        <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No results were found.'); ?>
    </span>
  </div>
<?php endif; ?>

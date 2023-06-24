<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pagination.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->storeCount > 1): ?>
<div class="seaocore_pagination">
	<div class="stores">
	  <ul class="paginationControl">
	    <?php /* Previous store link */ ?>
	    <?php if (isset($this->previous)): ?>
	    	<li>
	      	<a href="javascript:void(0)" onclick="javascript:storeAction('<?php echo $this->previous; ?>')"><?php echo $this->translate("&#171; Previous") ?></a>
	      </li>	
	    <?php endif; ?>
	
	    <?php foreach ($this->storesInRange as $store): ?>
	      <?php if ($store != $this->current): ?>
	      	<li>
	        	<a href="javascript:void(0)" onclick="javascript:storeAction('<?php echo $store; ?>')"><?php echo $store; ?></a>
	        </li>
	      <?php else: ?>
	        <li class="selected">
	        	<a href="javascript:void(0)"><?php echo $store; ?></a>
	        </li>	
	      <?php endif; ?>
	    <?php endforeach; ?>
	
	    <?php /* Next store link */ ?>
	    <?php if (isset($this->next)): ?>
	    	<li>
	      	<a href="javascript:void(0)" onclick="javascript:storeAction('<?php echo $this->next; ?>')"><?php echo $this->translate("Next &#187;") ?></a>
	      </li>
	    <?php endif; ?>
	
	  </div>
<?php endif; ?>
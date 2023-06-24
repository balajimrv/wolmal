<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: publish.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      parent.$('list-item-<?php echo $this->product_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your list has been published.'); ?> 
    </div>
  <?php else: ?>
	  <form method="POST" action="<?php echo $this->url() ?>">
	    <div>
	      <h3><?php echo $this->translate("Publish Product?"); ?></h3>
	      <p>
	        <?php echo $this->translate("Are you sure that you want to publish this Product?"); ?>
	      </p>
	      <p>&nbsp;
	      </p>
	      <p>
	        <input type="hidden" name="product_id" value="<?php echo $this->product_id?>"/>
					<input type="hidden" value="" name="search"><input type="checkbox" checked="checked" value="1" id="search" name="search">
					<?php echo $this->translate("Enable Product"); ?>
					<br />
					<br />
	        <button type='submit'><?php echo $this->translate('Publish'); ?></button>
	        <?php echo $this->translate(' or ')?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel')?></a>
	      </p>
	    </div>
	  </form>
  <?php endif; ?>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  	TB_close();
	</script>
<?php endif; ?>
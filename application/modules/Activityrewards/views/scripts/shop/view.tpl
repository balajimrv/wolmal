
<?php if (!empty($this->error_message)): ?>
  <table cellpadding=0 cellpadding=0 style="margin: 0px auto">
  <tr><td>
	<ul class="form-errors">
	  <li> <?php echo $this->translate($this->error_message) ?> </li>
	</ul>
  </td>
  </tr>
  </table>
<?php endif; ?>


  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td valign='top' style='padding-right: 10px; text-align: center;' width='1'>

    <?php echo $this->itemPhoto($this->item, 'thumb.profile') ?>

  </td>

    <td valign='top'>
	<div style='padding: 5px; background: #EEEEEE; font-weight: bold'>
	  <a href="<?php echo $this->url(array(),'activityrewards_spend') ?> "> <?php echo $this->translate("Bridges shop") ?> </a> &raquo; <?php echo $this->item->getTitle() ?>
	</div>
	<div style='padding: 5px; vertical-align: top;'>
  
	<?php if ($this->item->userpointspender_cost > 0): ?>
	<div style="float: right">
      
      <div style="background-color: #F6F6F6; margin-bottom: 5px; padding: 10px; margin-right: -5px; border: 1px solid #EEE">
        
        <div>
          <img style="float: left; margin-right: 8px" src="application/modules/Activitypoints/externals/images/userpoints_coins32.png">
          <span style="font-size: 24px; color: #777">
            <span style="font-weight: bold"><?php echo $this->item->userpointspender_cost ?></span>&nbsp;<?php echo $this->translate('100016708') ?></span>
          </span>
        </div>

        <div style="margin: 10px auto 5px; width: 100px">
          <form action="<?php echo $this->url(array('item_id' => $this->item->getIdentity(), 'item_title' => '-'), 'activityrewards_spend_view') ?>" method="post">  
          <input id="activitypoints_act_now" type="submit" id="submit_button" name="submit_button" value="<?php if(!empty($this->item->userpointspender_actiontext)): ?><?php echo $this->translate($this->item->userpointspender_actiontext) ?><?php else: ?><?php echo $this->translate('100016660') ?><?php endif; ?>" />
          <input type='hidden' name='item_id' value='<?php echo $this->item->getIdentity() ?>'>
          <input type='hidden' name='task' value='dobuy'>
          </form>
        </div>

      </div>
	  
  
	</div>
  <?php endif; ?>
  
	<div style='padding-top: 10px;'>
      <?php echo $this->item->userpointspender_body ?>
      
	  <br><br>
	
	</div>
	</div>
  </td>
  </tr>
  </table>
  
	
  <br>
  


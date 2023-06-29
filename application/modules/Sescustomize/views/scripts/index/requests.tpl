<?php if( count($this->navigation) ): ?>
<div class="headline">
  <h2>
	<?php echo $this->translate('Payment Requests');?>
  </h2>
  <div class="tabs">
	<?php
	  // Render the menu
	  echo $this->navigation()
		->menu()
		->setContainer($this->navigation)
		->render();
	?>
  </div>
</div>
<?php endif; ?>

 <?php if( count($this->paginator) ): ?>
      
      <form method="post" >
        <div class="clear" style="overflow: auto;"> 
        <table class='activitypoints_transactions_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Id"); ?></th>
              <th title="Total Amount"><?php echo $this->translate("Total Amount") ?></th>
              <th title="Requested Amount"><?php echo $this->translate("Req.Amount") ?></th>
              <th title="Requested Date"><?php echo $this->translate("Req.Date") ?></th>
              <th><?php echo $this->translate("Status") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
          </thead>
          <tbody>
            <?php 
              foreach ($this->paginator as $item): ?>
              <?php  $user = Engine_Api::_()->getItem('user', $item->user_id); 
         				if(!$user)
                	continue;
         			?>
            <tr>
              <td><?php echo $item->reedemrequest_id; ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->currentFb($item->user_id)); ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->amount); ?></td>
              <td><?php echo $item->creation_date; ?></td> 
              <td><?php echo $item->status == 1 ? 'Approved' : ($item->status == 0 ? "Processing" : "Rejected" ); ?></td> 
              
              <td>
                  <?php echo $this->htmlLink($this->url(array( 'id' => $item->reedemrequest_id),'sescustomize_bridgetra',true), $this->translate("Details"), array('class' => 'smoothbox')); ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </form>
      <br/>
      <div>
        <?php echo $this->paginationControl($this->paginator); ?>
      </div>
    <?php else:?>
      <div class="tip">
        <span>
          <?php echo $this->translate("There are no payment made yet.") ?>
        </span>
      </div>
    <?php endif; ?>
    </div>
  </div>
</div>
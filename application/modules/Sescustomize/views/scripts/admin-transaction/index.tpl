<div class='sesbasic-form sesbasic-categories-form'>
  <div>
    <?php if( count($this->navigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render();?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
    <?php if( count($this->subnavigation) ): ?>
      <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();?>
      </div>
    <?php endif; ?>
    
		
    <br />
    <?php if( count($this->paginator) ): ?>
      
      <form method="post" >
        <div class="clear" style="overflow: auto;"> 
        <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Id"); ?></th>
              <th><?php echo $this->translate("User"); ?></th>
              <th title="Total Amount"><?php echo $this->translate("Total Amount") ?></th>
              <th title="Requested Amount"><?php echo $this->translate("Req.Amount") ?></th>
              <th title="Requested Date"><?php echo $this->translate("Req.Date") ?></th>
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
              <td><?php echo $this->htmlLink($user->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($user->getTitle(),16)), array('title' => $user->getTitle(), 'target' => '_blank')); ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(Engine_Api::_()->getDbtable('ebvalues', 'sescustomize')->currentEb($item->user_id)); ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->amount); ?></td>
              <td><?php echo $item->creation_date; ?></td> 
              <td>
                <?php echo $this->htmlLink($this->url(array('module' => 'sescustomize', 'controller' => 'transaction','type'=>'1','id'=>$item->reedemrequest_id,'action'=>'status')), $this->translate("Approve"), array('class' => 'smoothbox')); ?> |
                <?php echo $this->htmlLink($this->url(array('module' => 'sescustomize', 'controller' => 'transaction','type' => '2', 'id' => $item->reedemrequest_id,'action'=>'status')), $this->translate("Reject"), array('class' => 'smoothbox')); ?> |
                    <?php echo $this->htmlLink($this->url(array('action' => 'detail-payment', 'id' => $item->reedemrequest_id,'controller' => 'transaction')), $this->translate("Details"), array('class' => 'smoothbox')); ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </form>
      <br/>
      <div>
        <?php //echo $this->paginationControl($this->paginator); ?>
      </div>
    <?php else:?>
      <div class="tip">
        <span>
          <?php echo $this->translate("There are no payment requests.") ?>
        </span>
      </div>
    <?php endif; ?>
    </div>
  </div>
</div>
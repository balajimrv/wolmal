<script type="text/javascript">
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }

  function enable_offer(offer_id, enable) {
    var asyncform = document.getElementById('asyncform');
    document.getElementById('asyncform_task').value = "enable";
    document.getElementById('asyncform_offer_id').value = offer_id;
    document.getElementById('asyncform_enable').value = enable;
    
    asyncform.submit();
  }
  
  function delete_offer(offer_id) {
    var asyncform = document.getElementById('asyncform');
    document.getElementById('asyncform_task').value = "delete";
    document.getElementById('asyncform_offer_id').value = offer_id;
    
    asyncform.submit();
  }
  
  function show_upselector() {
    document.getElementById("upselector_button").style['display'] = "none";
    document.getElementById("upselector_div").style['display'] = "block";
  }


</script>

<h2><?php echo $this->translate("Activity Points Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("100016167")
  ?>
</p>

<br />
<?php if( count($this->paginator) ): ?>

<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
      <th><?php echo $this->translate("100016168") ?></th>
      <th><?php echo $this->translate("100016169") ?></th>
      <th><?php echo $this->translate("Points") ?></th>
      <th><?php echo $this->translate("100016197") ?></th>
      <th><?php echo $this->translate("100016172") ?></th>
      <th><?php echo $this->translate("100016193") ?></th>
      <th><?php echo $this->translate("100016178") ?></th>
      <th><?php echo $this->translate("100016194") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' value="<?php echo $item->userpointspender_id ?>"/></td>
        <td><?php echo Engine_String::strlen($item->userpointspender_title) > 50 ? Engine_String::substr($item->userpointspender_title, 0, 50) . '...' : $item->userpointspender_title; ?></td>
        <td><?php echo $item->userpointspendertype_typename ?></td>
        <td><?php echo $item->userpointspender_cost ?></td>
        <td><?php echo $item->userpointspender_engagements ?></td>
        <td><?php echo $this->locale()->toNumber($item->userpointspender_views) ?></td>
        <td><?php if($item->userpointspender_enabled): ?><?php echo $this->translate('Yes'); ?><?php else : ?><?php echo $this->translate('No'); ?><?php endif; ?></td>
        <td><?php echo $this->timestamp($item->userpointspender_date) ?></td>
        
        <td nowrap='nowrap'>
          <a href='<?php echo $this->url(array('action' => 'edit', 'item_id' => $item->userpointspender_id));?>'><?php echo $this->translate('100016176'); ?></a> | <a href='javascript:enable_offer(<?php echo $item->userpointspender_id ?>, <?php echo $item->userpointspender_enabled ? '0' : '1' ?>)'>
          <?php if(!$item->userpointspender_enabled): ?><?php echo $this->translate('100016187'); ?><?php else: ?><?php echo $this->translate('100016177'); ?><?php endif; ?></a> | <a href='javascript:delete_offer(<?php echo $item->userpointspender_id ?>)'><?php echo $this->translate('100016180'); ?></a>
        </td>

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td>
  <br>
  <button id="upselector_button" type='button' class='button' onclick="show_upselector()"><?php echo $this->translate('100016188') ?></button>

  <div id="upselector_div" style="display:none">
    <form action='<?php echo $this->url(array('action'=>'edit'));?>' method='get' name='items'>
      <?php echo $this->translate('100016196') ?> &nbsp;
      <select class='text' name='offer_type'><option></option>
      <?php foreach($this->offer_types as $offer_type): ?><option value='<?php echo $offer_type['userpointspendertype_id']; ?>'><?php echo $offer_type['userpointspendertype_title'] ?></option><?php endforeach; ?></select>&nbsp;
    <button type='submit' class='button'><?php echo $this->translate('100016830') ?></button>
    <input type='hidden' name='newitem' value='1'>
    </form>
  </div>
  
</td>
</tr>
</table>

<form method=POST id="asyncform" action="<?php echo $this->url(array('action'=>'index'));?>">
  <input type="hidden" id="asyncform_task" name="task">
  <input type="hidden" id="asyncform_offer_id" name="item_id">
  <input type="hidden" id="asyncform_enable" name="enable">
</form>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no offers yet.") ?>
    </span>
  </div>

  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <button id="upselector_button" type='button' class='button' onclick="show_upselector()"><?php echo $this->translate('100016188') ?></button>
  
    <div id="upselector_div" style="display:none">
      <form action='<?php echo $this->url(array('action'=>'edit'));?>' method='get' name='items'>
        <?php echo $this->translate('100016196') ?> &nbsp;
        <select class='text' name='offer_type'><option></option>
        <?php foreach($this->offer_types as $offer_type): ?><option value='<?php echo $offer_type['userpointspendertype_id']; ?>'><?php echo $offer_type['userpointspendertype_title'] ?></option><?php endforeach; ?></select>&nbsp;
      <button type='submit' class='button'><?php echo $this->translate('100016830') ?></button>
      <input type='hidden' name='newitem' value='1'>
      </form>
    </div>
    
  </td>
  </tr>
  </table>


<?php endif; ?>

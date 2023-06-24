<?php

?>
<?php if( count($this->navigation) ): ?>
<div class="headline">
  <h2>
	<?php echo $this->translate('Bridges');?>
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
<div class="actpoints_bridges_page">
  <h3>My Referals</h3>
  <div class="actpoints_bridges_table">
  	<table>
    	<thead>
      	<tr>
          <th width="20%" class="isdata _value">Member Name</th>
          <th class="nodata"></th>
          <th class="isdata _value"><span>BB</span></th>
          <th class="nodata"></th>
          <th class="isdata _value"><span>CB</span></th>
        </tr>
      </thead>
	  <tbody>
    <?php foreach($this->users as $user):?>
      	<tr><td colspan="13" class="blankrow"></td></tr>
      	<tr>
          <td class="isdata _value"><a href="<?php echo $user->getHref();?>"><?php echo $user->displayname;?></a></td>
          <td class="nodata"></td>
          <td class="isdata _value"><span><?php echo Engine_Api::_()->sescustomize()->getUserBridges($user->user_id,'bb');?></span></td>
          <td class="nodata"></td>
          <td class="isdata _bb"><span><?php echo Engine_Api::_()->sescustomize()->getUserBridges($user->user_id,'cb');?></span></td>
        </tr>
     <?php endforeach;?>
      </tbody>
    </table>
  </div>

</div>

<style type="text/css">
.layout_activitypoints_bridges{
	padding:0 !important;
	border-width:0 !important;
}
.actpoints_bridges_page *{
	box-sizing:border-box;
}
.actpoints_bridges_page > h3{
	text-align: center;
	font-size: 25px;
	margin-bottom: 10px;
}
.actpoints_bridges_table table{
	width:100%;
}
.actpoints_bridges_table table th.nodata,
.actpoints_bridges_table table td.nodata{
	width:2px;
}
.actpoints_bridges_table table td.blankrow{
	height:2px;
}
.actpoints_bridges_table table th.isdata,
.actpoints_bridges_table table td.isdata{
	background-color: #f1f1f1;
	text-align: center;
	padding: 5px 0;
	font-weight: bold;
	font-size: 12px;
	white-space:nowrap;
}
.actpoints_bridges_table table td.isdata{
	padding:0;
}
.actpoints_bridges_table table th.year{
	padding:5px;
}
.actpoints_bridges_table table th > span,
.actpoints_bridges_table table td > span{
	display:block;
	font-weight:bold;
	margin:2px 0;
	font-size:11px;
}
.actpoints_bridges_table table th > p span,
.actpoints_bridges_table table td > p span{
	width:50%;
	float:left;
	font-weight:bold;
	font-size:11px;
}
.actpoints_bridges_table table td > p span{
	padding:5px;
}
.actpoints_bridges_table table td > p span + span{
	border-left:1px solid #fff;
}
.actpoints_bridges_table table th._ebRedeemed > p span,
.actpoints_bridges_table table td._ebRedeemed > p span{
	width:33%;
}
.actpoints_bridges_table table th._year{background-color:rgba(136, 148, 178, 0.38);}
.actpoints_bridges_table table th._value{background-color:#dcedf4;}
.actpoints_bridges_table table th._bb{background-color:#e6e0ec;}
.actpoints_bridges_table table th._cb{background-color:#d8e3bf;}
.actpoints_bridges_table table th._db{background-color:rgba(253, 213, 178, 0.57);}
.actpoints_bridges_table table th._eb{background-color:rgba(178, 178, 178, 0.38);}
.actpoints_bridges_table table th._ebRedeemed{background-color:#ecf0df;}
</style>
<script type="application/javascript">
sesJqueryObject('.redeem_amt').click(function(){
  Smoothbox.open(sesJqueryObject(this).data('src'));
	parent.Smoothbox.close;
	return false;  
})
</script>
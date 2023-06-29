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
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<div class="actpoints_bridges_page">
  <h3>Bridges Allotment System</h3>
  <b>NAME:</b>&nbsp;<?php echo $viewer->displayname;?>&nbsp;&nbsp;&nbsp;&nbsp;<b>PROFILE ID:</b>&nbsp;<?php echo $viewer->user_id;?>&nbsp;&nbsp;&nbsp;&nbsp;<b>RANK:</b>&nbsp;<?php echo Engine_Api::_()->getItem('authorization_level',$viewer->level_id)->title;?><br /><br /><br />
  <div class="actpoints_bridges_table">
  	<table>
    	<thead>
      	<tr>
          <th class="isdata _year">Year 2017</th>
          <th class="nodata"></th>
          <th class="isdata _value">Value<span>BB Rs.</span></th>
          <th class="nodata"></th>
          <th class="isdata _bb">BB<span>BUSINESS BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _cb">CB<span>COLLECTION BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _db">DB<span>DIRECT BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _eb">EB<span>EXTRA BRIDGES </span><span>(INR)</span></th>
          <th class="isdata _eb">FB<span>FINAL BRIDGES (INR)</span><span>FB = Value of (BB+CB+DB)+EB</span></th>
          <th class="nodata"></th>
          <th class="isdata _ebRedeemed">FB<p><span>Redeemed</span><span>To Bank A/C</span><span>Balance</span><span>Total Balance</span></p></th>
        </tr>
      </thead>
	  <tbody>
	    <?php for($i=1;$i<13;$i++):
	    
	    if(!empty($this->bridges[$i])) {
	       $item = $this->bridges[$i];
	       $bb = $item['total_bb'];
	       $cb = $item['total_cb'];
	       $db = $item['total_db'];
         
	     }
	     else {
	       $bb = $cb = $db =  0;
	     }
       
       $date = date('m-Y', mktime(0, 0, 0, $i, 10));
	      $bridgesValue1 = Engine_Api::_()->sescustomize()->getValue($date);
         if($bridgesValue1)
          $bridgesValue = $bridgesValue1['value'];
         else
          $bridgesValue = 0;
	    ?>
	        
          	<tr><td colspan="13" class="blankrow"></td></tr>
          	<tr>
              <td class="isdata _year"><?php echo date('M', mktime(0, 0, 0, $i, 10));?></td>
              <td class="nodata"></td>
              <td class="isdata _value"><span><?php echo $bridgesValue;?></span></td>
              <td class="nodata"></td>
              <td class="isdata _bb"><p><span><?php echo $bb;?></span><span><?php echo $bb*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _cb"><p><span onclick="showUsers('<?php echo date('m', mktime(0, 0, 0, $i, 10));?>', 'cb','<?php echo $cb;?>')"><?php echo $cb;?></span><span><?php echo $cb*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _db"><p><span onclick="showUsers('<?php echo date('m', mktime(0, 0, 0, $i, 10));?>', 'db','<?php echo $db;?>')"><?php echo $db;?></span><span><?php echo $db*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _eb"><span>0</td>
              <td class="isdata _eb"><span><?php echo $bb*$bridgesValue + $cb*$bridgesValue + $db*$bridgesValue ;?></span></td>
              <td class="nodata"></td>
              <td class="isdata _ebRedeemed"><p><span>-</span><span>-</span><span>-</span><span><?php echo $bb*$bridgesValue+$cb*$bridgesValue+$db*$bridgesValue;?></span></p></td>
            </tr>
         <?php endfor;?>
      </tbody>
    </table>
  </div>

</div>

<script type="text/javascript">
    function showUsers(month, type, bvalue) {
      if(bvalue == 0)
      return;
      url = en4.core.staticBaseUrl+'sescustomize/index/get-users/month/'+month+'/type/'+type;
      openURLinSmoothBox(url);	
      return;
    }
</script>

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
	width:25%;
}
.actpoints_bridges_table table th._year{background-color:rgba(136, 148, 178, 0.38);}
.actpoints_bridges_table table th._value{background-color:#dcedf4;}
.actpoints_bridges_table table th._bb{background-color:#e6e0ec;}
.actpoints_bridges_table table th._cb{background-color:#d8e3bf;}
.actpoints_bridges_table table th._db{background-color:rgba(253, 213, 178, 0.57);}
.actpoints_bridges_table table th._eb{background-color:rgba(178, 178, 178, 0.38);}
.actpoints_bridges_table table th._ebRedeemed{background-color:#ecf0df;}
</style>
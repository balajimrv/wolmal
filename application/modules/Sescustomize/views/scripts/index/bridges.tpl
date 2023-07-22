<?php

$previous_qualified_total=0;
$previous_non_qualified_total=0;
$EB_value = 0;

foreach($this->full_bridges as $x => $value) {
    $previous_bb = $this->full_bridges[$x]['total_full_bb'];
    $previous_cb = $this->full_bridges[$x]['total_full_cb'];
    $previous_db = $this->full_bridges[$x]['total_full_db'];
    $creation_date = $this->full_bridges[$x]['creation_date'];
    
    $monthYear = date('m-Y', strtotime($creation_date));
    $dateMonth = date('Y-m', strtotime($creation_date));
    
    $valueRs = Engine_Api::_()->sescustomize()->getValue($monthYear);
    $bridges_value = $valueRs['value'];
    
    $Bank =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'bank'));
    $Redeem =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'redeem'));
  
    if($previous_bb > 0){
    	$FB_value = (($previous_bb*$bridges_value) + ($previous_cb*$bridges_value) + ($previous_db*$bridges_value)) + $EB_value;
    }else{
    	$FB_value = 0;
    }
    
    $RD_value = ($Bank+$Redeem);
    $totalPreviousEarn = $totalPreviousEarn + ($FB_value - $RD_value);
    
    //for non qualified amount
    if($previous_bb == 0){
        $previous_non_qualified_total = $previous_non_qualified_total + ((($previous_cb*$bridges_value) + ($previous_db*$bridges_value)) + $EB_value);
    }
    
    //for qualified amount
    $previous_qualified_total = $previous_qualified_total + $FB_value;
}

    $totalEarn = $totalPreviousEarn;
    $non_qualified_total=$previous_non_qualified_total;
    $qualified_total=$previous_qualified_total;
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
<?php $bridgesValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescustomize.bridges.value', 10);?>
  <?php $isBalance = Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->currentFb(); 
        $isRequestSend = Engine_Api::_()->getDbtable('reedemrequests', 'sescustomize')->isReqExists();; 
  ?>
  <?php echo $viewer = Engine_Api::_()->user()->getViewer();?>
<div class="actpoints_bridges_page">
  <h3>Vallet</h3><b>Year:</b>&nbsp;
  <?php $date = new DateTime();?>
<?php $years = $date->format('Y')-'2017';?>  
 <select name="parent" id="parent" class="postform" onChange="showYearData(this.value)">
 
  <?php for ($i=0; $i<=$years; $i++): ?>
    <option class="" value="<?php echo '2017'+$i; ?>" <?php echo $_GET['year'] && $_GET['year'] == '2017'+$i ? 'selected' : (empty($_GET['year']) && '2017'+$i == date('Y') ? 'selected': '');?> ><?php echo "2017"+$i; ?></option>
  <?php endfor;?>
</select>&nbsp;
  <b>NAME:</b>&nbsp;<?php echo $viewer->displayname;?>&nbsp;&nbsp;&nbsp;&nbsp;<b>PROFILE ID:</b>&nbsp;<?php echo $viewer->user_id;?>&nbsp;&nbsp;&nbsp;&nbsp;<b>RANK:</b>&nbsp;<?php echo Engine_Api::_()->getItem('authorization_level',$viewer->level_id)->title;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->url(array('action'=>'reference-member'),'sescustomize_bridges',true);?>" ><?php echo $this->translate("My Referrals");?></a>
  
  
  <div style="float:right;margin: 20px;">
  
  <span>
      <strong>
          NON QUALIFIED AMOUNT: 
            <?php
                for($i=1;$i<13;$i++){
                    $dateMonth = date('Y-m', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));
                    if(!empty($this->bridges[$i])) {
                        $item = $this->bridges[$i];
                        $bb = (float)$item['total_bb'];
                        $cb = (float)$item['total_cb'];
                        $db = (float)$item['total_db'];         
                    }
                    else {
                        $bb = $cb = $db =  0;
                    }
                    
                    $date = date('m-Y', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));
                    $get_bridges_value = Engine_Api::_()->sescustomize()->getValue($date);
                    $bridges_value = 0;
                    if($get_bridges_value){
                        $bridges_value = (int)$get_bridges_value['value'];
                    }
                    
                    if($bb == 0){
                        $non_qualified_total = $non_qualified_total + ((($cb * $bridges_value) + ($db * $bridges_value)) + $EB_value);
                    }
                    
                    //Qualified Total Amount
                    $bank =  (float)Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'bank'));
                    $redeem =  (float)Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'redeem'));
                   
                    $fb_value=0;
                    if($bb > 0){
                        $fb_value = ($bb * $bridges_value) + ($cb * $bridges_value) + ($db * $bridges_value) + $EB_value;
                    }
                    $redeemValue = ($bank+$redeem);
                    $qualified_total = $qualified_total + ($fb_value);
                }
                echo round($non_qualified_total,1);
            ?>
      </strong>
  </span>
  <span style="padding-left: 20px;">
      <strong>QUALIFIED TOTAL AMOUNT: 
        <?php echo round($qualified_total,1); ?>
      </strong>
  </span>	
  
  	  <span style="padding-left: 20px;"><strong>AMOUNT</strong> (Redeemed+Withdrawn): 
      <strong><?php
  $earn =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->totalAmount($viewer->user_id);
  echo round($earn,1);
  ?></strong></span>
  <span style="font-size: 16px; padding-left: 20px;">
      <a href="/bridges/view-request/">View Requests
  </span></a>
  </div>
  
  <style>
     .actpoints_bridges_table {
  
  
    overflow-x: auto;
    white-space: nowrap;
    width:100%;
}

.actpoints_bridges_table table {
  width: 1170px;
}


 </style>
  
  <div class="actpoints_bridges_table">
  	<table style="width: 110020px; max-width:1120px;">
    	<thead>
      	<tr>
          <th class="isdata _year">Year <?php echo $this->year;?></th>
          <th class="nodata"></th>
          <th class="isdata _value">Value<span>BB Rs.</span></th>
          <th class="nodata"></th>
          <th class="isdata _bb">BB<span>BUSINESS BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _cb">CB<span>COLLECTION BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _db">DB<span>DIRECT BRIDGES</span><p><span>GAINED</span><span>VALUE</span></p></th>
          <th class="nodata"></th>
          <th class="isdata _eb" style="background-color:#FFCCE6;">EB<span>EXTRA BRIDGES </span><span>(INR)</span></th>
          <td class="nodata"></td>
          <th class="isdata _eb" style="background-color:#E6CCFF;">FB<span>FINAL BRIDGES</span><span>FB = Value of (BB+CB+DB)+EB</span></th>
          <th class="nodata"></th>
          <th class="isdata _ebRedeemed">FB 
            <p>
              <span>Redeemed</span>
              <span>To Bank A/C <?php if($totalEarn >= 10000 || $_SESSION['totalEarn'] >= 5000){ ?><br> (<a href="javascript:;" class="redeem_amt" data-src="sescustomize/index/redeem-form/">WITHDRAWAL FORM<?php  } ?></a>)</span>
              <span>Total Balance</span>
            </p>
          </th>
        </tr>
      </thead>
	  <tbody>
	      
	    <?php for($i=1;$i<13;$i++):
	    $dateMonth = date('Y-m', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));
	    if(!empty($this->bridges[$i])) {
	       $item = $this->bridges[$i];
	       $bb = $item['total_bb'];
	       $cb = $item['total_cb'];
	       $db = $item['total_db'];         
	     }
	     else {
	       $bb = $cb = $db =  0;
	     }
       
       $date = date('m-Y', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));
	      $bridgesValue1 = Engine_Api::_()->sescustomize()->getValue($date);
         if($bridgesValue1)
          $bridgesValue = $bridgesValue1['value'];
         else
          $bridgesValue = 0;
	    ?>
	          <?php  
	          
	               $fbValueUserTable =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize');
	               $selectFb = $fbValueUserTable->select()->where('DATE_FORMAT(creation_date,"%Y-%m") =?',$dateMonth)->where('user_id =?',$this->viewer()->getIdentity())->where('type =?','insert')->limit(1);
	               $fbVal = $fbValueUserTable->fetchRow($selectFb);
	               
	               if($fbVal)
	                $fbVal = $fbVal->total;
	               else
	                $fbVal = 0;
	                $earn =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->earningGroupBy(array('month'=>$dateMonth));
                    $bank =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'bank'));
                    $redeem =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'redeem'));
                   
                   if($bb > 0){
                    $fbValue = ($bridgesValue*($bb + $cb + $db))+$EB_value;
                   }else{
                    $fbValue = 0;
                   }
                   
                   $redeemValue = ($bank+$redeem);
                   
                   $totalEarn = $totalEarn + ($fbValue - $redeemValue);
                   
                   $_SESSION['totalEarn'] = $totalEarn;
                   
              ?>
          	<tr><td colspan="13" class="blankrow"></td></tr>
          	<tr>
              <td class="isdata _year"><?php echo date('M', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));?></td>
              <td class="nodata"></td>
              <td class="isdata _value"><span><?php echo $bridgesValue;?></span></td>
              <td class="nodata"></td>
              <td class="isdata _bb"><p><span><?php echo $bb;?></span><span><?php echo $bb*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _cb"><p><span style="cursor:pointer;" onclick="showUsers('<?php echo date('m', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));?>', 'cb','<?php echo $cb;?>',<?php echo (!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y')); ?>)"><?php echo $cb;?></span><span><?php echo $cb*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _db"><p><span style="cursor:pointer;" onclick="showUsers('<?php echo date('m', mktime(0, 0, 0, $i, 10,(!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y'))));?>', 'db','<?php echo $db;?>',<?php echo (!empty($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y')); ?>)"><?php echo $db;?></span><span><?php echo $db*$bridgesValue;?></span></p></td>
              <td class="nodata"></td>
              <td class="isdata _eb"><span>0</span></td>
              <td class="nodata"></td>
              <td class="isdata _eb"><span><?php echo round($fbValue,1) ;?></td>
              <td class="nodata"></td>
              <td class="isdata _ebRedeemed">
                <p>
                  <span style="cursor:pointer;" onClick="showTransferData('<?php echo $dateMonth; ?>', 'redeem','<?php echo $redeem;?>');"><?php echo $redeem; ?></span>
                  <span style="cursor:pointer;" onClick="showTransferData('<?php echo $dateMonth;?>', 'bank','<?php echo $bank;?>');"><?php echo $bank; ?></span>
                  <span><?php echo strtotime(date('Y-m')) >= strtotime($dateMonth) ? round($totalEarn,2) : 0; ?></span>
                </p>
              </td>
            </tr>
         <?php endfor;?>
      </tbody>
    </table>
  </div>

</div>

<script type="text/javascript">
  function showYearData(value) {
    window.location.href = "<?php echo $this->url(array('action'=> 'bridges'),'sescustomize_bridges',true);?>"+"?year="+value;
  }
    function showTransferData(month, type, value){
      if(value == 0)
      return;
      var url = en4.core.staticBaseUrl+'sescustomize/index/get-transfer/month/'+month+'/type/'+type;
      openURLinSmoothBox(url);	
      return;
    }
    function showUsers(month, type, bvalue,year) {
      if(bvalue == 0)
      return;
      var url = en4.core.staticBaseUrl+'sescustomize/index/get-users/month/'+month+'/type/'+type+'/year/'+year;
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
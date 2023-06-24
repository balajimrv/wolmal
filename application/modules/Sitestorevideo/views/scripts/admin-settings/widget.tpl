<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestorevideo','controller'=>'settings','action'=>'index'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'show-video'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate("Ajax based Tabbed widget for Videos") ?>
</h3>
<p>
  <?php echo $this->translate("Below you can choose the tabs and their sequence for this widget. To adjust the sequence, use drag-and-drop and then click on 'Save Order' to save it. If you do not see this widget at the desired location then make sure that you have placed this from the Layout Editor and that you have enabled atleast one tab for it.") ?>
</p>
<br />
<div class="seaocore_admin_order_list">
	<div class="list_head">
	  <div style="width:23%">
	    <?php echo $this->translate("Tab Title");?>
	  </div>
	   <div style="width:23%" class="admin_table_centered">
	    <?php echo $this->translate("No. of Videos");?>
	  </div>
	  <div style="width:23%" class="admin_table_centered">
	    <?php echo $this->translate("Enabled");?>
	  </div>
	  <div style="width:23%" class="admin_table_centered">
	    <?php echo $this->translate("Options");?>
	  </div>
	</div>
  <form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' =>'update-order')) ?>'>
  	<input type='hidden'  name='order' id="order" value=''/>
    <div id='order-element'>
    	<ul>
      	<?php foreach ($this->tabs as $item) : ?>
        	<li>
	          <input type='hidden'  name='order[]' value='<?php echo $item->tab_id; ?>'>
	          <div style="width:23%;" class='admin_table_bold'>
	            <?php echo $this->translate($item->getTitle()); ?>
	          </div>
	          <div style="width:23%;" class="admin_table_centered">
	            <?php echo $item->limit ?>
	          </div>
	          <div style="width:23%;" class='admin_table_centered'>
	            <?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorevideo', 'controller' => 'settings', 'action' => 'enabled', 'tab_id' => $item->tab_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable Tab'))), array())  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestorevideo', 'controller' => 'settings', 'action' => 'enabled', 'tab_id' => $item->tab_id), $this->htmlImage($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable Tab')))) ) ?>
	          </div>
	          <div style="width:23%;" class='admin_table_centered'>          
	            <a href='<?php echo $this->url(array('action' => 'edit-tab', 'tab_id' => $item->tab_id)) ?>' class="smoothbox">
	              <?php echo $this->translate("Edit") ?>
	            </a>            
	      		</div>
	      	</li>
	    	<?php endforeach; ?>
   		</ul>
  	</div>
	</form>
  <br />
   <button onClick="javascript:saveOrder(true);" type='submit'>
    <?php echo $this->translate("Save Order") ?>
  </button>
</div>
<script type="text/javascript">

  var saveFlag=false;
  var origOrder;
	var changeOptionsFlag = false;

		function saveOrder(value){
			saveFlag=value;
    	var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      $("order").value=finalOrder;

      	$('saveorder_form').submit();
		}
  window.addEvent('domready', function(){
				//         We autogenerate a list on the fly
				var initList = [];
				var li = $('order-element').getElementsByTagName('li');
				for (i = 1; i <= li.length; i++)
						initList.push(li[i]);
				origOrder = initList;
				var temp_array = $('order-element').getElementsByTagName('ul');
				temp_array.innerHTML = initList;
				new Sortables(temp_array);
		});

		window.onbeforeunload = function(event){
			var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
				finalOrder.push(li[i]);



			for (i = 0; i <= li.length; i++){
				if(finalOrder[i]!=origOrder[i])
				{
					changeOptionsFlag = true;
					break;
				}
			}

			if(changeOptionsFlag == true && !saveFlag){
				var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the tabs has been detected. If you click Cancel, all unsaved changes will be lost. Click OK to save change and proceed.")); ?>");
				if(answer) {
          $('order').value=finalOrder;
					$('saveorder_form').submit();

				}
			}
		}
</script>
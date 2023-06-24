<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Activitypoints/externals/scripts/activitypoints.js')
?>

<style>
.clearfix:after {
	content: ".";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
} 

.clearfix {
	display: inline-block;
}

/* Hide from IE Mac \*/
.clearfix {
	display: block;
}  /* End hide from IE Mac */ /* --- a /begin --- */

.uptopusers .entry {
	padding: 10px 15px 0 15px;
	border-bottom: 1px solid #F6F6F6;
}

.uptopusers .entry .topuser_title {
  font-size: 14px;
  font-weight:bold;
  margin: 0px;
  padding: 0px;
  margin-bottom: 5px;
}

/*.uptopusers .entry h2 span {*/
/*	float: left;*/
/*}*/
  
.uptopusers .entry .entry_body {
	margin-bottom: 10px;
}

.uptopusers .entry .image-wrap {
  width: 100px;
	float: left;
	margin: 4px 15px 15px 0;
}

.upcontent1 a {
	color: #4b4b4b;
	text-decoration: underline;
}

.upcontent1 a:hover {
	text-decoration: none;
}

.uptopusers .entry .upcontent1 {
	width: 200px;
	float: left;
}

.uptopusers .text {
	padding-bottom: 10px;
}

.uptopusers .entry .options {
	width: 255px;
	float: left;
    line-height: 17px;
}

.uptopusers .options ul {
	padding: 0 0 5px 25px;
	margin: 10px 0 0 0;
	list-style: none;
	font-size: 14px;
}

.uptopusers ul {
	margin-left: 15px;
}

.uptopusers p,.uptopusers ol,.uptopusers ul {
	padding-bottom: 15px;
	font-size: 12px;
}

.uptopusers .options ul li {
	padding: 1px 0 1px 12px;
}
  
.uptopusers ul li,.uptopusers ol li {
	padding: 1px 0 1px 0;
}

</style>

<?php if( count($this->navigation) ): ?>
<div class="headline">
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


<div style="font-weight: bold; font-size: 20px; width: 200px; padding-top: 10px"> <?php echo $this->translate('100016806') ?> </div>

<table cellpadding='0' cellspacing='0' width='100%' style="margin-top: 20px">
<tr>
<td style='padding-right: 10px; vertical-align: top;'>

  <div style="width: 640px; border: 1px solid #DDD" class="uptopusers">

  <?php for($index = 0; $index < count($this->items); $index++) : ?>

    <div class="entry clearfix">
      <div class="image-wrap">
		<?php echo $this->htmlLink($this->items[$index]->getHref(), $this->itemPhoto($this->items[$index], 'thumb.icon')) ?>
      </div>
      <div class="upcontent1" <?php if($index == 0): ?>style="width: 150px"<?php endif;?>>
        <div class="topuser_title"><?php echo $this->htmlLink($this->items[$index]->getHref(), $this->items[$index]->getTitle()) ?></div>
        <div class="text clearfix">
          
		  <?php echo $this->translate('100016857') ?> <?php echo $this->items[$index]['points'] ?><br/>
          <?php echo $this->translate('100016859') ?> <?php echo $this->items[$index]['view_count'] ?><br/>
          <?php echo $this->translate('100016858') ?> <?php echo Engine_Api::_()->getDbTable('pointranks','activitypoints')->getRank($this->items[$index]) ?> <br/>
		  
      </div>
      </div>
      <?php if($index == 0): ?>
      <div style="padding-top: 18px; float: left; width: 50px">
      <img src="application/modules/Activitypoints/externals/images/MemberQuarter-large.gif">
      </div>
      <?php endif; ?>
      <div class="options">
        <ul>
          <li><?php echo $this->htmlLink($this->items[$index]->getHref(), $this->translate('ACTIVITYPOINTS_VIEW_USER_PROFILE', $this->items[$index]->getTitle()) ) ?></li>
        </ul>
      </div>
    </div>

  <?php endfor; ?>
  
  </div>

</td>


<td valign='top'>

<div style='padding: 5px; background: #F9F9F9; border: 1px solid #DDDDDD;'>

<div style="text-align:center; font-weight: bold"> <?php echo $this->translate('100016808') ?> </div>
<br>
<?php echo $this->translate('100016809') ?>
<ol style="list-style: square; padding: 0px;margin-left: 20px; margin-bottom: 10px; margin-top: 10px">
<?php if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('album')) : ?>
<li> <a href="<?php echo $this->url(array('module'	=> 'albums', 'controller'	=> 'upload'),'default') ?>"> <?php echo $this->translate('100016810') ?> </a>
<?php endif; ?>
<li> <?php echo $this->translate('100016811') ?>
<li> <a href="<?php echo $this->url(array('module'	=> 'core', 'controller'	=> 'invite'),'default') ?>"> <?php echo $this->translate('100016812') ?> </a>
<?php if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('group')) : ?>
<li> <a href="<?php echo $this->url(array('module'	=> 'groups', 'controller'	=> 'create'),'default') ?>"><?php echo $this->translate('100016813') ?></a> <?php echo $this->translate('100016814') ?>
<?php endif; ?>
<li> <?php echo $this->translate('100016814') ?>

</ol>

<br>
  
<a href="<?php echo $this->url(array(), 'activitypoints_help') ?>"> <?php echo $this->translate('100016816') ?> </a>

<br>
</div>

</td>

</tr>
</table>


  
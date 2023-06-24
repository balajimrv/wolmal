<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $randonNumber = $this->identity; ?>
<!--for IE support canvas-->
<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/excanvas.js"></script><![endif]-->
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.tagcanvas.min.js'); ?>
<div class="sesbasic_cloud_widget sesbasic_clearfix">
  <div id="myCanvasContainer_<?php echo $randonNumber ?>" style="height:<?php echo $this->height;  ?>px">
   <canvas style="width:100%;height:100%;" id="myCanvas_<?php echo $randonNumber ?>">
    <p>Anything in here will be replaced on browsers that support the canvas element</p>
    <ul>
      <?php foreach($this->paginator as $valueTags){ 
      		if($valueTags['text'] == '' || empty($valueTags['text'] ))
          	continue;
      ?>
      <li><a href="<?php echo $this->url(array('module' =>'sesalbum','controller' => 'index', 'action' => 'browse'),'sesalbum_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>"><?php echo $valueTags['text'] ?></a></li>
      <?php } ?>
    </ul>
   </canvas>
  </div>
  <a href="<?php echo $this->url(array('action' => 'tags'),'sesalbum_general',true);?>" class="sesbasic_more_link clear"><?php echo $this->translate("See All Tags");?> &raquo;</a>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
   if( ! jqueryObjectOfSes ('#myCanvas_<?php echo $randonNumber ?>').tagcanvas({
		textFont: 'Impact,"Arial Black",sans-serif',
		textColour: "<?php echo $this->color; ?>",
		textHeight: "<?php echo $this->textHeight; ?>",
		maxSpeed : 0.03,
		depth : 0.75,
		shape : 'sphere',
		shuffleTags : true,
		reverse : false,
		initial :  [0.1,-0.0],
		minSpeed:.1
   })) {
     // TagCanvas failed to load
     jqueryObjectOfSes ('#myCanvasContainer_<?php echo $randonNumber ?>').hide();
   }
 });
 </script>
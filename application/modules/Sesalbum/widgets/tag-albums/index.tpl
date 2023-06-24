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
<div class="sesbasic_tags sesbasic_clearfix sesbasic_bxs">
  <?php foreach($this->tagCloudData as $valueTags){ 
  				if($valueTags['text'] == '' && empty($valueTags['text']))
          	continue;
  ?>
  	<a href="<?php echo $this->url(array('module' =>'sesalbum','controller' => 'index', 'action' => 'browse'),'sesalbum_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>"><b><?php echo $valueTags['text'] ?></b><sup><?php echo $valueTags['itemCount']; ?></sup></a>
  <?php } ?>
</div>
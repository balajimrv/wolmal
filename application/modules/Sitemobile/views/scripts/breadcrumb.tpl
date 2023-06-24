<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<section class="breadcrumb">
  <?php foreach($this->brdObj as $data): ?>  
    <?php if(!empty($data)):?>
			<?php $isActive = $data['icon']=='arrow-d' ? 1:0; ?>
<!--  For those links which are in end, also having arrow-d, but they are links not just text-->
			<?php if($isActive):?>
         <?php if(isset($data['href'])):?>
  <a href="<?php echo $data['href'];?>"<?php if(isset($data['class'])): ?> class ="<?php echo $data['class']?>"<?php endif;?> ><?php echo $data['title']; ?></a>
         <?php else:?>
      <?php echo $data['title']; ?>
       <?php endif;?>
  
    <?php else:?>
      <a <?php if(isset($data['href'])):?>href="<?php echo $data['href'];?>"<?php endif;?><?php if(isset($data['class'])): ?> class ="<?php echo $data['class']?>"<?php endif;?> ><?php echo $data['title']; ?></a><span class="brd-sep">&raquo;</span>
    <?php endif; ?>
      
  <?php endif; ?>
 <?php endforeach;?>
</section>
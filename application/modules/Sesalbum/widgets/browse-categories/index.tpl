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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?>
<ul class="<?php echo ($this->allign == 1) ? 'sesbasic_horizontal_categories' : 'sesbasic_sidebar_categories' ?> sesbasic_bxs sesbasic_clearfix">
	<?php if($this->type == 'album')
  				$action = 'browse';
         else
         	$action = 'browse-photo';
           ?>
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <a class="catlabel <?php echo $this->icon == 1 ? '' : 'noicon' ?>" href="<?php echo $this->url(array('module' => 'sesalbum','controller'=>'index','action' => $action), 'sesalbum_general', true).'?category_id='.urlencode($item->getIdentity()) ; ?>" <?php if($this->icon == 1 && $item->cat_icon != '' && !is_null($item->cat_icon)){ ?>style="background-image:url(<?php echo $this->storage->get($item->cat_icon, '')->getPhotoUrl(); ?>);"<?php } ?>><?php echo $item->category_name; ?><?php echo $this->show_count == 'yes' ? ' ('.$item->total_item_categories.')' : '' ; ?></a>
    </li>
  <?php endforeach; ?>
</ul>
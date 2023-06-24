<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */
 
($this->home_style == '1' ) ? ($width_forwide = ' width_main') : ($width_forwide = '');
$defaultImage  = ($this->defaultImage) ? $this->defaultImage : 'http://placehold.it/1x1';
?>

<ul class="home_photos home_recent_blogs width_main clearfix<?php echo $width_forwide; ?>">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <?php
      preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $item->body, $image);
      $imagex = ($image['src']) ? $image['src'] : $defaultImage;
      echo '<div class="image" style="background-image: url('.$imagex.')">'.$this->htmlLink($item->getHref()).'</div>';
      ?>
      <div class="description">
        <h3 class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
        </h3>
        <div class="text">
          <?php
          echo $this->string()->truncate($this->string()->stripTags($item->body), 190);
          ?>
        </div>
        <div class="more">
          <?php echo $this->htmlLink($item->getHref(), $this->translate('Read More')); ?>
        </div>
        <ul class="info">
          <li class="owner">
            <?php
            $owner = $item->getOwner();
            echo $this->htmlLink($owner->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon').$owner->getTitle());
            ?>
          </li>
          <li class="stats">
            <?php echo $this->timestamp($item->{$this->recentCol}); ?>
          </li>
        </ul>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Offers Listings');?>
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

<div class='layout_right'>
<div class='activityrewards_gutter'>
    <?php echo $this->form->render($this) ?>
  
    <?php
    $this->tagstring = "";
    if (count($this->itemTags )){
      foreach ($this->itemTags as $tag){
        if (!empty($tag->text)){
          $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a> ";
        }
      }
    }
    ?>
  
    <?php if ($this->tagstring ):?>
      <h4><?php echo $this->translate('Tags')?></h4>
      <ul>
        <?php echo $this->tagstring;?>
      </ul>
    <?php endif; ?>
  
  </div>
</div>

<div class='layout_middle'>
  <div class='activityrewards_middle'>
  <?php if( $this->tag ): ?>
    <h3>
      <?php echo $this->translate('Showing offers using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array(), 'activityrewards_earn', true) ?>">(x)</a>
    </h3>
  <?php endif; ?>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="activityrewards_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='activityrewards_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <?php if($item->getPoints() > 0) : ?>
          <div class='activityrewards_browse_points' style="float: right">

            <div>
              <img style="float: left; margin-right: 8px" src="application/modules/Activitypoints/externals/images/userpoints_coins16.png">
              <span style="font-size: 1.3em; color: #777">
                <span style="font-weight: bold"><?php echo $item->getPoints() ?></span>&nbsp;<?php echo $this->translate('100016708') ?></span>
            </div>

          </div>
          <?php endif; ?>
          <div class='activityrewards_browse_info'>
            <div class='activityrewards_browse_info_title'>
              <h3>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              </h3>
            </div>
            <div class='activityrewards_browse_info_date'>
              <?php
                echo $this->timestamp($item->userpointearner_date);
              ?>
            </div>
            <div class='activityrewards_browse_info_blurb'>
              <?php echo $item->getDescription(); ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif( $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No offers with that criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No offers yet.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","activityrewards")); ?>
</div>
</div>
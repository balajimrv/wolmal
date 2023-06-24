<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="ui-page-content">
  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-author-photo">
      <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
    </div>	
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
        <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
      </div>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->timestamp($this->blog->creation_date) ?>
        <?php if ($this->category): ?>
          - 
          <?php
          echo $this->htmlLink(array(
              'route' => 'blog_general',
              'QUERY' => array('category' => $this->category->category_id)
                  ), $this->translate($this->category->category_name)
          )
          ?>
        <?php endif; ?>
        <?php if (count($this->blogTags)): ?>
          -
          <?php foreach ($this->blogTags as $tag): ?>
            #<?php echo $tag->getTag()->text ?>&nbsp;
          <?php endforeach; ?>
<?php endif; ?>
      </div>
      <div class="sm-ui-cont-cont-date">
<?php echo $this->translate(array('%s view', '%s views', $this->blog->view_count), $this->locale()->toNumber($this->blog->view_count)) ?>
      </div>
    </div>	
  </div>
  <div class="sm-ui-cont-cont-des">
<?php echo nl2br($this->blog->body) ?>
  </div>
</div>
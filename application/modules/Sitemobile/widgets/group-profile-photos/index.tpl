<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="group_album_options profile-content-top-button" data-type="horizontal" data-role="controlgroup">
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <?php if(!Engine_Api::_()->sitemobile()->isApp()) {  
        echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'photo',
            'action' => 'list',
            'subject' => $this->subject()->getGuid(),
                ), $this->translate('View All Photos'), array(
            //  'class' => 'buttonlink icon_group_photo_view',
            'data-role' => "button", 'data-icon' => "th", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
    }
    ?>
  <?php endif; ?>

  <?php if ($this->canUpload): ?>
    <?php      
        echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $this->subject()->getGuid(),
                ), $this->translate('Upload Photos'), array(
            //  'class' => 'buttonlink',
            'data-role' => "button", 'data-icon' => "picture", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
      
    ?>
  <?php endif; ?>
</div>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <ul class="sm-ui-thumbs thumbs thumbs_nocaptions">
  <?php foreach ($this->paginator as $photo): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
      <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No photos have been uploaded to this event yet.'); ?>
    </span>
  </div>
<?php endif; ?>
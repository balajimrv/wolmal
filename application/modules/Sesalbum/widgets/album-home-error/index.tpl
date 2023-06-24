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
<?php if(count($this->paginator) <= 0): ?>
<div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created an '.$this->itemType.' yet.');?>
        <?php if ($this->canCreate):?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create','module'=>'sesalbum'), "sesalbum_general",true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div>
  <h2><?php echo $this->user->getTitle() ?></h2>
  <?php if($this->show_designation && $this->editor->designation): ?>
    <?php echo "<b>(".$this->editor->designation.")</b>" ?>
  <?php endif; ?>
</div>
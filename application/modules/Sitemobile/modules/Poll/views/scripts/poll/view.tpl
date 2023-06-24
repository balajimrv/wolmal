<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<div class="ui-page-content">
  <div class="sm-ui-cont-head">	
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
        <?php echo $this->poll->getTitle()?>
            <?php if( $this->poll->closed ): ?>
            <p class="ui-li-aside">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/close.png' alt="<?php echo $this->translate('Closed') ?>" />
            </p>
            <?php endif;?>
      </div>
      <div class="sm-ui-cont-cont-date">
		<?php echo $this->translate('Created by %s', $this->htmlLink($this->poll->getOwner(), $this->poll->getOwner()->getTitle())) ?>
        -
		<?php echo $this->timestamp($this->poll->creation_date) ?>
      </div>

      </div>
    </div>
  <div class="sm-ui-cont-cont-des">
		<?php echo nl2br($this->poll->description); ?>
	</div>
	
	<?php  
		echo $this->render('_poll.tpl')
	?>

</div>
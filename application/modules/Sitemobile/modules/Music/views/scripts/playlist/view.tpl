<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<?php
// this is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs    = $playlist->getSongs();
$can_edit = $this->can_edit;
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()):?>
<div class="sm-ui-cont-head">
		<div class="sm-ui-cont-cont-info">
			<div class="sm-ui-cont-author-name">
					<?php echo $playlist->getTitle();?>
			</div>
			<div class="sm-ui-cont-cont-date">
        <?php echo $this->translate('Created by ') ?>
	      <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
			-
	    <?php echo  $this->timestamp($playlist->creation_date) ?>
	    </div>
			<div class="sm-ui-cont-cont-date">
      <?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?>
      -
      <?php echo $this->translate(array('%s view', '%s views', $playlist->view_count), $this->locale()->toNumber($playlist->view_count)) ?>
			</div>
		</div>
	</div>
<p class="description">
    <?php echo $playlist->getDescription() ?>
  </p>
  <?php endif; ?>
<div class="sm-ui-video-view">
  	 		<?php echo $this->partial('_Player.tpl', array('playlist'=>$playlist, 'can_edit' => $can_edit)) ?>
</div>
  <?php if (Engine_Api::_()->sitemobile()->isApp()):?>
<div class="sm-ui-cont-head">
		<div class="sm-ui-cont-cont-info">
			<div class="sm-ui-cont-author-name">
					<?php echo $playlist->getTitle();?>
			</div>
			<div class="sm-ui-cont-cont-date">
        <?php echo $this->translate('Created by ') ?>
	      <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
			-
	    <?php echo  $this->timestamp($playlist->creation_date) ?>
	    </div>
			<div class="sm-ui-cont-cont-date">
      <?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?>
      -
      <?php echo $this->translate(array('%s view', '%s views', $playlist->view_count), $this->locale()->toNumber($playlist->view_count)) ?>
			</div>
		</div>
	</div>
  <p class="description">
    <?php echo $playlist->getDescription() ?>
  </p>
<?php endif; ?>
 
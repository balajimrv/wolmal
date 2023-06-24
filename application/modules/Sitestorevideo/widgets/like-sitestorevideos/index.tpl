<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitestore_sidebar_list">
	<?php foreach ($this->paginator as $sitestorevideo): ?>
    <?php  $this->partial()->setObjectKey('sitestorevideo');
        echo $this->partial('application/modules/Sitestorevideo/views/scripts/partialWidget.tpl', $sitestorevideo);
		?>		       
					<?php echo $this->translate(array('%s like', '%s likes', $sitestorevideo->like_count), $this->locale()->toNumber($sitestorevideo->like_count)) ?>
					|
					<?php echo $this->translate(array('%s view', '%s views', $sitestorevideo->view_count), $this->locale()->toNumber($sitestorevideo->view_count)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
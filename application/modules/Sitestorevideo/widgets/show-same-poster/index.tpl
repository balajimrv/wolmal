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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
	<?php foreach ($this->paginator as $sitestorevideo): ?>
    <?php  $this->partial()->setObjectKey('sitestorevideo');
        echo $this->partial('application/modules/Sitestorevideo/views/scripts/partialWidget.tpl', $sitestorevideo);
		?>		       
          <?php
            $sitestore->store_title  = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);
	          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
	          $tmpBody = strip_tags($sitestore->store_title);
	          $store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestorevideo->store_id, $sitestorevideo->owner_id, $sitestorevideo->getSlug()), $store_title, array('title' => $sitestore->store_title)) ?> 
          </div>
					<div class="sitestore_sidebar_list_details"> 
						<?php echo $this->translate(array('%s comment', '%s comments', $sitestorevideo->comment_count), $this->locale()->toNumber($sitestorevideo->comment_count)) ?>,
						<?php echo $this->translate(array('%s view', '%s views', $sitestorevideo->view_count), $this->locale()->toNumber($sitestorevideo->view_count)) ?>      
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
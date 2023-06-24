<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Videos","icon"=>"arrow-d")
    );

echo $this->breadcrumb($breadcrumb);
?>
<div class="layout_middle">
  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Store Video ?'); ?></h3>
          <p> 
            <?php echo $this->translate('Are you sure that you want to delete the store video titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->title, $this->timestamp($this->sitestorevideo->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' data-theme="b" ><?php echo $this->translate('Delete'); ?></button>
            	<div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	
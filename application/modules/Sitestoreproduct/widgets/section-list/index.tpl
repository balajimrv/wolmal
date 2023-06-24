<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$link = $this->url(array("tab" => $this->ProductTabId, 'section' => 2), 'sitestore_entry_view', false);
if ($this->countSection > 1)
  echo '<h3>' . $this->translate("Section List") . '</h3>';
else
  echo '<h3>' . $this->translate("Section Lists") . '</h3>';
?>
<ul class="sitestore_sidebar_list">
  <?php foreach ($this->sections as $section) : ?>  
    <li> 
      <div class="bold">
        <?php echo $this->translate($this->htmlLink(array('module' => 'sitestoreproduct', 'controller' => 'printing-tag', 'action' => 'show-products', 'route' => 'default', 'store_id' => $this->store_id, 'sectionId' => $section->section_id ), $this->translate($section->section_name.' ('.$section->count. ')'), array('class' => 'smoothbox', 'title' => $this->translate(array($section->section_name.' (%s)', $section->section_name.'(%s)', $section->count), $this->locale()->toNumber($section->count))))); ?>
			</div>
    </li>
  <?php endforeach; ?>
</ul>

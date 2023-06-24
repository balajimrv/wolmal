<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: splash-screen.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>

<h3 class="sm-page-head">
  <?php echo $this->translate('Splash Screen Settings'); ?>
  <span><a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'settings', 'action' => 'faq', 'faq_id' => 'faq_13'), 'admin_default', true) ?>/#faq_13" class="buttonlink icon_help" target="_blank"></a></span>   
  <span><a href="https://lh6.googleusercontent.com/-fEjA9Eaz9Gc/UbXAAIb4OxI/AAAAAAAAAXo/22Ryi8em74A/s450/mobile-Splash-Screen.jpg" target="_blank" title="View Screenshot" class="buttonlink sm_icon_view" ></a></span>
</h3>

<p>
  Use this area to upload an image for the mobile splash screen of your choice. You can view uploaded splash screen image’s preview by clicking on "Preview" link. Below, you can also add more splash screen images by using the "Add more" link.</p>
  	
<br/>
<!--If Splash screen exist then display the table and its content otherwise display the tip and link to add splash screen.-->
<?php if ($this->photoUrl): ?>
  <div class="admin_menus_options">
    <?php echo $this->htmlLink(array('reset' => false, 'action' => 'add-splash-screen'), $this->translate('Add more'), array('class' => 'buttonlink seaocore_icon_add smoothbox')) ?> 
  </div>
  <br />
  <table class='admin_table' width="1000">
    <thead>
      <tr>
        <th><?php echo $this->translate('Title'); ?></th>
        <th><?php echo $this->translate('Preview '); ?></th>
        <th><?php echo $this->translate('Size (px)'); ?></th>
        <th><?php echo $this->translate('Option'); ?></th>
      </tr> 
    </thead>
    <tbody>
      <?php foreach ($this->photoUrl as $key => $values): ?>
        <tr>
          <td><?php echo $values['title']; ?></td>
          <td><a href="<?php echo $values['photo_url']; ?>" target="_blank">preview</a></td>
          <td><?php echo $this->translate($key); ?></td>
          <td> <?php echo $this->htmlLink(array('reset' => false, 'action' => 'remove-splash-screen', 'file_id' => $values['file_id']), $this->translate('Delete'), array('class' => 'smoothbox')) ?></td>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>  
  <div class="tip">
    <span>
      <?php echo $this->translate('You have not uploaded any splash screen yet. Please '); ?>  <?php echo $this->htmlLink(array('reset' => false, 'action' => 'add-splash-screen'), $this->translate('click here '), array('class' => 'smoothbox')) ?><?php echo $this->translate('to add splash screen.')?>
    </span>
  </div>
<?php endif; ?>  

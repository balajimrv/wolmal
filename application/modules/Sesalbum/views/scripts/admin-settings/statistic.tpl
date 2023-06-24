<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: statistic.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesalbum/views/scripts/dismiss_message.tpl';?>
<h2>
  <?php echo $this->translate("Advanced Photos & Albums Plugin") ?>
</h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'settings', 'action' => 'contact-us'),'admin_default',true); ?>" class="request-btn">Feature Request</a>
</div>
<?php if( count($this->navigation) ): ?>
  <div class='sesbasic-admin-navgation'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate("Album Statistics") ?> </h3>
      <p class="description">
        <?php echo $this->translate("Below are some valuable statistics for the Albums/Photos created on this site:"); ?>
      </p>
      <table class='admin_table' style="width: 50%;">
        <tbody>
          <tr>
            <td><strong class="bold"><?php echo "Total Albums:" ?><strong></td>
            <td><?php echo $this->totalalbum; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Albums:" ?><strong></td>
            <td><?php echo $this->totalfeatured; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Albums:" ?><strong></td>
            <td><?php echo $this->totalsponsored; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Albums:" ?><strong></td>
            <td><?php echo $this->totalfavourite; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Rated Albums:" ?><strong></td>
            <td><?php echo $this->totalrated; ?></td>
          </tr>          
          <tr>
            <td><strong class="bold"><?php echo "Total Photos:" ?><strong></td>
            <td><?php echo $this->totalalbumphotos; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Photos:" ?><strong></td>
            <td><?php echo $this->totalfeaturedphotos; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Photos:" ?><strong></td>
            <td><?php echo $this->totalsponsoredphotos; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Photos:" ?><strong></td>
            <td><?php echo $this->totalfavouritephotos; ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Rated Photos:" ?><strong></td>
            <td><?php echo $this->totalratedphotos; ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </form>
</div>
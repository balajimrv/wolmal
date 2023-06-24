<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: startup.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<div>
  <section class="sitestoreproduct_startup_top">
  	<div class="sitestoreproduct_startup_inner">
      <div class="sitestoreproduct_startup_box_wrap">
        <div class="fleft">
          <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/startup_img.png" alt="">
        </div>
        <div class="o_hidden">
          <div class="sitestoreproduct_startup_box_lft">
            <div class="txt_center clr">
              <h2><?php echo $this->translate("Want to sell on") ?><img src="http://wolmal.com/public/admin/logo-worldmall.png" style="
    width: 200px;
    height: 32px;
    padding-left: 10px;
"></h2>
              <?php if( empty($this->notCreateStore) ) : ?>
                <?php echo $this->translate("Get your products discovered by our community and make more sales. Create your own online store and start selling today!"); ?>
              <?php endif; ?>
            </div>
            <?php if( empty($this->notCreateStore) ) : ?>
              <div class="sitestoreproduct_openstore_link mtop15 sitestore_startup_button dblock txt_center">
                <a class="sitestoreproduct_icon_plus" href="<?php echo $this->url(array("action"=>"index"), 'sitestore_packages', true); ?>"><b><?php echo $this->translate("Open a New Store") ?></b></a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <div class="widthfull sitestoreproduct_startup_background"></div>
  </section>
  <section class="sitestoreproduct_startup_links">
  	<div class="sitestoreproduct_startup_grid">
      <table>
        <tr>
          <?php if( !empty($this->pages[1]) ): ?>
          <?php $url = $this->url(array('action' => 'get-started'), "sitestoreproduct_general", false); ?>
            <td>
              <a href="<?php echo $url; ?>" class="fleft">
                <div>
                  <b class="mbot5 dblock"><?php echo $this->translate($this->pages[1]['title']); ?></b>
                  <p class="mbot5"><?php echo $this->translate($this->pages[1]['short_description']); ?></p>
                  <span class="txt_center o_hidden"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/getstart.png" alt=""></span>
                </div>
              </a>
            </td>
          <?php endif; ?>
          <?php if( !empty($this->pages[2]) ): ?>
          <?php $url = $this->url(array('action' => 'basic'), "sitestoreproduct_general", false); ?>
            <td>
              <a href="<?php echo $url ?>" class="fleft">
                <div>
                  <b class="mbot5 dblock"><?php echo $this->translate($this->pages[2]['title']); ?></b>
                  <p class="mbot5"><?php echo $this->translate($this->pages[2]['short_description']); ?></p>
                  <span class="txt_center o_hidden"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/Basics.png" alt=""></span>
                </div>
              </a>
            </td>
          <?php endif; ?>
          <?php if( !empty($this->pages[3]) ): ?>
          <?php $url = $this->url(array('action' => 'stories'), "sitestoreproduct_general", false); ?>
            <td>
              <a href="<?php echo $url; ?>" class="fleft">
                <div>
                  <b class="mbot5 dblock"><?php echo $this->translate($this->pages[3]['title']); ?></b>
                  <p class="mbot5"><?php echo $this->translate($this->pages[3]['short_description']); ?></p>
                  <span class="txt_center o_hidden"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/success-stories.png" alt=""></span>
                </div>
              </a>
            </td>
          <?php endif; ?>
          <?php if( !empty($this->pages[4]) ): ?>
          <?php $url = $this->url(array('action' => 'tools'), "sitestoreproduct_general", false); ?>
            <td>
              <a href="<?php echo $url; ?>" class="fleft">
                <div>
                  <b class="mbot5 dblock"><?php echo $this->translate($this->pages[4]['title']); ?></b>
                  <p class="mbot5"><?php echo $this->translate($this->pages[4]['short_description']); ?></p>
                  <span class="txt_center o_hidden"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/tools.png" alt=""></span>
                </div>
              </a>
            </td>
          <?php endif; ?>
        </tr>
      </table>
    </div>
  </section>
</div>
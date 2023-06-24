<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
  <!--  <div class="heading">
  <?php //echo $this->translate("%s Stores", $this->ownerObj->getTitle()); ?>
    </div>-->

  <div class='seaocore_members_popup_content' style="height: 330px;">

    <ul class="seaocore_browse_list">
      <?php
      foreach ($this->productsObj as $item): ?>
        <li <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
        if ($item->featured): ?>class="lists_highlight"<?php endif;
    endif; ?> >
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
              if ($item->featured): ?>
              <span title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></span>
            <?php endif;
          endif; ?>
          <div class='seaocore_browse_list_photo'>
            <?php
            echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'left')), array('target' => '_parent'));
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):
              if (!empty($item->sponsored)):
                $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
                if (!empty($sponsored)) :
                  ?>
                  <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
                    <?php echo $this->translate('SPONSORED'); ?>                 
                  </div>
                  <?php
                endif;
              endif;
            endif;
            ?>  
          </div>
          <div class='seaocore_browse_list_info'>
            <div class='sr_sitestoreproduct_browse_list_info_header o_hidden'>
              <div class="sr_sitestoreproduct_list_title">
                <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->title_truncation), array('title' => $item->getTitle(), 'target' => '_parent')); ?>
              </div>
              <div class="clear"></div>
            </div>

            <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
              <a target="_parent" href="<?php echo $this->url(array('category_id' => $item->category_id, 'categoryname' => $item->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                <?php echo $item->getCategory()->getTitle(true) ?>
              </a>
            </div>

            <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
              <?php
              echo $this->translate("Created On: %s", gmdate('M d,Y, g:i A', strtotime($item->start_date))) . '<br />';
              if (!empty($item->end_date)) :
                echo $this->translate("On Sale Till: %s", gmdate('M d,Y, g:i A', strtotime($item->end_date))) . '<br />';
              endif;
              echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) . '<br />';
              if (!empty($getIntrestedMemberCount)):
                echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
              endif;
              ?>
              <?php
              $productTitle = Engine_Api::_()->sitestoreproduct()->getProductTypeName($item->product_type);
              echo $productTitle;
              ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<div class="seaocore_members_popup_bottom">
  <button type='button' onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close"); ?></button>
</div>
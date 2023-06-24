<div class="global_form_popup admin_member_stats">
  <h3>Member Statistics</h3>
  <ul>
    <li>
      <?php echo $this->itemPhoto($this->user, 'thumb.icon', $this->user->getTitle()) ?>
    </li>
    <?php if( !empty($this->post_count) ): ?>
    <li>
      <?php echo $this->translate('Activity Feeds:') ?>
      <span><?php echo $this->translate($this->post_count) ?></span>
    </li>
    <?php endif; ?>
     <?php if( !empty($this->album_count) ): ?>
    <li>
      <?php echo $this->translate('Albums Created:') ?>
      <span><?php echo $this->translate($this->album_count) ?></span>
    </li>
    <?php endif; ?>
      <?php if( !empty($this->photo_count) ): ?>
    <li>
      <?php echo $this->translate('Photos Uploaded:') ?>
      <span><?php echo $this->translate($this->photo_count) ?></span>
    </li>
    <?php endif; ?>
   <?php if( !empty($this->video_count) ): ?>
    <li>
      <?php echo $this->translate('Videos Uploaded:') ?>
      <span><?php echo $this->translate($this->video_count) ?></span>
    </li>
    <?php endif; ?>
    <?php if( !empty($this->product_purchased) ): ?>
    <li>
      <?php echo $this->translate('Product Purchased:') ?>
      <span><?php echo $this->translate($this->product_purchased) ?></span>
    </li>
    <?php endif; ?>
    <?php if( !empty($this->like_count) ): ?>
    <li>
      <?php echo $this->translate('Likes:') ?>
      <span><?php echo $this->translate($this->like_count) ?></span>
    </li>
    <?php endif; ?>
    <?php if( !empty($this->share_count) ): ?>
    <li>
      <?php echo $this->translate('Shares:') ?>
      <span><?php echo $this->translate($this->share_count) ?></span>
    </li>
    <?php endif; ?>
    <?php if( !empty($this->comment_count) ): ?>
    <li>
      <?php echo $this->translate('Comments:') ?>
      <span><?php echo $this->translate($this->comment_count) ?></span>
    </li>
    <?php endif; ?>
  </ul>
  <br/>
  <button type="submit" onclick="parent.Smoothbox.close();return false;" name="close_button" value="Close">Close</button>
</div>

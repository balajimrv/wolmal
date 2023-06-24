

<div>
  
    <?php if($this->userpoints_enable_topusers): ?>
      <?php echo $this->translate('100016733') ?> <a href="<?php echo $this->url(array(),'topusers') ?>"> <span style="font-weight:bold"><?php if ($this->user_points_totalearned != 0 ): ?> <?php echo $this->user_rank ?> <?php else: ?> <?php echo $this->translate('100016738') ?> <?php endif; ?></span> </a> <br><br>
    <?php endif; ?>

    <?php echo $this->translate('100016734') ?> <a href="<?php echo $this->url(array(),'activitypoints_vault') ?>"> <span class='activitypoints_points' style="font-weight:bold" id="voter_points_count"><?php echo $this->user_points ?></span> <?php echo $this->translate('100016735') ?></a> <br><br>
    <?php echo $this->translate('100016736') ?> <a href="<?php echo $this->url(array(),'activitypoints_vault') ?>"> <span style="font-weight:bold" id="voter_points_count"><?php echo $this->user_points_totalearned ?></span> <?php echo $this->translate('100016735') ?></a> <br><br>

    <?php if ($this->userpoints_enable_pointrank): ?>
      <?php echo $this->translate('100016737') ?> <span style="font-weight:bold"><?php echo $this->user_rank_title ?></span> <br><br>

      <?php if(!empty($this->user_rank_next)) : ?>

      <div style="margin-bottom: 10px; margin-top: 10px">

      <div style='width: 155px; text-align: center; padding-bottom: 2px'>
      <?php echo $this->translate("For next rank I need") ?>
      </div>
      
      <div class="activitypoints-progress-wrap">
        <div class="activitypoints-progress-value" style="width: <?php echo sprintf("%d", $this->user_rank_next['rank_diff_pct']) ?>%;">
          <div class="activitypoints-progress-text">
            <?php echo $this->user_rank_next['rank_diff'] ?> <?php echo $this->translate("points") ?>
            <br />
          </div>
        </div>
      </div>
      
      <br>
      </div>

      <?php endif; ?>

    <?php endif; ?>

    <?php if($this->userpoints_enable_offers): ?>
    <a href="<?php echo $this->url(array(),'activityrewards_earn') ?>"><?php echo $this->translate('100016739') ?></a>
    <span style="pading-left: 4px; padding-right: 4px; color: #CCC"> | </span>
    <?php endif; ?>
    <?php if($this->userpoints_enable_shop): ?>
    <a href="<?php echo $this->url(array(),'activityrewards_spend') ?>"><?php echo $this->translate('100016740') ?></a>
    <?php endif; ?>

</div>
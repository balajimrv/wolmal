
<ul>
  <?php foreach( $this->items as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'topuserswidget_thumb')) ?>
      <div class='topuserswidget_info'>
        <div class='topuserswidget_name'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class='topuserswidget_points'>
          <img style="float: left; margin-right: 5px; border: 0px" src="application/modules/Activitypoints/externals/images/userpoints_coins16.png">
          <?php echo $this->translate(array('%s point', '%s points', $user->points),$this->locale()->toNumber($user->points)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

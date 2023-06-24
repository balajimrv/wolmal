<h2><?php echo $this->translate("Activity Points Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("Find some quick answers on this page") ?>
</p>

<br />


<div class="admin_statistics">

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('activitypoints_faq_1').style.display=='block') ? $('activitypoints_faq_1').style.display='none' : $('activitypoints_faq_1').style.display='block';">
    <?php echo $this->translate('Resetting all points') ?>
  </div>
  <div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 40px' id='activitypoints_faq_1'>
    <a href="<?php echo $this->url(array('action' => 'reset')) ?>">Click here</a> if you would like to clear/reset ALL points for ALL users.
  </div>

  <!--<div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('activitypoints_faq_2').style.display=='block') ? $('activitypoints_faq_2').style.display='none' : $('activitypoints_faq_2').style.display='block';">-->
  <!--  <?php echo $this->translate('Q') ?>-->
  <!--</div>-->
  <!--<div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 40px' id='activitypoints_faq_2'>-->
  <!--  <?php echo $this->translate('A') ?>-->
  <!--</div>-->

</div>

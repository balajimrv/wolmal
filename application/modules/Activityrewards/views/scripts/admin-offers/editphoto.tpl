
<h2>
  <a href="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'settings','action'=>'index'), 'admin_default',true) ?>"><?php echo $this->translate("Activity Points Plugin") ?></a> &raquo; <a href="<?php echo $this->url(array('module'  => 'activityrewards', 'controller' => 'offers','action'=>'index'), 'admin_default',true) ?>"><?php echo $this->translate("Offers") ?></a> &raquo; <?php echo $this->translate("Edit Offer Photo") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>





<div class='clear'>
  <div class='settings'>

      <div class="global_form" id="admin_settings_form">
      <?php if ($this->form->saved_successfully): ?><h3 class="slowfade"><?php echo $this->translate("Settings were saved successfully.") ?></h3><?php endif; ?>

      <form method="post" action="<?php echo $this->url(array('action'=>'editphoto'), 'admin_default') ?>" class="global_form" enctype="multipart/form-data">
        <div>
          <div>
            <h3><?php echo $this->form->getTitle() ?></h3>

            <p class="form-description"><?php echo $this->form->getDescription() ?></p>

            <div class="form-elements">

              <div class="form-wrapper" id="photo-wrapper">
                <div class="form-label" id="photo-label">
                  <label class="optional" for="photo"><?php echo $this->translate("Current Photo") ?></label>
                </div>

                <div class="form-element" id="photo-element" style="text-align: center">
                  <?php echo $this->itemPhoto($this->item, 'thumb.normal')  ?>
                  <?php if($this->item->getPhoto() != '') : ?>
                  <br><br>
                  <a href="<?php echo $this->url(array('action'=>'editphoto','task' =>'remove'), 'admin_default') ?>"><?php echo $this->translate("[remove photo]") ?></a>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-wrapper" id="photo-wrapper">
                <div class="form-label" id="photo-label">
                  <label class="optional" for="photo"><?php echo $this->translate("Replace With") ?></label>
                </div>

                <div class="form-element" id="photo-element">
                  <input type="hidden" id="MAX_FILE_SIZE" value="<?php echo $this->form->photo->getMaxFileSize() ?>" name="MAX_FILE_SIZE">
                  <input type="file" id="photo" name="photo">
                </div>
              </div>

              <div class="form-wrapper" id="submit-wrapper">
                <div class="form-label" id="submit-label">
                  &nbsp;
                </div>

                <div class="form-element" id="submit-element">
                  <button type="submit" id="submit" name="submit"><?php echo $this->translate("Save Changes") ?></button>
                </div>
              </div><input type="hidden" id="item_id" value="<?php echo $this->form->item_id->getValue() ?>" name="item_id">
            </div>
          </div>
        </div>
      </form>
      
      </div>

  </div>
</div>


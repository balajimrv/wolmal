<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: super-editor.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<form method="post" class="global_form_popup">
  <?php if($this->super_editor): ?>
     <div>
      <p>
        <?php echo $this->translate("Please select another editor as Super Editor to remove this editor from Super Editor.") ?>
      </p>
      <br />
      <p>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo $this->translate("Close") ?>
        </a>
      </p>
    </div> 
  <?php else: ?>
    <div>
      <h3><?php echo $this->translate("Make Super Editor?") ?></h3>

        <p>
          <?php echo $this->translate("Are you sure you want to make this editor as Super Editor? (Note:  At any time only one Editor can be made Super Editor. Thus, if you make this editor as Super Editor, then the current Super Editor will be removed.)") ?>
        </p>     
      
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->editor_id ?>"/>
        <button type='submit'><?php echo $this->translate("Make Super Editor") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo $this->translate("cancel") ?>
        </a>
      </p>
    </div>
  <?php endif; ?>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
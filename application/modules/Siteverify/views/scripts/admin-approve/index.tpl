<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">

  function multiDelete()
  {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected verify entries?'); ?>");
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<h2>
  <?php echo $this->translate('Members Verification Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Approve Verifications') ?>
</h3>
<p>
  <?php echo $this->translate("This page lists all member verifications that require administrator approval. You can use this page to approve these verification entries and delete if necessary.") ?>
</p>
<br />
<br />

<?php if (count($this->paginator)): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete();">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'>ID</th>
          <th><?php echo $this->translate("User") ?></th>
          <th><?php echo $this->translate("Verified By") ?></th>
          <th><?php echo $this->translate("Verify Count") ?></th>
          <th><?php echo $this->translate("Comments") ?></th>
          <th><?php echo $this->translate("Verified Date") ?></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($this->paginator as $item):
          // OBJECTS OF USERS ACCORDING TO RESOURCE ID AND POSTER ID
          $resource = Engine_Api::_()->getItem('user', $item->resource_id);
          $poster = Engine_Api::_()->getItem('user', $item->poster_id);
          $verifyTable = Engine_Api::_()->getDbtable('verifies', 'siteverify');
          ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
            <td><?php echo $item->getIdentity(); ?></td>
            <td><?php
              echo $this->htmlLink($resource->getHref(), $this->itemPhoto($resource, 'thumb.icon')) . "<br />";
              echo $this->htmlLink($resource->getHref(), $resource->getTitle(), array('target' => "_blank"))
              ?> </td>
            <td><?php
              echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon')) . "<br />";
              echo $this->htmlLink($poster->getHref(), $poster->getTitle(), array('target' => "_blank"))
              ?> </td>
            <td><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $item->resource_id, 'resource_type' => $item->resource_type), $verifyTable->getVerifyCount($item->resource_id), array('class' => 'smoothbox')); ?></td>
            <td><?php
              $verify_comments = Engine_Api::_()->seaocore()->seaddonstruncateTitle($item->comments);
              if ($verify_comments):
                ?>
                <span title="<?php echo $item->comments; ?>"> <?php echo $verify_comments; ?></span>
                <?php
              else :
                echo $this->translate("---");
              endif;
              ?></td>
            <td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteverify', 'controller' => 'approve', 'action' => 'approve', 'id' => $item->verify_id), $this->translate('approve'), array('class' => 'smoothbox')) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteverify', 'controller' => 'approve', 'action' => 'edit', 'id' => $item->verify_id), $this->translate('edit'), array('class' => 'smoothbox')) ?>
              |
              <?php
              echo $this->htmlLink(
                      array('route' => 'admin_default', 'module' => 'siteverify', 'controller' => 'approve', 'action' => 'delete', 'id' => $item->verify_id), $this->translate("delete"), array('class' => 'smoothbox'))
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br/>
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
  <br/>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no pending verification requests.") ?>
    </span>
  </div>
<?php endif; ?>

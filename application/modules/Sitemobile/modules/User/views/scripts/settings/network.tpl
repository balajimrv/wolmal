<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: network.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<script type="text/javascript">

  function joinNetwork(network_id)
  {
    $('#join_id').val(network_id);
    $('#network-form').submit();
  }

  function leaveNetwork(network_id)
  {
    $('#leave_id').val( network_id );
    $('#network-form').submit();
  }

</script>
<div class="ui-page-content">
  <h3 class="sm-ui-cont-cont-heading"><?php echo $this->translate('My Networks'); ?></h3>
  <div class="sm-ui-cont-cont-des">
    <?php echo $this->translate(array('You belong to %s network.', 'You belong to %s networks.', count($this->networks)), $this->locale()->toNumber(count($this->networks))) ?>
  </div>
  <ul id='current_networks' class='networks'>
    <?php foreach ($this->networks as $network): ?>
      <li>
        <div>
          <?php echo $network->title ?> <span>(<?php echo $this->translate(array('%s member.', '%s members.', $network->membership()->getMemberCount()), $this->locale()->toNumber($network->membership()->getMemberCount())) ?>)</span>
        </div>
        <?php if ($network->assignment == 0): ?>
          <a href='javascript:void(0);' onclick="leaveNetwork(<?php echo $network->network_id; ?>)"><?php echo $this->translate('Leave Network'); ?></a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <h3 class="sm-ui-cont-cont-heading"><?php echo $this->translate('Available Networks'); ?></h3>
  <?php if (!empty($this->network_suggestions)): ?>
    <div id='avaliable_networks'>
      <?php echo $this->form->setAttrib('data-ajax', 'true')->render($this) ?>
    </div>

          <!--<ul class='networks' data-role="listview" data-filter="true" data-filter-placeholder='<?php //echo $this->translate("Search...") ?>' data-inset="true" data-icon="false">-->
    <ul class='networks'>
      <?php foreach ($this->network_suggestions as $network): ?>
        <li>
          <div>
            <?php echo $network->title ?>
            <span>(<?php echo $this->translate(array('%s member.', '%s members.', $network->membership()->getMemberCount()), $this->locale()->toNumber($network->membership()->getMemberCount())) ?>)</span>
          </div>
          <?php if ($network->assignment == 0): ?>
            <a href='javascript:void(0);' onclick="joinNetwork(<?php echo $network->network_id; ?>)">
              <?php echo $this->translate('Join Network'); ?>
            </a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php else: ?>
    <div class="tip">
      <span><?php echo $this->translate('There are currently no avaliable networks to join.'); ?></span>
    </div>

    <div style='display:none;'>
      <?php echo $this->form->render($this) ?>
    </div>
  <?php endif; ?>
</div>
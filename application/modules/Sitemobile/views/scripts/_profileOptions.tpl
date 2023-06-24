<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _pageContent.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$row = $this->row;
$subject = $this->subject();
$subject_type = $subject->getType();
$viewer = $this->viewer();
?>

<ul class="user-socialparticipation">
    <?php //REQUEST GROUP ?>
    <li class="<?php echo $subject_type;?>_request" style='display: none;'>
      <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions('.<?php echo $subject_type;?>_cancelrequest', '.<?php echo $subject_type;?>_request',  '<?php echo $this->url(array('controller' => 'member', 'action' => 'request', $subject_type.'_id' => $subject->getIdentity()), $subject_type.'_extended', true);?>')">
        <?php echo $this->translate('Request Membership'); ?>
      </a>
    </li>
    
    <?php //JOIN GROUP ?>
    
    <li class="<?php echo $subject_type;?>_join" style='display: none;'>
      <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions('.<?php echo $subject_type;?>_leave', '.<?php echo $subject_type;?>_join', '<?php echo $this->url(array('controller' => 'member', 'action' => 'join', $subject_type.'_id' => $subject->getIdentity(), 'direct_join'=>true), $subject_type.'_extended', true);?>')">
          <?php echo $this->translate('Join'); ?>
      </a>
    </li>
    
    <?php //Leave GROUP ?>
    
    <li class="<?php echo $subject_type;?>_leave" style='display: none;'>
      <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions('.<?php echo $subject_type;?>_join', '.<?php echo $subject_type;?>_leave', '<?php echo $this->url(array('controller' => 'member', 'action' => 'leave', $subject_type.'_id' => $subject->getIdentity(),'direct_leave'=>true), $subject_type.'_extended', true);?>')">
        <?php echo $this->translate('Leave'); ?>
      </a> 
    </li>
    
    
    <?php //CANCEL REQUEST ?>
    
    <li class="<?php echo $subject_type;?>_cancelrequest" style='display: none;'>
      <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions('.<?php echo $subject_type;?>_request', '.<?php echo $subject_type;?>_cancelrequest', '<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', $subject_type.'_id' => $subject->getIdentity()), $subject_type.'_extended', true);?>')">
        <?php echo $this->translate('Cancel Request'); ?>
      </a>
    </li>
    
    
    
    <?php //ACCEPT REQEUST?>
    
    <li class="accept-ignore" style='display: none;'>
     <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions( '.<?php echo $subject_type;?>_leave', '.accept-ignore', '<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', $subject_type.'_id' => $subject->getIdentity()), $subject_type.'_extended', true);?>')">
        <?php echo $this->translate('Accept Request'); ?>
      </a>
    </li>
    
    <?php //IGNORE REQEUST?>
    
    <li class="accept-ignore" style='display: none;'>
     <a class="ui-link" href="#" onclick="sm4app.core.Module.core.profileOptions( '.accept-ignore', '.accept-ignore', '<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'group_id' => $subject->getIdentity()), 'group_extended', true);?>')">
        <?php echo $this->translate('Ignore Request'); ?>
      </a>
    </li>

    <?php 
    $showContainer = '';
    if (null === $row) { ?>     

      <?php if ($subject->membership()->isResourceApprovalRequired()) {  // REQUEST GROUP?>
        <?php
        $showContainer = '.' . $subject_type. '_request';
        ?>
      <?php } else { //JOIN <?php echo $subject_type;?>

        <?php
        $showContainer = '.' . $subject_type. '_join';
        ?>
      <?php } ?> 
<?php } else if ($row->active) { //LEAVE <?php echo $subject_type;?> 

      <?php if (!$subject->isOwner($viewer)) { ?>
        <?php
        $showContainer = '.' . $subject_type. '_leave';
        ?>
      <?php } ?>
    <?php } else if (!$row->resource_approved && $row->user_approved) { //CANCEL REQEUST ?>
      <?php $showContainer = '.' . $subject_type. '_cancelrequest';?>
    <?php } else if (!$row->user_approved && $row->resource_approved) { //ACCEPT OR IGNORE MEMBERSHIP REQEUST?>
      <?php
      $showContainer = '.accept-ignore';
      ?>
    <?php } ?>
<?php if ($subject->authorization()->isAllowed($viewer, 'invite')) { //INVITE MEMBERS?>
      <li class="invitemembers">
        <a class="ui-link" href='<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', $subject_type.'_id' => $subject->getIdentity()), $subject_type . '_extended', true);?>'>
          <?php echo $this->translate('Invite'); ?>
        </a>
      </li>
<?php } else { ?>
  
    <li class="invitemembers" style='display:none;'>
        <a class="ui-link" href='<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', $subject_type.'_id' => $subject->getIdentity()), $subject_type.'_extended', true);?>'>
          <?php echo $this->translate('Invite'); ?>
        </a>
     </li>
<?php } ?>
  <?php //SHARE GROUP ?>  
    <li>
      <a class="ui-link" href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), 'default', true);?>">
         
        <?php echo $this->translate('Share'); ?>
      </a>
    </li>
  </ul>

<script type="text/javascript">
 <?php if($showContainer):?>
  sm4.core.runonce.add(function() {
   $.mobile.activePage.find("<?php echo $showContainer;?>").css('display', 'table-cell');
   
 });
 <?php endif;?>
</script>  
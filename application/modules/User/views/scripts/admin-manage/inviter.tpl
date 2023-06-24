
<div class="global_form_popup admin_member_stats">
  <h3>Inviter Details</h3>
  <?php if( count($this->inviter) ): ?>
  <ul>
    
    <?php if(!empty($this->inviter[0]['displayname'])): ?>
    
    <li>
      <?php echo $this->translate('Inviter Name:') ?>
      <span><?php if($this->inviter[0]['displayname'])echo $this->inviter[0]['displayname']; ?></span>
    </li>
   <li>
      <?php echo $this->translate('User ID:') ?>
      <span><?php echo $this->inviter[0]['user_id']; ?></span>
    </li>
    
    <li>
      <?php echo $this->translate('User Level:') ?>
      <span><?php echo $userLevel = Engine_Api::_()->getItem('authorization_level', $this->inviter[0]['level_id']); ?></span>
    </li>
    <li>
      <?php echo $this->translate('Email ID:') ?>
      <span><?php echo $this->inviter[0]['email']; ?></span>
    </li>
    <?php else: ?>
    <li>No Inviter Found!</li>
    <?php endif; ?>
  </ul>
  <br/>
  <h3>Previously Invited by:</h3>
  <table class="admin_table" style="width:100%">
      <thead>
          <tr>
              <th>Member ID</th>
              <th>Name</th>
              <th>Date</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach( $this->previous_inviter as $item ): ?>
              <tr>
                  <td><?php echo $item['user_id']; ?></td>
                  <td><?php echo $item['displayname']; ?></td>
                  <td><?php echo $this->locale()->toDateTime($item['referred_date']); ?></td>
              </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  <?php else: ?>
    <p>No inviter found!</p>
  <?php endif; ?>
  <br>
  <button type="submit" onclick="parent.Smoothbox.close();return false;" name="close_button" value="Close">Close</button>
</div>
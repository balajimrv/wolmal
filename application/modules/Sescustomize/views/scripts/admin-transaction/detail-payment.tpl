<div class="sesbasic_view_stats_popup">
  <h3>Payment Information</h3>
  <table>
  	<tr>
      <td><?php echo $this->translate('User Id') ?>:</td>
      <td><?php echo $this->payment->user_id; ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('User') ?>:</td>
      <td><?php echo  Engine_Api::_()->getItem('user',$this->payment->user_id); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Amount') ?>:</td>
      <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->payment->amount) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Note') ?>:</td>
      <td><?php echo $this->payment->note ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Bank Name') ?>:</td>
      <td><?php echo $this->payment->bank_name ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('IFSC Code') ?>:</td>
      <td><?php echo ($this->payment->ifsc_code) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Account Number') ?>:</td>
      <td><?php echo ($this->payment->account_number) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Account Holder Name') ?>:</td>
      <td><?php echo ($this->payment->account_holder_name) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Mobile Number') ?>:</td>
      <td><?php echo ($this->payment->monile_number) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Admin Note') ?>:</td>
      <td><?php echo ($this->payment->admin_note) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Statusr') ?>:</td>
      <td><?php echo ($this->payment->status == 0 ? "Not processed." : "Processed") ?></td>
    </tr>
     <tr>
      <td><?php echo $this->translate('Date') ?>:</td>
      <td><?php echo $this->payment->creation_date; ;?></td>
    </tr>
  </table>
  <br />
  <button onclick='javascript:parent.Smoothbox.close()'>
    <?php echo $this->translate("Close") ?>
  </button>
</div>
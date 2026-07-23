<?php $active_account_tab = 'info'; $this->load->view('dyafa/partials/account_tabs', array('account' => $account, 'active_account_tab' => $active_account_tab)); ?>
<div class="dso-card">
    <table class="dso-table">
        <tr><th>Industry</th><td><?php echo htmlspecialchars($account->industry); ?></td></tr>
        <tr><th>City</th><td><?php echo htmlspecialchars($account->city); ?></td></tr>
        <tr><th>Contact</th><td><?php echo htmlspecialchars($account->primary_contact_person); ?> (<?php echo htmlspecialchars($account->primary_contact_mobile); ?>)</td></tr>
        <tr><th>Status</th><td><?php echo $account->status; ?></td></tr>
    </table>
</div>

<div class="dso-card">
    <h3>Add Activity</h3>
    <?php if ($this->session->flashdata('dso_login_error')): ?><div class="dso-alert error"><?php echo $this->session->flashdata('dso_login_error'); ?></div><?php endif; ?>
    <form class="dso-form" method="post" action="<?php echo base_url('dyafa/accounts/add_activity/' . $account->id); ?>">
        <label>Type</label>
        <select name="activity_type">
            <?php foreach (array('Call','Meeting','Visit','FollowUp','Reservation','Collection','Complaint','Opportunity') as $t): ?>
            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Date/Time</label>
        <input type="datetime-local" name="activity_date" required>
        <label>Notes</label>
        <textarea name="notes" rows="2" required></textarea>
        <br><button type="submit" class="dso-btn">Log Activity</button>
    </form>
</div>

<a class="dso-btn" href="<?php echo base_url('dyafa/accounts'); ?>">Back to All Accounts</a>

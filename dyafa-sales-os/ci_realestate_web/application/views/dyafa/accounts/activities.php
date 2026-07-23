<?php $active_account_tab = 'activities'; $this->load->view('dyafa/partials/account_tabs', array('account' => $account, 'active_account_tab' => $active_account_tab)); ?>

<div class="dso-card">
    <h3>Full Activity Log</h3>
    <table class="dso-table">
        <tr><th>Date</th><th>Type</th><th>Notes</th></tr>
        <?php if (empty($activities)): ?>
        <tr><td colspan="3">No activities logged for this account.</td></tr>
        <?php else: foreach ($activities as $a): ?>
        <tr><td><?php echo $a->activity_date; ?></td><td><?php echo $a->activity_type; ?></td><td><?php echo htmlspecialchars($a->notes); ?></td></tr>
        <?php endforeach; endif; ?>
    </table>
</div>

<a class="dso-btn" href="<?php echo base_url('dyafa/accounts/view/' . $account->id); ?>">Add / Manage Activities</a>
<a class="dso-btn secondary" href="<?php echo base_url('dyafa/accounts'); ?>">All Accounts</a>

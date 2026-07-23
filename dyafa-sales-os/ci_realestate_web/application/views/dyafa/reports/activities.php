<h2>Activities Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/activities?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/activities'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>By Type</h3>
    <table class="dso-table"><tr><th>Type</th><th>Count</th></tr>
    <?php foreach ($by_type as $r): ?><tr><td><?php echo $r->activity_type; ?></td><td><?php echo $r->cnt; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>All Activities</h3>
    <table class="dso-table">
        <tr><th>Account</th><th>Type</th><th>Notes</th><th>Date</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr><td><?php echo $r->account_id; ?></td><td><?php echo $r->activity_type; ?></td><td><?php echo htmlspecialchars(mb_substr((string) $r->notes, 0, 80)); ?></td><td><?php echo $r->activity_date; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

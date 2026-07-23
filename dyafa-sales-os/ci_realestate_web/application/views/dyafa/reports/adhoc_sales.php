<h2>Adhoc Sales Report <a class="dso-btn" href="<?php echo site_url('dyafa/reports/adhoc_sales?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/adhoc_sales'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>By Status</h3>
    <table class="dso-table"><tr><th>Status</th><th>Count</th></tr>
    <?php foreach ($by_status as $r): ?><tr><td><?php echo $r->status; ?></td><td><?php echo $r->cnt; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>All Adhoc Sales</h3>
    <table class="dso-table">
        <tr><th>Event Type</th><th>Event Date</th><th>Pax</th><th>Est. Value</th><th>Status</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr><td><?php echo $r->event_type; ?></td><td><?php echo $r->event_date; ?></td><td><?php echo $r->pax; ?></td><td><?php echo number_format($r->estimated_value, 2); ?></td><td><?php echo $r->status; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

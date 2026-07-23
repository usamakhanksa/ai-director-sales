<h2>Daily Sales Report - <?php echo date('Y-m-d'); ?>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/daily_sales'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>Today's Reservations</h3>
    <table class="dso-table">
        <tr><th>Account</th><th>Property</th><th>Total</th><th>Status</th></tr>
        <?php foreach ($reservations as $r): ?>
        <tr><td><?php echo $r->account_id; ?></td><td><?php echo htmlspecialchars($r->property); ?></td><td><?php echo number_format($r->total_amount, 2); ?></td><td><?php echo $r->status; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Today's Adhoc Sales</h3>
    <table class="dso-table">
        <tr><th>Event Type</th><th>Est. Value</th><th>Status</th></tr>
        <?php foreach ($adhoc as $a): ?>
        <tr><td><?php echo $a->event_type; ?></td><td><?php echo number_format($a->estimated_value, 2); ?></td><td><?php echo $a->status; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Collected Today (approx.)</h3>
    <p><b><?php echo number_format($collected_today, 2); ?></b></p>
</div>

<h2>Revenue Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/revenue?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/revenue'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>By Month</h3>
    <table class="dso-table"><tr><th>Month</th><th>Total</th></tr>
    <?php foreach ($by_month as $r): ?><tr><td><?php echo $r->ym; ?></td><td><?php echo number_format($r->total, 2); ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>By Property</h3>
    <table class="dso-table"><tr><th>Property</th><th>Total</th></tr>
    <?php foreach ($by_property as $r): ?><tr><td><?php echo htmlspecialchars($r->property); ?></td><td><?php echo number_format($r->total, 2); ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>By Account</h3>
    <table class="dso-table"><tr><th>Account</th><th>Total</th></tr>
    <?php foreach ($by_account as $r): ?><tr><td><?php echo htmlspecialchars($r->company_name); ?></td><td><?php echo number_format($r->total, 2); ?></td></tr><?php endforeach; ?>
    </table>
</div>

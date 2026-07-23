<h2>Leads Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/leads?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/leads'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>By Status</h3>
    <table class="dso-table"><tr><th>Status</th><th>Count</th></tr>
    <?php foreach ($by_status as $r): ?><tr><td><?php echo $r->status; ?></td><td><?php echo $r->cnt; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>By Category</h3>
    <table class="dso-table"><tr><th>Category</th><th>Count</th></tr>
    <?php foreach ($by_category as $r): ?><tr><td><?php echo $r->lead_category; ?></td><td><?php echo $r->cnt; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>By Owner &amp; Status</h3>
    <table class="dso-table"><tr><th>Owner</th><th>Status</th><th>Count</th></tr>
    <?php foreach ($by_owner as $r): ?><tr><td><?php echo htmlspecialchars($r->owner_name); ?></td><td><?php echo $r->status; ?></td><td><?php echo $r->cnt; ?></td></tr><?php endforeach; ?>
    </table>
</div>

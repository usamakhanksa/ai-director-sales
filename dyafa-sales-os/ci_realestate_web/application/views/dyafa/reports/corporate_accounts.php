<h2>Corporate Accounts Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/corporate_accounts?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/corporate_accounts'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Company</th><th>Industry</th><th>City</th><th>Status</th><th>VIP</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r->company_name); ?></td>
            <td><?php echo htmlspecialchars((string) $r->industry); ?></td>
            <td><?php echo htmlspecialchars((string) $r->city); ?></td>
            <td><?php echo $r->status; ?></td>
            <td><?php echo $r->is_vip ? 'Yes' : ''; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<h2>Contract Renewal Report (expiring within 60 days) <a class="dso-btn" href="<?php echo site_url('dyafa/reports/contract_renewals?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/contract_renewals'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Contract #</th><th>Company</th><th>Expiry</th><th>Account Manager</th><th>Status</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r->contract_number); ?></td>
            <td><?php echo htmlspecialchars($r->company_name); ?></td>
            <td><?php echo $r->expiry_date; ?></td>
            <td><?php echo $r->account_manager_id; ?></td>
            <td><?php echo $r->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

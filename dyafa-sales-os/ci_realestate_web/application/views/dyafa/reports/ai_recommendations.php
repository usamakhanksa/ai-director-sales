<h2>AI Recommendation Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/ai_recommendations?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/ai_recommendations'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Account</th><th>Type</th><th>Priority</th><th>Est. Revenue</th><th>Status</th><th>Created</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo $r->account_id; ?></td>
            <td><?php echo $r->type; ?></td>
            <td><?php echo $r->priority; ?></td>
            <td><?php echo number_format($r->estimated_revenue, 2); ?></td>
            <td><?php echo $r->status; ?></td>
            <td><?php echo $r->created_at; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

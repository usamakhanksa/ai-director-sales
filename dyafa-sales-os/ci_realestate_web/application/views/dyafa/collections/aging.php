<h2>Collections Aging Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/aging?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/aging'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<table class="dso-table">
<tr><th>Account</th><th>0-30</th><th>31-60</th><th>61-90</th><th>90+</th><th>Total Outstanding</th></tr>
<?php foreach ($buckets as $b): ?>
<tr>
    <td><?php echo htmlspecialchars($b->company_name); ?></td>
    <td><?php echo number_format($b->b_0_30, 2); ?></td>
    <td><?php echo number_format($b->b_31_60, 2); ?></td>
    <td><?php echo number_format($b->b_61_90, 2); ?></td>
    <td><?php echo number_format($b->b_90_plus, 2); ?></td>
    <td><b><?php echo number_format($b->total_outstanding, 2); ?></b></td>
</tr>
<?php endforeach; ?>
</table>
<a class="dso-btn" href="<?php echo base_url('dyafa/collections'); ?>">Back</a>

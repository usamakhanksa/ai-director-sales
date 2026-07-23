<h2>Reservations Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/reservations?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/reservations'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>ID</th><th>Account</th><th>Property</th><th>Check-in</th><th>Check-out</th><th>Room Nights</th><th>Total</th><th>Status</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo $r->id; ?></td>
            <td><?php echo $r->account_id; ?></td>
            <td><?php echo htmlspecialchars($r->property); ?></td>
            <td><?php echo $r->check_in; ?></td>
            <td><?php echo $r->check_out; ?></td>
            <td><?php echo $r->room_nights; ?></td>
            <td><?php echo number_format($r->total_amount, 2); ?></td>
            <td><?php echo $r->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

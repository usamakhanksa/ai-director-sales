<h2>Property Performance Report
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/property_performance?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/property_performance'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Property</th><th>City</th><th>Reservations</th><th>Room Nights</th><th>Revenue</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r->name); ?></td>
            <td><?php echo htmlspecialchars((string) $r->city); ?></td>
            <td><?php echo $r->total_reservations; ?></td>
            <td><?php echo $r->total_room_nights; ?></td>
            <td><?php echo number_format($r->total_revenue, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

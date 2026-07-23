<h2>Room Nights Report <a class="dso-btn" href="<?php echo site_url('dyafa/reports/room_nights?export=csv'); ?>">Export CSV</a>
    <?php if (in_array($this->session->userdata('dso_role'), $this->config->item('dso_hod_roles'), true)): ?>
    <a class="dso-btn" href="<?php echo site_url('dyafa/reports/push_to_reporting/room_nights'); ?>">Push to Reporting Platform</a>
    <?php endif; ?>
</h2>

<div class="dso-card">
    <h3>By Property</h3>
    <table class="dso-table"><tr><th>Property</th><th>Total Room Nights</th></tr>
    <?php foreach ($by_property as $r): ?><tr><td><?php echo htmlspecialchars($r->property); ?></td><td><?php echo $r->total_room_nights; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>By Month</h3>
    <table class="dso-table"><tr><th>Month</th><th>Total Room Nights</th></tr>
    <?php foreach ($by_month as $r): ?><tr><td><?php echo $r->ym; ?></td><td><?php echo $r->total_room_nights; ?></td></tr><?php endforeach; ?>
    </table>
</div>

<h2>Welcome, <?php echo htmlspecialchars($account->company_name); ?></h2>
<div class="dso-card">
    <p>Outstanding balance: <b><?php echo number_format($outstanding, 2); ?></b></p>
    <a class="dso-btn" href="<?php echo base_url('dyafa/portal/search'); ?>">Search Hotels</a>
    <a class="dso-btn" href="<?php echo base_url('dyafa/portal/reservations'); ?>">My Reservations</a>
    <a class="dso-btn" href="<?php echo base_url('dyafa/portal/reservation_new'); ?>">New Reservation</a>
    <a class="dso-btn" href="<?php echo base_url('dyafa/portal/statement'); ?>">Statement</a>
    <?php
    $capabilities = $this->config->item('dso_corporate_capabilities');
    $my_capabilities = isset($capabilities[$this->session->userdata('dso_role')]) ? $capabilities[$this->session->userdata('dso_role')] : array();
    if (in_array('manage_users', $my_capabilities, true)):
    ?>
    <a class="dso-btn" href="<?php echo base_url('dyafa/portal/users'); ?>">Company Users</a>
    <?php endif; ?>
</div>
<div class="dso-card">
    <h3>Recent Reservations</h3>
    <table class="dso-table">
        <tr><th>Property</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th></tr>
        <?php foreach (array_slice($reservations, 0, 5) as $r): ?>
        <tr><td><?php echo htmlspecialchars($r->property); ?></td><td><?php echo $r->check_in; ?></td><td><?php echo $r->check_out; ?></td><td><?php echo number_format($r->total_amount, 2); ?></td><td><?php echo $r->status; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

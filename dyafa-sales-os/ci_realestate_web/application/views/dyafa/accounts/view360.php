<?php $active_account_tab = '360'; $this->load->view('dyafa/partials/account_tabs', array('account' => $account, 'active_account_tab' => $active_account_tab)); ?>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Industry</th><td><?php echo htmlspecialchars($account->industry); ?></td></tr>
        <tr><th>City</th><td><?php echo htmlspecialchars($account->city); ?></td></tr>
        <tr><th>Contact</th><td><?php echo htmlspecialchars($account->primary_contact_person); ?> (<?php echo htmlspecialchars($account->primary_contact_mobile); ?>)</td></tr>
        <tr><th>Status</th><td><?php echo $account->status; ?></td></tr>
        <tr><th>VIP</th><td><?php echo $account->is_vip ? 'Yes' : 'No'; ?></td></tr>
    </table>
</div>

<div class="dso-card">
    <h3>Contract Summary</h3>
    <table class="dso-table">
        <tr><th>Contract #</th><th>Status</th><th>Start</th><th>Expiry</th><th>Credit Limit</th><th>Payment Terms</th></tr>
        <?php if (empty($contracts)): ?>
        <tr><td colspan="6">No contracts on file for this account.</td></tr>
        <?php else: foreach ($contracts as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c->contract_number); ?></td>
            <td><span class="dso-badge"><?php echo htmlspecialchars($c->status); ?></span></td>
            <td><?php echo htmlspecialchars($c->start_date); ?></td>
            <td><?php echo htmlspecialchars($c->expiry_date); ?></td>
            <td><?php echo number_format($c->credit_limit, 2); ?></td>
            <td><?php echo htmlspecialchars($c->payment_terms); ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Reservations</h3>
    <table class="dso-table">
        <tr><th>Property</th><th>Status</th><th>Room Nights</th><th>Total Amount</th><th>Created</th></tr>
        <?php if (empty($reservations)): ?>
        <tr><td colspan="5">No reservations for this account.</td></tr>
        <?php else: foreach ($reservations as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r->property); ?></td>
            <td><span class="dso-badge"><?php echo htmlspecialchars($r->status); ?></span></td>
            <td><?php echo (int) $r->room_nights; ?></td>
            <td><?php echo number_format($r->total_amount, 2); ?></td>
            <td><?php echo htmlspecialchars($r->created_at); ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Outstanding Collections</h3>
    <table class="dso-table">
        <tr><th>Due Date</th><th>Amount</th><th>Paid</th><th>Outstanding</th><th>Status</th></tr>
        <?php $has_outstanding = false; ?>
        <?php foreach ($collections as $col): if ($col->status === 'Paid') continue; $has_outstanding = true; ?>
        <tr>
            <td><?php echo htmlspecialchars($col->due_date); ?></td>
            <td><?php echo number_format($col->amount, 2); ?></td>
            <td><?php echo number_format($col->paid_amount, 2); ?></td>
            <td><?php echo number_format($col->amount - $col->paid_amount, 2); ?></td>
            <td><span class="dso-badge"><?php echo htmlspecialchars($col->status); ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$has_outstanding): ?>
        <tr><td colspan="5">No outstanding collections for this account.</td></tr>
        <?php endif; ?>
    </table>
</div>

<a class="dso-btn" href="<?php echo base_url('dyafa/accounts/activities/' . $account->id); ?>">View Full Activity Log</a>
<a class="dso-btn secondary" href="<?php echo base_url('dyafa/accounts'); ?>">All Accounts</a>

<h2>Account Performance</h2>

<div class="dso-card">
    <table class="dso-table">
        <tr>
            <th>Company Name</th>
            <th>Status</th>
            <th>VIP</th>
            <th>Total Revenue</th>
            <th>Room Nights</th>
            <th>Reservations</th>
        </tr>
        <?php if (empty($accounts)): ?>
        <tr><td colspan="6">No accounts found.</td></tr>
        <?php else: foreach ($accounts as $a): ?>
        <tr>
            <td><a href="<?php echo base_url('dyafa/accounts/view360/' . $a->id); ?>"><?php echo htmlspecialchars($a->company_name); ?></a></td>
            <td><span class="dso-badge"><?php echo htmlspecialchars($a->status); ?></span></td>
            <td><?php echo $a->is_vip ? 'Yes' : 'No'; ?></td>
            <td><?php echo number_format($a->total_revenue, 2); ?></td>
            <td><?php echo (int) $a->total_room_nights; ?></td>
            <td><?php echo (int) $a->reservation_count; ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </table>
</div>

<a class="dso-btn" href="<?php echo base_url('dyafa/accounts'); ?>">Back</a>

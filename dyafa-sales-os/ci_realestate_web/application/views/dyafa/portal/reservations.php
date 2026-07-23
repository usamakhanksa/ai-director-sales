<h2>My Reservations</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/portal/reservation_new'); ?>">+ New Reservation</a>
<br><br>
<table class="dso-table">
<tr><th>Property</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($reservations as $r): ?>
<tr>
    <td><?php echo htmlspecialchars($r->property); ?></td>
    <td><?php echo $r->check_in; ?></td>
    <td><?php echo $r->check_out; ?></td>
    <td><?php echo number_format($r->total_amount, 2); ?></td>
    <td><span class="dso-badge"><?php echo $r->status; ?></span></td>
    <td>
        <?php if (!in_array($r->status, array('Cancelled', 'CheckedOut', 'NoShow'), true)): ?>
        <a href="<?php echo base_url('dyafa/portal/reservation_cancel/' . $r->id); ?>" onclick="return confirm('Cancel this reservation?');">Cancel</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

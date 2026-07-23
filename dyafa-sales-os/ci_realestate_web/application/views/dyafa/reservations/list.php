<h2>Reservations</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/reservations/add'); ?>">+ Add Reservation</a>
<br><br>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>
<table class="dso-table" data-server-paginated="1">
<tr><th>Account</th><th>Property</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>PMS Ref</th><th>Actions</th></tr>
<?php foreach ($reservations as $r): ?>
<tr>
    <td><?php echo $r->account_id; ?></td>
    <td><?php echo htmlspecialchars($r->property); ?></td>
    <td><?php echo $r->check_in; ?></td>
    <td><?php echo $r->check_out; ?></td>
    <td><?php echo number_format($r->total_amount, 2); ?></td>
    <td><span class="dso-badge"><?php echo $r->status; ?></span></td>
    <td>
        <?php if (!empty($r->pms_reference)): ?>
            <span title="Room <?php echo htmlspecialchars($r->pms_room_no); ?> / <?php echo htmlspecialchars($r->pms_status); ?>"><?php echo htmlspecialchars($r->pms_reference); ?></span>
        <?php else: ?>
            <span style="color:var(--color-muted);">-</span>
        <?php endif; ?>
    </td>
    <td>
        <a href="<?php echo base_url('dyafa/reservations/edit/' . $r->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/reservations/cancel/' . $r->id); ?>" onclick="return confirm('Cancel?');">Cancel</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php if (!empty($dso_pagination)) echo $dso_pagination; ?>

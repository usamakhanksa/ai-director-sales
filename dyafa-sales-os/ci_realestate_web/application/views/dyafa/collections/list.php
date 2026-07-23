<h2>Collections</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/collections/add'); ?>">+ Add Collection</a>
<br><br>
<table class="dso-table">
<tr><th>Invoice #</th><th>Account</th><th>Amount</th><th>Paid</th><th>Due Date</th><th>Status</th><th>Finance Ref</th><th>Payment Ref</th><th>Actions</th></tr>
<?php foreach ($collections as $c): ?>
<tr>
    <td><?php echo htmlspecialchars($c->invoice_no); ?></td>
    <td><?php echo $c->account_id; ?></td>
    <td><?php echo number_format($c->amount, 2); ?></td>
    <td><?php echo number_format($c->paid_amount, 2); ?></td>
    <td><?php echo $c->due_date; ?></td>
    <td><span class="dso-badge"><?php echo $c->status; ?></span></td>
    <td><?php echo !empty($c->finance_reference) ? htmlspecialchars($c->finance_reference) : '<span style="color:var(--color-muted);">-</span>'; ?></td>
    <td><?php echo !empty($c->payment_reference) ? htmlspecialchars($c->payment_reference) : '<span style="color:var(--color-muted);">-</span>'; ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/collections/edit/' . $c->id); ?>">Edit / Record Payment</a> |
        <a href="<?php echo base_url('dyafa/collections/delete/' . $c->id); ?>" onclick="return confirm('Delete?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

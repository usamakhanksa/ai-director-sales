<h2>Invoices</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/collections/add'); ?>">+ Add Invoice</a>
<br><br>
<table class="dso-table">
<tr><th>Invoice #</th><th>Account</th><th>Amount</th><th>Due Date</th><th>Status</th></tr>
<?php foreach ($collections as $c): ?>
<tr>
    <td><?php echo htmlspecialchars($c->invoice_no); ?></td>
    <td><?php echo $c->account_id; ?></td>
    <td><?php echo number_format($c->amount, 2); ?></td>
    <td><?php echo $c->due_date; ?></td>
    <td><span class="dso-badge"><?php echo $c->status; ?></span></td>
</tr>
<?php endforeach; ?>
</table>

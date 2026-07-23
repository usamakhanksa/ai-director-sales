<h2>Account Statements</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<br>
<form method="get" action="<?php echo base_url('dyafa/collections/statements'); ?>">
    <select name="account_id" onchange="this.form.submit()">
        <option value="">-- Select an account --</option>
        <?php foreach ($accounts as $a): ?>
        <option value="<?php echo $a->id; ?>" <?php echo ((string) $selected_account_id === (string) $a->id) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($a->company_name); ?>
        </option>
        <?php endforeach; ?>
    </select>
</form>
<br>

<?php if ($account): ?>
<div class="dso-card">
    <h3>Statement - <?php echo htmlspecialchars($account->company_name); ?></h3>
    <table class="dso-table">
        <tr><th>Invoice #</th><th>Amount</th><th>Paid</th><th>Due Date</th><th>Status</th></tr>
        <?php $total_due = 0; foreach ($collections as $c): $total_due += ($c->amount - $c->paid_amount); ?>
        <tr>
            <td><?php echo htmlspecialchars($c->invoice_no); ?></td>
            <td><?php echo number_format($c->amount, 2); ?></td>
            <td><?php echo number_format($c->paid_amount, 2); ?></td>
            <td><?php echo $c->due_date; ?></td>
            <td><?php echo $c->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p>Total Outstanding: <b><?php echo number_format($total_due, 2); ?></b></p>
</div>
<?php elseif ($selected_account_id): ?>
<p>Account not found.</p>
<?php endif; ?>

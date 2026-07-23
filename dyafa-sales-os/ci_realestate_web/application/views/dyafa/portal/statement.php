<h2>Statement - <?php echo htmlspecialchars($account->company_name); ?></h2>

<div class="dso-card">
    <h3>Invoices / Collections</h3>
    <table class="dso-table">
        <tr><th>Invoice #</th><th>Amount</th><th>Paid</th><th>Due Date</th><th>Status</th><th>Invoice</th></tr>
        <?php $total_due = 0; foreach ($collections as $c): $total_due += ($c->amount - $c->paid_amount); ?>
        <tr>
            <td><?php echo htmlspecialchars($c->invoice_no); ?></td>
            <td><?php echo number_format($c->amount, 2); ?></td>
            <td><?php echo number_format($c->paid_amount, 2); ?></td>
            <td><?php echo $c->due_date; ?></td>
            <td><?php echo $c->status; ?></td>
            <td><a href="<?php echo base_url('dyafa/portal/invoice_download/' . $c->id); ?>" target="_blank">Download PDF</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p>Total Outstanding: <b><?php echo number_format($total_due, 2); ?></b></p>
</div>

<div class="dso-card">
    <h3>Reservations</h3>
    <table class="dso-table">
        <tr><th>Property</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th></tr>
        <?php foreach ($reservations as $r): ?>
        <tr><td><?php echo htmlspecialchars($r->property); ?></td><td><?php echo $r->check_in; ?></td><td><?php echo $r->check_out; ?></td><td><?php echo number_format($r->total_amount, 2); ?></td><td><?php echo $r->status; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

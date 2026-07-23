<h2><?php echo $collection ? 'Edit Collection' : 'Add Collection'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Account</label>
    <select name="account_id">
        <?php foreach ($accounts as $a): ?>
        <option value="<?php echo $a->id; ?>" <?php echo ($collection && $collection->account_id == $a->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a->company_name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Invoice No</label>
    <input type="text" name="invoice_no" value="<?php echo $collection ? htmlspecialchars($collection->invoice_no) : ''; ?>" required>

    <label>Amount</label>
    <input type="number" step="0.01" name="amount" value="<?php echo $collection ? $collection->amount : ''; ?>" required>

    <label>Due Date</label>
    <input type="date" name="due_date" value="<?php echo $collection ? $collection->due_date : ''; ?>" required>

    <label>Paid Amount (manual entry - see note below)</label>
    <input type="number" step="0.01" name="paid_amount" value="<?php echo $collection ? $collection->paid_amount : 0; ?>">

    <label>Status</label>
    <select name="status">
        <?php foreach (array('Pending','PartiallyPaid','Paid','Overdue') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($collection && $collection->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <br><button type="submit" class="dso-btn">Save</button>
</form>
<p style="font-size:12px;color:var(--color-muted);">Automated online payment gateway is out of scope for this build. Payments are recorded manually here by the Finance Team; status is auto-derived from paid_amount vs amount.</p>

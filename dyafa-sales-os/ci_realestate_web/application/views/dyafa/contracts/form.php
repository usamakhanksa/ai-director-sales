<h2><?php echo $contract ? 'Edit Contract' : 'Add Contract'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Company Name</label>
    <input type="text" name="company_name" value="<?php echo $contract ? htmlspecialchars($contract->company_name) : ''; ?>" required>

    <label>Contract Number</label>
    <input type="text" name="contract_number" value="<?php echo $contract ? htmlspecialchars($contract->contract_number) : ''; ?>" required>

    <label>Account ID (optional, link later if not yet created)</label>
    <input type="number" name="account_id" value="<?php echo $contract ? $contract->account_id : ''; ?>">

    <label>Start Date</label>
    <input type="date" name="start_date" value="<?php echo $contract ? $contract->start_date : ''; ?>">

    <label>Expiry Date</label>
    <input type="date" name="expiry_date" value="<?php echo $contract ? $contract->expiry_date : ''; ?>">

    <label>Payment Terms</label>
    <input type="text" name="payment_terms" value="<?php echo $contract ? htmlspecialchars($contract->payment_terms) : ''; ?>">

    <label>Credit Days</label>
    <input type="number" name="credit_days" value="<?php echo $contract ? $contract->credit_days : 0; ?>">

    <label>Credit Limit</label>
    <input type="number" step="0.01" name="credit_limit" value="<?php echo $contract ? $contract->credit_limit : ''; ?>" required>

    <label>Account Manager</label>
    <select name="account_manager_id">
        <option value="">-- none --</option>
        <?php foreach ($managers as $m): ?>
        <option value="<?php echo $m->id; ?>" <?php echo ($contract && $contract->account_manager_id == $m->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($m->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Allowed Properties</label>
    <?php $selected_properties = $contract ? array_map('trim', explode(',', $contract->allowed_properties)) : array(); ?>
    <div>
    <?php foreach ($all_properties as $p): ?>
        <label style="display:inline-block;font-weight:normal;margin-right:14px;">
            <input type="checkbox" name="allowed_properties[]" value="<?php echo htmlspecialchars($p); ?>" <?php echo in_array($p, $selected_properties, true) ? 'checked' : ''; ?> style="width:auto;">
            <?php echo htmlspecialchars($p); ?>
        </label>
    <?php endforeach; ?>
    </div>
    <p style="font-size:12px;color:var(--color-muted);">Manage the property list under <a href="<?php echo base_url('dyafa/properties'); ?>">Properties</a>.</p>

    <label>Corporate Rates (JSON: {"Property Name": rate})</label>
    <textarea name="corporate_rates" rows="3"><?php echo $contract ? htmlspecialchars($contract->corporate_rates) : '{}'; ?></textarea>

    <label>Status</label>
    <select name="status">
        <?php foreach (array('Active','Pending Approval','Pending Renewal','Expired','Suspended','Cancelled') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($contract && $contract->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

<h2><?php echo $account ? 'Edit Account' : 'Add Account'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Company Name</label>
    <input type="text" name="company_name" value="<?php echo $account ? htmlspecialchars($account->company_name) : ''; ?>" required>

    <label>Industry</label>
    <input type="text" name="industry" value="<?php echo $account ? htmlspecialchars($account->industry) : ''; ?>">

    <label>City</label>
    <input type="text" name="city" value="<?php echo $account ? htmlspecialchars($account->city) : ''; ?>">

    <label>Primary Contact Person</label>
    <input type="text" name="primary_contact_person" value="<?php echo $account ? htmlspecialchars($account->primary_contact_person) : ''; ?>">

    <label>Primary Contact Mobile</label>
    <input type="text" name="primary_contact_mobile" value="<?php echo $account ? htmlspecialchars($account->primary_contact_mobile) : ''; ?>">

    <label>Primary Contact Email</label>
    <input type="email" name="primary_contact_email" value="<?php echo $account ? htmlspecialchars($account->primary_contact_email) : ''; ?>">

    <label>Linked Contract</label>
    <select name="contract_id">
        <option value="">-- none --</option>
        <?php foreach ($contracts as $c): ?>
        <option value="<?php echo $c->id; ?>" <?php echo ($account && $account->contract_id == $c->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->contract_number . ' - ' . $c->company_name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Account Owner</label>
    <select name="account_owner_id">
        <option value="">-- none --</option>
        <?php foreach ($users as $u): ?>
        <option value="<?php echo $u->id; ?>" <?php echo ($account && $account->account_owner_id == $u->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Status</label>
    <select name="status">
        <option value="Active" <?php echo ($account && $account->status == 'Active') ? 'selected' : ''; ?>>Active</option>
        <option value="Inactive" <?php echo ($account && $account->status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
    </select>

    <label>
        <input type="checkbox" name="is_vip" value="1" <?php echo ($account && $account->is_vip) ? 'checked' : ''; ?>>
        VIP Account (triggers arrival notifications to the account owner)
    </label>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

<h2><?php echo $team ? 'Edit Team: ' . htmlspecialchars($team->name) : 'Add Team'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Team Name</label>
    <input type="text" name="name" value="<?php echo $team ? htmlspecialchars($team->name) : ''; ?>" required>

    <label>HOD / Team Lead</label>
    <select name="hod_user_id">
        <option value="">-- None --</option>
        <?php foreach ($users as $u): ?>
        <option value="<?php echo $u->id; ?>" <?php echo ($team && $team->hod_user_id == $u->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Territory &mdash; Properties</label>
    <?php foreach ($properties as $p): ?>
    <label style="display:block;font-weight:normal;">
        <input type="checkbox" name="property_ids[]" value="<?php echo $p->id; ?>" <?php echo in_array($p->id, $assigned_properties) ? 'checked' : ''; ?>>
        <?php echo htmlspecialchars($p->name); ?> (<?php echo htmlspecialchars($p->city); ?>)
    </label>
    <?php endforeach; ?>

    <label>Territory &mdash; Corporate Accounts</label>
    <?php foreach ($accounts as $a): ?>
    <label style="display:block;font-weight:normal;">
        <input type="checkbox" name="account_ids[]" value="<?php echo $a->id; ?>" <?php echo in_array($a->id, $assigned_accounts) ? 'checked' : ''; ?>>
        <?php echo htmlspecialchars($a->company_name); ?>
    </label>
    <?php endforeach; ?>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

<?php if ($team): ?>
<h3>Members</h3>
<table class="dso-table">
<tr><th>Name</th><th>Role</th></tr>
<?php foreach ($members as $m): ?>
<tr><td><?php echo htmlspecialchars($m->name); ?></td><td><?php echo htmlspecialchars($m->role); ?></td></tr>
<?php endforeach; ?>
</table>
<p style="font-size:12px;color:var(--color-muted);">Assign users to this team from Administration &gt; Users &amp; Roles.</p>
<?php endif; ?>

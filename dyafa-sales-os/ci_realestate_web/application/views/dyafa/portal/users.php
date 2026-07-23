<h2>Company Users</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/portal/user_add'); ?>">+ Add Company User</a>
<br><br>
<?php if ($this->session->flashdata('dso_success')): ?>
<div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div>
<?php endif; ?>
<table class="dso-table">
<tr><th>Name</th><th>Email</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?php echo htmlspecialchars($u->name); ?></td>
    <td><?php echo htmlspecialchars($u->email); ?></td>
    <td><?php echo htmlspecialchars($u->username); ?></td>
    <td><?php echo htmlspecialchars($u->role); ?></td>
    <td><span class="dso-badge"><?php echo $u->status; ?></span></td>
    <td>
        <a href="<?php echo base_url('dyafa/portal/user_edit/' . $u->id); ?>">Edit</a>
        &nbsp;|&nbsp;
        <a href="<?php echo base_url('dyafa/portal/user_toggle_status/' . $u->id); ?>" onclick="return confirm('Toggle status for this user?');"><?php echo $u->status === 'Active' ? 'Deactivate' : 'Activate'; ?></a>
    </td>
</tr>
<?php endforeach; ?>
</table>

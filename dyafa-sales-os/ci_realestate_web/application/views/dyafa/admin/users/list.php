<h2>Users &amp; Roles &mdash; Users</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/users/add'); ?>">+ Add User</a>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/roles'); ?>">Manage Roles</a>
<br><br>
<table class="dso-table">
<tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?php echo htmlspecialchars($u->name); ?></td>
    <td><?php echo htmlspecialchars($u->username); ?></td>
    <td><?php echo htmlspecialchars($u->email); ?></td>
    <td><?php echo htmlspecialchars($u->role); ?></td>
    <td><span class="dso-badge"><?php echo $u->status; ?></span></td>
    <td>
        <a href="<?php echo base_url('dyafa/admin/users/edit/' . $u->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/admin/users/toggle_status/' . $u->id); ?>" onclick="return confirm('Toggle status for this user?');"><?php echo $u->status === 'Active' ? 'Deactivate' : 'Activate'; ?></a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<h2>Users &amp; Roles &mdash; Roles</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/roles/add'); ?>">+ Add Role</a>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/users'); ?>">Manage Users</a>
<br><br>
<table class="dso-table">
<tr><th>Role</th><th>System Role</th><th>Actions</th></tr>
<?php foreach ($roles as $r): ?>
<tr>
    <td><?php echo htmlspecialchars($r->name); ?></td>
    <td><?php echo $r->is_system ? 'Yes' : 'No'; ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/admin/roles/edit/' . $r->id); ?>">Edit Permissions</a>
        <?php if (!$r->is_system): ?>
        | <a href="<?php echo base_url('dyafa/admin/roles/delete/' . $r->id); ?>" onclick="return confirm('Delete this role?');">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

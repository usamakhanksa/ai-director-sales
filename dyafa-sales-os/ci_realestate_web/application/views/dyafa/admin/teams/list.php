<h2>Teams</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/teams/add'); ?>">+ Add Team</a>
<br><br>
<table class="dso-table">
<tr><th>Team</th><th>HOD</th><th>Actions</th></tr>
<?php foreach ($teams as $t): ?>
<tr>
    <td><?php echo htmlspecialchars($t->name); ?></td>
    <td><?php echo htmlspecialchars($t->hod_name ?: '-'); ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/admin/teams/edit/' . $t->id); ?>">Edit / Territory</a> |
        <a href="<?php echo base_url('dyafa/admin/teams/delete/' . $t->id); ?>" onclick="return confirm('Delete this team?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

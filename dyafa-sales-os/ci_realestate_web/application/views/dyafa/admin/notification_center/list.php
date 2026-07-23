<h2>Notification Center</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/admin/notificationcenter/broadcast'); ?>">+ Broadcast Notification</a>
<br><br>
<table class="dso-table">
<tr><th>User</th><th>Type</th><th>Message</th><th>Read</th><th>Sent</th></tr>
<?php foreach ($notifications as $n): ?>
<tr>
    <td><?php echo htmlspecialchars($n->user_name ?: ($n->role ?: 'All')); ?></td>
    <td><span class="dso-badge"><?php echo htmlspecialchars($n->type); ?></span></td>
    <td><?php echo htmlspecialchars($n->message); ?></td>
    <td><?php echo $n->is_read ? 'Yes' : 'No'; ?></td>
    <td><?php echo $n->created_at; ?></td>
</tr>
<?php endforeach; ?>
</table>

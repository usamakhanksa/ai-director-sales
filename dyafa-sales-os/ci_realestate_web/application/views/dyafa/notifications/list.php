<h2>Notifications</h2>
<table class="dso-table" data-server-paginated="1">
<tr><th>Type</th><th>Message</th><th>Date</th><th>Read?</th><th>Actions</th></tr>
<?php foreach ($notifications as $n): ?>
<tr>
    <td><?php echo htmlspecialchars($n->type); ?></td>
    <td><?php echo htmlspecialchars($n->message); ?></td>
    <td><?php echo $n->created_at; ?></td>
    <td><?php echo $n->is_read ? 'Yes' : 'No'; ?></td>
    <td><?php if (!$n->is_read): ?><a href="<?php echo base_url('dyafa/notifications/mark_read/' . $n->id); ?>">Mark Read</a><?php endif; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php if (!empty($dso_pagination)) echo $dso_pagination; ?>

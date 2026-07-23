<h2>Lead Sources</h2>
<table class="dso-table">
<tr><th>Source</th><th>Count</th><th>Total Estimated Revenue</th></tr>
<?php foreach ($sources as $s): ?>
<tr>
    <td><?php echo htmlspecialchars($s->source ?: 'Unknown'); ?></td>
    <td><?php echo $s->cnt; ?></td>
    <td><?php echo number_format($s->total_revenue, 2); ?></td>
</tr>
<?php endforeach; ?>
</table>

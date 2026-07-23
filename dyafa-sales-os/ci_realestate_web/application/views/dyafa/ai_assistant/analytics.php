<h2>AI Sales Assistant - Analytics</h2>
<p style="font-size:12px;color:var(--color-muted);">Aggregate counts of <code>dso_ai_recommendations</code> by type and by status.</p>
<p><a href="<?php echo base_url('dyafa/aiassistant'); ?>">&larr; Back to AI Sales Assistant</a></p>

<h3>By Type</h3>
<?php if (empty($by_type)): ?>
<div class="dso-alert">No recommendations yet.</div>
<?php else: ?>
<table class="dso-table">
<tr><th>Type</th><th>Count</th></tr>
<?php foreach ($by_type as $row): ?>
<tr><td><?php echo htmlspecialchars($row->type); ?></td><td><?php echo (int) $row->total; ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<h3>By Status</h3>
<?php if (empty($by_status)): ?>
<div class="dso-alert">No recommendations yet.</div>
<?php else: ?>
<table class="dso-table">
<tr><th>Status</th><th>Count</th></tr>
<?php foreach ($by_status as $row): ?>
<tr><td><?php echo htmlspecialchars($row->status); ?></td><td><?php echo (int) $row->total; ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<h2>Contracts Funnel</h2>
<table class="dso-table">
<tr><th>Status</th><th>Count</th></tr>
<?php foreach ($counts as $c): ?>
<tr><td><?php echo $c->status; ?></td><td><?php echo $c->cnt; ?></td></tr>
<?php endforeach; ?>
</table>
<a class="dso-btn" href="<?php echo base_url('dyafa/contracts'); ?>">Back</a>

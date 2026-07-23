<h2>Performance: <?php echo htmlspecialchars($user->name); ?> - <?php echo $month; ?></h2>
<?php if (!$perf): ?>
    <p>No target set for this user/month.</p>
<?php else: ?>
    <p>Overall Achievement: <b><?php echo $perf['overall_pct']; ?>%</b> - <span class="dso-badge"><?php echo $perf['band']; ?></span></p>
    <table class="dso-table">
        <tr><th>Metric</th><th>Target</th><th>Actual</th><th>%</th></tr>
        <?php foreach ($perf['metrics'] as $key => $m): ?>
        <tr>
            <td><?php echo ucwords(str_replace('_', ' ', $key)); ?></td>
            <td><?php echo $m['target']; ?></td>
            <td><?php echo $m['actual']; ?></td>
            <td><?php echo $m['pct']; ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/targets'); ?>">Back</a>

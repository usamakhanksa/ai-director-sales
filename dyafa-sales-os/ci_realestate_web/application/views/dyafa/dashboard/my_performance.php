<h2>My Performance - <?php echo $month; ?></h2>

<div class="dso-stats">
    <div class="dso-stat" style="--stat-accent:var(--color-accent);">
        <div class="dso-stat-label">MTD Revenue</div>
        <div class="dso-stat-value"><?php echo number_format($mtd_revenue, 2); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-accent-2);">
        <div class="dso-stat-label">MTD Reservations</div>
        <div class="dso-stat-value"><?php echo $mtd_reservations; ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-muted);">
        <div class="dso-stat-label">MTD Collections</div>
        <div class="dso-stat-value"><?php echo number_format($mtd_collections, 2); ?></div>
    </div>
</div>

<div class="dso-card">
    <h3>Target vs Actual</h3>
    <?php if (!$performance): ?>
        <p>No target set for this month.</p>
    <?php else: ?>
        <p>Overall Achievement: <b><?php echo $performance['overall_pct']; ?>%</b> - <span class="dso-badge"><?php echo $performance['band']; ?></span></p>
        <table class="dso-table">
            <tr><th>Metric</th><th>Target</th><th>Actual</th><th>%</th></tr>
            <?php foreach ($performance['metrics'] as $key => $m): ?>
            <tr>
                <td><?php echo ucwords(str_replace('_', ' ', $key)); ?></td>
                <td><?php echo $m['target']; ?></td>
                <td><?php echo $m['actual']; ?></td>
                <td><?php echo $m['pct']; ?>%</td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="dso-card">
    <h3>My Recent Leads</h3>
    <table class="dso-table">
        <tr><th>Company</th><th>Revenue</th><th>Status</th></tr>
        <?php foreach ($recent_leads as $l): ?>
        <tr>
            <td><?php echo htmlspecialchars($l->company_name); ?></td>
            <td><?php echo number_format($l->estimated_revenue, 2); ?></td>
            <td><?php echo $l->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

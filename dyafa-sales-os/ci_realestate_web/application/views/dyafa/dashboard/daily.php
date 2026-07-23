<h2>Daily Dashboard</h2>

<div class="dso-stats">
    <div class="dso-stat" style="--stat-accent:var(--color-accent);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5v9M9.5 9.7c0-1.2 1.1-2.2 2.5-2.2s2.5 1 2.5 2c0 2.4-5 1.6-5 4 0 1 1.1 2 2.5 2s2.5-1 2.5-2.2"/></svg></div>
        <div class="dso-stat-label">Today's Revenue</div>
        <div class="dso-stat-value"><?php echo number_format($today_revenue, 2); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-accent-2);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></div>
        <div class="dso-stat-label">MTD Revenue</div>
        <div class="dso-stat-value"><?php echo number_format($mtd_revenue, 2); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-muted);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="1.5"/><path d="M3 10h18M8 3v4M16 3v4"/></svg></div>
        <div class="dso-stat-label">Reservations Today</div>
        <div class="dso-stat-value"><?php echo (int) $reservations_today; ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-nav-bg);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5"/></svg></div>
        <div class="dso-stat-label">Activities Today</div>
        <div class="dso-stat-value"><?php echo (int) $activities_today; ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-accent);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.6 3.1-6.5 7-6.5s7 2.9 7 6.5"/></svg></div>
        <div class="dso-stat-label">New Leads (this month)</div>
        <div class="dso-stat-value"><?php echo (int) $new_leads_today; ?></div>
    </div>
</div>

<div class="dso-charts-row">
    <div class="dso-card dso-chart-card">
        <h3>MTD Revenue Trend</h3>
        <canvas id="dsoRevenueTrendChart" height="220"></canvas>
    </div>
    <div class="dso-card dso-chart-card dso-chart-card-narrow">
        <h3>Target Achievement</h3>
        <?php if (empty($performance)): ?>
            <p style="color:var(--color-muted);font-size:13px;">No target has been set for you this month.</p>
        <?php else: ?>
            <canvas id="dsoAchievementGauge" height="220"></canvas>
            <p style="text-align:center;margin-top:10px;"><span class="dso-badge accent"><?php echo $performance['band']; ?></span></p>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($performance)): ?>
<div class="dso-card">
    <h3>Target Achievement (this month)</h3>
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
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';
    // Brand palette below must mirror the 5 hex values in application/views/dyafa/layout/header.php's :root block.
    var ACCENT = '#e95a54', ACCENT_2 = '#fbcdab', NAV_BG = '#2a273c', MUTED = '#8f9793';

    var daysInMonth = <?php echo (int) date('t'); ?>;
    var trend = <?php echo json_encode($revenue_trend); ?>;
    var labels = [];
    var data = [];
    for (var d = 1; d <= daysInMonth; d++) {
        labels.push(d);
        data.push(trend[d] || 0);
    }

    new Chart(document.getElementById('dsoRevenueTrendChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: data,
                borderColor: ACCENT,
                backgroundColor: ACCENT + '22',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { title: { display: true, text: 'Day of month' }, grid: { display: false } },
                y: { beginAtZero: true },
            },
        },
    });

    <?php if (!empty($performance)): ?>
    var pct = <?php echo (float) $performance['overall_pct']; ?>;
    var remaining = Math.max(0, 100 - pct);
    new Chart(document.getElementById('dsoAchievementGauge'), {
        type: 'doughnut',
        data: {
            labels: ['Achieved', 'Remaining'],
            datasets: [{
                data: [pct, remaining],
                backgroundColor: [ACCENT, MUTED + '33'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
            },
        },
        plugins: [{
            id: 'dsoGaugeCenterText',
            afterDraw: function (chart) {
                var ctx = chart.ctx;
                var w = chart.width, h = chart.height;
                ctx.save();
                ctx.font = '700 22px Segoe UI, Arial, sans-serif';
                ctx.fillStyle = '<?php echo "#2a273c"; ?>';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(pct + '%', w / 2, h / 2);
                ctx.restore();
            },
        }],
    });
    <?php endif; ?>
})();
</script>

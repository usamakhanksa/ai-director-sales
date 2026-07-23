<h2>HOD Sales Dashboard</h2>

<div class="dso-stats">
    <div class="dso-stat" style="--stat-accent:var(--color-accent);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5v9M9.5 9.7c0-1.2 1.1-2.2 2.5-2.2s2.5 1 2.5 2c0 2.4-5 1.6-5 4 0 1 1.1 2 2.5 2s2.5-1 2.5-2.2"/></svg></div>
        <div class="dso-stat-label">Outstanding Collections</div>
        <div class="dso-stat-value"><?php echo number_format($outstanding, 2); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-accent-2);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="0.8" fill="currentColor"/></svg></div>
        <div class="dso-stat-label">Team Members Ranked</div>
        <div class="dso-stat-value"><?php echo count($ranking); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-muted);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4"/><path d="M9.5 13h5M9.5 16.5h5"/></svg></div>
        <div class="dso-stat-label">Contracts Expiring (30d)</div>
        <div class="dso-stat-value"><?php echo count($expiring); ?></div>
    </div>
    <div class="dso-stat" style="--stat-accent:var(--color-nav-bg);">
        <div class="dso-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="13" rx="1.5"/><path d="M8 7V5.5A2.5 2.5 0 0 1 10.5 3h3A2.5 2.5 0 0 1 16 5.5V7"/></svg></div>
        <div class="dso-stat-label">Top Accounts Listed</div>
        <div class="dso-stat-value"><?php echo count($top_accounts); ?></div>
    </div>
</div>

<div class="dso-charts-row">
    <div class="dso-card dso-chart-card">
        <h3>Team MTD Revenue Trend</h3>
        <canvas id="dsoHodRevenueTrendChart" height="220"></canvas>
    </div>
    <div class="dso-card dso-chart-card dso-chart-card-narrow">
        <h3>Top 5 Accounts by Revenue</h3>
        <canvas id="dsoTopAccountsChart" height="220"></canvas>
    </div>
</div>

<div class="dso-card">
    <h3>Team Performance Ranking (MTD)</h3>
    <table class="dso-table">
        <tr><th>#</th><th>Sales Executive</th><th>Revenue MTD</th><th>Achievement %</th><th>Band</th></tr>
        <?php foreach ($ranking as $i => $r): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($r['user']->name); ?></td>
            <td><?php echo number_format($r['revenue'], 2); ?></td>
            <td><?php echo $r['pct'] !== null ? $r['pct'] . '%' : '-'; ?></td>
            <td><span class="dso-badge accent"><?php echo $r['band']; ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Lead Conversion Rate by Owner</h3>
    <table class="dso-table">
        <tr><th>Owner</th><th>Total Leads</th><th>Won</th><th>Conversion %</th></tr>
        <?php foreach ($conversion as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['owner']); ?></td>
            <td><?php echo $c['total']; ?></td>
            <td><?php echo $c['won']; ?></td>
            <td><?php echo $c['rate']; ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Top 5 Accounts by Reservation Revenue</h3>
    <table class="dso-table">
        <tr><th>Account</th><th>Total Revenue</th></tr>
        <?php foreach ($top_accounts as $a): ?>
        <tr><td><?php echo htmlspecialchars($a->company_name); ?></td><td><?php echo number_format($a->total_revenue, 2); ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="dso-card">
    <h3>Contracts Expiring within 30 Days</h3>
    <table class="dso-table">
        <tr><th>Contract #</th><th>Company</th><th>Expiry Date</th><th>Status</th></tr>
        <?php foreach ($expiring as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c->contract_number); ?></td>
            <td><?php echo htmlspecialchars($c->company_name); ?></td>
            <td><?php echo $c->expiry_date; ?></td>
            <td><?php echo $c->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

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
    new Chart(document.getElementById('dsoHodRevenueTrendChart'), {
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

    var topAccounts = <?php echo json_encode(array_map(function ($a) { return array('name' => $a->company_name, 'total' => (float) $a->total_revenue); }, $top_accounts)); ?>;
    new Chart(document.getElementById('dsoTopAccountsChart'), {
        type: 'bar',
        data: {
            labels: topAccounts.map(function (a) { return a.name; }),
            datasets: [{
                label: 'Revenue',
                data: topAccounts.map(function (a) { return a.total; }),
                backgroundColor: [ACCENT, ACCENT_2, MUTED, NAV_BG, ACCENT + 'aa'],
                borderRadius: 4,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } },
        },
    });
})();
</script>

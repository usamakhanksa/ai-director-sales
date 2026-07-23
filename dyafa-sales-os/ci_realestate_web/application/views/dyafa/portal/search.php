<h2>Search Hotels &amp; Availability</h2>
<p>Properties allowed under your contract, with your corporate rate where set (falls back to the property's standard rate).</p>
<table class="dso-table">
<tr><th>Property</th><th>City</th><th>Rate</th><th>Action</th></tr>
<?php foreach ($results as $r): ?>
<tr>
    <td><?php echo htmlspecialchars($r['property']->name); ?></td>
    <td><?php echo htmlspecialchars((string) $r['property']->city); ?></td>
    <td><?php echo $r['rate'] !== null ? number_format($r['rate'], 2) : '<span style="color:var(--color-muted);">Contact Sales</span>'; ?></td>
    <td>
        <?php if ($r['rate'] !== null): ?>
        <a class="dso-btn" href="<?php echo base_url('dyafa/portal/reservation_new?property=' . urlencode($r['property']->name) . '&rate=' . $r['rate']); ?>">Book</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<h2>Credit Limits</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<br>
<table class="dso-table">
<tr><th>Account</th><th>Credit Days</th><th>Credit Limit</th><th>Outstanding</th><th>Remaining</th><th>Status</th></tr>
<?php foreach ($rows as $r): $remaining = $r->credit_limit - $r->outstanding; ?>
<tr>
    <td><?php echo htmlspecialchars($r->company_name); ?></td>
    <td><?php echo (int) $r->credit_days; ?></td>
    <td><?php echo number_format($r->credit_limit, 2); ?></td>
    <td><?php echo number_format($r->outstanding, 2); ?></td>
    <td><?php echo number_format($remaining, 2); ?></td>
    <td>
        <?php if ($remaining < 0): ?>
            <span class="dso-badge danger">Over Limit</span>
        <?php elseif ($r->credit_limit > 0 && $r->outstanding >= $r->credit_limit * 0.8): ?>
            <span class="dso-badge warning">Approaching Limit</span>
        <?php else: ?>
            <span class="dso-badge">OK</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

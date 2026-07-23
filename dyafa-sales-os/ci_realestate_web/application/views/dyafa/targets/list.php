<h2>Sales Targets</h2>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/targets/add'); ?>">+ Add Target</a>
<br><br>
<table class="dso-table">
<tr><th>User</th><th>Month</th><th>Revenue Target</th><th>Actions</th></tr>
<?php foreach ($targets as $t): ?>
<tr>
    <td><?php echo htmlspecialchars($t->user_name); ?></td>
    <td><?php echo $t->month; ?></td>
    <td><?php echo number_format($t->revenue_target, 2); ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/targets/performance/' . $t->user_id . '/' . $t->month); ?>">View Performance</a> |
        <a href="<?php echo base_url('dyafa/targets/edit/' . $t->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/targets/delete/' . $t->id); ?>" onclick="return confirm('Delete?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

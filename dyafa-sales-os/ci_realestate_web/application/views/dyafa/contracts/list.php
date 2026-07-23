<h2>Contracts</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/contracts/add'); ?>">+ Add Contract</a>
<a class="dso-btn" href="<?php echo base_url('dyafa/contracts/funnel'); ?>">View Funnel</a>
<br><br>
<?php $this->load->view('dyafa/partials/list_tabs', get_defined_vars()); ?>
<?php $this->load->view('dyafa/partials/list_filters', get_defined_vars()); ?>
<table class="dso-table">
<tr><th>Contract #</th><th>Company</th><th>Expiry</th><th>Credit Limit</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($contracts as $c): ?>
<tr>
    <td><?php echo htmlspecialchars($c->contract_number); ?></td>
    <td><?php echo htmlspecialchars($c->company_name); ?></td>
    <td><?php echo $c->expiry_date; ?></td>
    <td><?php echo number_format($c->credit_limit, 2); ?></td>
    <td><span class="dso-badge"><?php echo $c->status; ?></span></td>
    <td>
        <a href="<?php echo base_url('dyafa/contracts/edit/' . $c->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/contracts/delete/' . $c->id); ?>" onclick="return confirm('Delete?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

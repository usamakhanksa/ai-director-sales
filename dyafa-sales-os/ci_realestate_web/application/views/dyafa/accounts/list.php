<h2>Corporate Accounts</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/accounts/add'); ?>">+ Add Account</a>
<br><br>
<?php $this->load->view('dyafa/partials/list_filters', get_defined_vars()); ?>
<table class="dso-table">
<tr><th>Company</th><th>City</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($accounts as $a): ?>
<tr>
    <td><?php echo htmlspecialchars($a->company_name); ?></td>
    <td><?php echo htmlspecialchars($a->city); ?></td>
    <td><?php echo htmlspecialchars($a->primary_contact_person); ?></td>
    <td><span class="dso-badge"><?php echo $a->status; ?></span></td>
    <td>
        <a href="<?php echo base_url('dyafa/accounts/view/' . $a->id); ?>">View</a> |
        <a href="<?php echo base_url('dyafa/accounts/edit/' . $a->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/accounts/delete/' . $a->id); ?>" onclick="return confirm('Delete?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

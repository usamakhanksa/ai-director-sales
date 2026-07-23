<h2>Leads</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<a class="dso-btn" href="<?php echo base_url('dyafa/leads/add'); ?>">+ Add Lead</a>
<br><br>
<?php $this->load->view('dyafa/partials/list_tabs', get_defined_vars()); ?>
<?php $this->load->view('dyafa/partials/list_filters', get_defined_vars()); ?>
<table class="dso-table" data-server-paginated="1">
<tr><th>Company</th><th>Contact</th><th>Revenue</th><th>Score</th><th>Category</th><th>Owner</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($leads as $l): ?>
<tr>
    <td><?php echo htmlspecialchars($l->company_name); ?></td>
    <td><?php echo htmlspecialchars($l->contact_person); ?></td>
    <td><?php echo number_format($l->estimated_revenue, 2); ?></td>
    <td><?php echo $l->lead_score; ?></td>
    <td><span class="dso-badge"><?php echo $l->lead_category; ?></span></td>
    <td><?php echo $l->lead_owner_id; ?></td>
    <td><?php echo $l->status; ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/leads/view/' . $l->id); ?>">View</a> |
        <a href="<?php echo base_url('dyafa/leads/edit/' . $l->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/leads/assign/' . $l->id); ?>">Assign</a> |
        <a href="<?php echo base_url('dyafa/leads/delete/' . $l->id); ?>" onclick="return confirm('Delete this lead?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php if (!empty($dso_pagination)) echo $dso_pagination; ?>

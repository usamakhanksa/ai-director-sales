<h2>Properties</h2>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>
<?php if ($this->session->userdata('dso_role') === 'Sales Coordinator'): ?>
<p><a class="dso-btn" href="<?php echo base_url('dyafa/properties/add'); ?>">Add Property</a></p>
<?php endif; ?>
<?php if ($this->session->flashdata('dso_success')): ?>
<div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div>
<?php endif; ?>
<table class="dso-table">
<tr><th>Name</th><th>City</th><th>Total Rooms</th><th>Status</th><th>Map</th><th>Actions</th></tr>
<?php foreach ($properties as $p): ?>
<tr>
    <td><?php echo htmlspecialchars($p->name); ?></td>
    <td><?php echo htmlspecialchars($p->city); ?></td>
    <td><?php echo $p->total_rooms; ?></td>
    <td><span class="dso-badge"><?php echo $p->status; ?></span></td>
    <td>
        <?php if ($p->map_file): ?><a href="<?php echo base_url('uploads/property_maps/' . $p->map_file); ?>" target="_blank">File</a><?php endif; ?>
        <?php if ($p->lat && $p->lng): ?>
        &nbsp;<a href="https://www.openstreetmap.org/?mlat=<?php echo $p->lat; ?>&mlon=<?php echo $p->lng; ?>#map=14/<?php echo $p->lat; ?>/<?php echo $p->lng; ?>" target="_blank">Map</a>
        <?php endif; ?>
    </td>
    <td>
        <a href="<?php echo base_url('dyafa/properties/rates/' . $p->id); ?>">Rates</a>
        <?php if ($this->session->userdata('dso_role') === 'Sales Coordinator'): ?>
        &nbsp;<a href="<?php echo base_url('dyafa/properties/edit/' . $p->id); ?>">Edit</a>
        &nbsp;<a href="<?php echo base_url('dyafa/properties/delete/' . $p->id); ?>" onclick="return confirm('Delete this property?');">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

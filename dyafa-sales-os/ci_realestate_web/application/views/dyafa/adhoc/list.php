<h2>Adhoc Sales</h2>
<a class="dso-btn" href="<?php echo base_url('dyafa/adhoc/add'); ?>">+ Add Adhoc Sale</a>
<br><br>
<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>
<table class="dso-table">
<tr><th>Event Type</th><th>Date</th><th>Pax</th><th>Est. Value</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($items as $i): ?>
<tr>
    <td><?php echo $i->event_type; ?></td>
    <td><?php echo $i->event_date; ?></td>
    <td><?php echo $i->pax; ?></td>
    <td><?php echo number_format($i->estimated_value, 2); ?></td>
    <td><span class="dso-badge"><?php echo $i->status; ?></span></td>
    <td>
        <a href="<?php echo base_url('dyafa/adhoc/edit/' . $i->id); ?>">Edit</a> |
        <a href="<?php echo base_url('dyafa/adhoc/delete/' . $i->id); ?>" onclick="return confirm('Delete?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

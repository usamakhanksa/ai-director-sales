<h2>Rates - <?php echo htmlspecialchars($property->name); ?></h2>
<p><a href="<?php echo base_url('dyafa/properties'); ?>">&larr; Back to Properties</a></p>

<table class="dso-table">
<tr><th>Rate Type</th><th>Rate</th><th>Actions</th></tr>
<?php foreach ($rates as $r): ?>
<tr>
    <td><?php echo htmlspecialchars($r->rate_type); ?></td>
    <td><?php echo number_format($r->rate, 2); ?></td>
    <td>
    <?php if ($this->session->userdata('dso_role') === 'Sales Coordinator'): ?>
        <a href="<?php echo base_url('dyafa/properties/delete_rate/' . $r->id . '/' . $property->id); ?>" onclick="return confirm('Delete this rate?');">Delete</a>
    <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php if ($this->session->userdata('dso_role') === 'Sales Coordinator'): ?>
<h3>Add Rate</h3>
<form class="dso-form" method="post">
    <label>Rate Type</label>
    <input type="text" name="rate_type" placeholder="e.g. Standard Room, Suite, Long Stay" required>

    <label>Rate</label>
    <input type="number" step="0.01" name="rate" required>

    <br><button type="submit" class="dso-btn">Add Rate</button>
</form>
<?php endif; ?>

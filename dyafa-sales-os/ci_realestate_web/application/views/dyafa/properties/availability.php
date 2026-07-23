<h2>Availability Settings - <?php echo htmlspecialchars($property->name); ?></h2>
<p><a href="<?php echo base_url('dyafa/properties'); ?>">&larr; Back to Properties</a></p>
<p style="font-size:12px;color:var(--color-muted);">Note: this screen sets a simple bookable flag and blackout dates for this property. It is not a day-by-day room-inventory calendar. "Availability" for corporate contracts (which properties a contract may book) is managed separately via Contracts > Allowed Properties.</p>

<?php if ($this->session->flashdata('dso_success')): ?>
<div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div>
<?php endif; ?>

<h3>Bookable</h3>
<form class="dso-form" method="post">
    <input type="hidden" name="action" value="toggle_bookable">
    <label>
        <input type="checkbox" name="is_bookable" value="1" <?php echo $property->is_bookable ? 'checked' : ''; ?>>
        This property is bookable
    </label>
    <br><button type="submit" class="dso-btn">Save</button>
</form>

<h3>Blackout Dates</h3>
<table class="dso-table">
<tr><th>Date</th><th>Reason</th><th>Actions</th></tr>
<?php foreach ($blackout_dates as $b): ?>
<tr>
    <td><?php echo htmlspecialchars($b->blackout_date); ?></td>
    <td><?php echo htmlspecialchars($b->reason); ?></td>
    <td>
        <a href="<?php echo base_url('dyafa/properties/delete_blackout_date/' . $b->id . '/' . $property->id); ?>" onclick="return confirm('Remove this blackout date?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($blackout_dates)): ?>
<tr><td colspan="3">No blackout dates set.</td></tr>
<?php endif; ?>
</table>

<h3>Add Blackout Date</h3>
<form class="dso-form" method="post">
    <input type="hidden" name="action" value="add_blackout">
    <label>Date</label>
    <input type="date" name="blackout_date" required>

    <label>Reason</label>
    <input type="text" name="reason" placeholder="e.g. Maintenance, Renovation">

    <br><button type="submit" class="dso-btn">Add Blackout Date</button>
</form>

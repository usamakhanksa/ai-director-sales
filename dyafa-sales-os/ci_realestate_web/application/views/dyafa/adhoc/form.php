<h2><?php echo $item ? 'Edit Adhoc Sale' : 'Add Adhoc Sale'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Account (optional)</label>
    <select name="account_id">
        <option value="">-- none --</option>
        <?php foreach ($accounts as $a): ?>
        <option value="<?php echo $a->id; ?>" <?php echo ($item && $item->account_id == $a->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a->company_name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Event Type</label>
    <select name="event_type">
        <?php foreach (array('Wedding','Birthday','MeetingRoom','Event','Catering','Conference','Retreat','GroupBooking','CoffeeBreak') as $t): ?>
        <option value="<?php echo $t; ?>" <?php echo ($item && $item->event_type == $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
        <?php endforeach; ?>
    </select>

    <label>Venue / Property (optional)</label>
    <select name="venue_property_id">
        <option value="">-- none --</option>
        <?php foreach ($venue_properties as $p): ?>
        <option value="<?php echo $p->id; ?>" <?php echo ($item && $item->venue_property_id == $p->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Event Date</label>
    <input type="date" name="event_date" value="<?php echo $item ? $item->event_date : ''; ?>" required>

    <label>Pax</label>
    <input type="number" name="pax" value="<?php echo $item ? $item->pax : ''; ?>" required>

    <label>Estimated Value</label>
    <input type="number" step="0.01" name="estimated_value" value="<?php echo $item ? $item->estimated_value : ''; ?>" required>

    <label>Status</label>
    <select name="status">
        <?php foreach (array('Inquiry','ProposalSent','Negotiation','Confirmed','Completed','Cancelled','Lost') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($item && $item->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <label>Notes</label>
    <textarea name="notes" rows="3"><?php echo $item ? htmlspecialchars($item->notes) : ''; ?></textarea>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

<h2><?php echo $reservation ? 'Edit Reservation' : 'Add Reservation'; ?></h2>
<?php if (!empty($error)): ?><div class="dso-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Account</label>
    <select name="account_id">
        <?php foreach ($accounts as $a): ?>
        <option value="<?php echo $a->id; ?>" <?php echo ($reservation && $reservation->account_id == $a->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a->company_name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Property</label>
    <select name="property" required>
        <option value="">-- select property --</option>
        <?php foreach ($properties as $p): ?>
        <option value="<?php echo htmlspecialchars($p); ?>" <?php echo ($reservation && $reservation->property === $p) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p); ?></option>
        <?php endforeach; ?>
        <?php if ($reservation && !in_array($reservation->property, $properties, true)): ?>
        <option value="<?php echo htmlspecialchars($reservation->property); ?>" selected><?php echo htmlspecialchars($reservation->property); ?> (inactive/legacy)</option>
        <?php endif; ?>
    </select>
    <p style="font-size:12px;color:var(--color-muted);">Must match one of the account's contract allowed properties. Manage the property list under <a href="<?php echo base_url('dyafa/properties'); ?>">Properties</a>.</p>

    <label>Check-in</label>
    <input type="date" name="check_in" value="<?php echo $reservation ? $reservation->check_in : ''; ?>" required>

    <label>Check-out</label>
    <input type="date" name="check_out" value="<?php echo $reservation ? $reservation->check_out : ''; ?>" required>

    <label>Rate</label>
    <input type="number" step="0.01" name="rate" value="<?php echo $reservation ? $reservation->rate : ''; ?>" required>

    <label>Room Nights</label>
    <input type="number" name="room_nights" value="<?php echo $reservation ? $reservation->room_nights : ''; ?>" required>

    <label>Total Amount</label>
    <input type="number" step="0.01" name="total_amount" value="<?php echo $reservation ? $reservation->total_amount : ''; ?>" required>

    <label>Status</label>
    <select name="status">
        <?php foreach (array('Pending','Confirmed','CheckedIn','Extended','CheckedOut','Cancelled','NoShow') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($reservation && $reservation->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

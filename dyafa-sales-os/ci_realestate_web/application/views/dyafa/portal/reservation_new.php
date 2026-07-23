<h2>New Reservation Request</h2>
<?php if (!empty($error)): ?><div class="dso-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Property</label>
    <input type="text" name="property" required placeholder="Must be one of your contract's allowed properties" value="<?php echo htmlspecialchars($this->input->get('property') ?: ''); ?>">
    <label>Check-in</label>
    <input type="date" name="check_in" required>
    <label>Check-out</label>
    <input type="date" name="check_out" required>
    <label>Rate</label>
    <input type="number" step="0.01" name="rate" required value="<?php echo htmlspecialchars($this->input->get('rate') ?: ''); ?>">
    <label>Room Nights</label>
    <input type="number" name="room_nights" required>
    <label>Total Amount</label>
    <input type="number" step="0.01" name="total_amount" required>
    <br><button type="submit" class="dso-btn">Submit Request</button>
</form>

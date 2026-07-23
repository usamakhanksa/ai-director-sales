<h2>Log Activity</h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Type</label>
    <select name="activity_type">
        <?php foreach (array('Call','Meeting','Visit','FollowUp','Reservation','Collection','Complaint','Opportunity') as $t): ?>
        <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
        <?php endforeach; ?>
    </select>

    <label>Account (optional)</label>
    <select name="account_id">
        <option value="">-- None --</option>
        <?php foreach ($accounts as $acc): ?>
        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->company_name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Date/Time</label>
    <input type="datetime-local" name="activity_date" required>

    <label>Notes</label>
    <textarea name="notes" rows="3" required></textarea>

    <br><button type="submit" class="dso-btn">Log Activity</button>
</form>

<h2><?php echo $target ? 'Edit Target' : 'Add Target'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>User</label>
    <select name="user_id">
        <?php foreach ($users as $u): ?>
        <option value="<?php echo $u->id; ?>" <?php echo ($target && $target->user_id == $u->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Month (YYYY-MM)</label>
    <input type="month" name="month" value="<?php echo $target ? $target->month : date('Y-m'); ?>" required>

    <label>Revenue Target</label>
    <input type="number" step="0.01" name="revenue_target" value="<?php echo $target ? $target->revenue_target : ''; ?>" required>

    <label>Room Nights Target</label>
    <input type="number" name="room_nights_target" value="<?php echo $target ? $target->room_nights_target : 0; ?>">

    <label>Reservations Target</label>
    <input type="number" name="reservations_target" value="<?php echo $target ? $target->reservations_target : 0; ?>">

    <label>Collections Target</label>
    <input type="number" step="0.01" name="collections_target" value="<?php echo $target ? $target->collections_target : 0; ?>">

    <label>Adhoc Revenue Target</label>
    <input type="number" step="0.01" name="adhoc_revenue_target" value="<?php echo $target ? $target->adhoc_revenue_target : 0; ?>">

    <label>Meetings Target</label>
    <input type="number" name="meetings_target" value="<?php echo $target ? $target->meetings_target : 0; ?>">

    <label>Visits Target</label>
    <input type="number" name="visits_target" value="<?php echo $target ? $target->visits_target : 0; ?>">

    <label>Calls Target</label>
    <input type="number" name="calls_target" value="<?php echo $target ? $target->calls_target : 0; ?>">

    <label>New Leads Target</label>
    <input type="number" name="new_leads_target" value="<?php echo $target ? $target->new_leads_target : 0; ?>">

    <label>New Contracts Target</label>
    <input type="number" name="new_contracts_target" value="<?php echo $target ? $target->new_contracts_target : 0; ?>">

    <br><button type="submit" class="dso-btn">Save</button>
</form>

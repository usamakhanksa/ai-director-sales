<h2>Assign / Reassign Lead: <?php echo htmlspecialchars($lead->company_name); ?></h2>
<form class="dso-form" method="post">
    <label>Assign To</label>
    <select name="lead_owner_id">
        <?php foreach ($users as $u): ?>
        <option value="<?php echo $u->id; ?>" <?php echo ($lead->lead_owner_id == $u->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name) . ' (' . $u->role . ')'; ?></option>
        <?php endforeach; ?>
    </select>
    <br><button type="submit" class="dso-btn">Reassign</button>
</form>

<h2>Broadcast Notification</h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Target</label>
    <select name="target" required>
        <option value="all">Everyone</option>
        <?php foreach ($roles as $r): ?>
        <option value="role:<?php echo $r; ?>">Role: <?php echo $r; ?></option>
        <?php endforeach; ?>
        <?php foreach ($users as $u): ?>
        <option value="<?php echo $u->id; ?>">User: <?php echo htmlspecialchars($u->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Message</label>
    <textarea name="message" rows="4" required></textarea>

    <br><button type="submit" class="dso-btn">Send</button>
</form>

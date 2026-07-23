<h2><?php echo $user ? 'Edit User' : 'Add User'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo $user ? htmlspecialchars($user->name) : ''; ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo $user ? htmlspecialchars($user->email) : ''; ?>" required>

    <?php if (!$user): ?>
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>
    <?php else: ?>
    <label>New Password (leave blank to keep current)</label>
    <input type="password" name="password">
    <?php endif; ?>

    <label>Role</label>
    <select name="role_id" required>
        <option value="">-- Select Role --</option>
        <?php foreach ($roles as $r): ?>
        <option value="<?php echo $r->id; ?>" <?php echo ($user && $user->role_id == $r->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r->name); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Team</label>
    <select name="team_id">
        <option value="">-- No Team --</option>
        <?php foreach ($teams as $t): ?>
        <option value="<?php echo $t->id; ?>" <?php echo ($user && $user->team_id == $t->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($t->name); ?></option>
        <?php endforeach; ?>
    </select>

    <?php if ($user): ?>
    <label>Status</label>
    <select name="status">
        <option value="Active" <?php echo $user->status === 'Active' ? 'selected' : ''; ?>>Active</option>
        <option value="Inactive" <?php echo $user->status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <?php endif; ?>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

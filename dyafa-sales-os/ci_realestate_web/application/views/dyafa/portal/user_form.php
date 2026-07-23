<h2><?php echo isset($user) && $user ? 'Edit Company User' : 'Add Company User'; ?></h2>
<?php if (!empty($error)): ?><div class="dso-alert error"><?php echo $error; ?></div><?php endif; ?>
<form class="dso-form" method="post">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo isset($user) && $user ? htmlspecialchars($user->name) : ''; ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo isset($user) && $user ? htmlspecialchars($user->email) : ''; ?>" required>

    <label>Username</label>
    <input type="text" name="username" value="<?php echo isset($user) && $user ? htmlspecialchars($user->username) : ''; ?>" required>

    <label>Password<?php echo isset($user) && $user ? ' <span style="font-weight:400;color:var(--color-muted);">(leave blank to keep current password)</span>' : ''; ?></label>
    <input type="password" name="password" <?php echo isset($user) && $user ? '' : 'required'; ?> minlength="6">

    <label>Role</label>
    <select name="role">
        <?php
        $roles = array(
            'CorporateAdmin'             => 'Administrator',
            'CorporateHR'                => 'HR',
            'CorporateFinance'           => 'Finance',
            'CorporateTravelCoordinator' => 'Travel Coordinator',
            'CorporateProjectManager'    => 'Project Manager',
        );
        $current_role = isset($user) && $user ? $user->role : null;
        foreach ($roles as $value => $label):
        ?>
        <option value="<?php echo $value; ?>" <?php echo $current_role === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

<h2><?php echo $role ? 'Edit Role: ' . htmlspecialchars($role->name) : 'Add Role'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Role Name</label>
    <input type="text" name="name" value="<?php echo $role ? htmlspecialchars($role->name) : ''; ?>" <?php echo ($role && $role->is_system) ? 'readonly' : 'required'; ?>>

    <label>Permissions</label>
    <?php
    $grouped = array();
    foreach ($permissions as $p) {
        $grouped[$p->group_name][] = $p;
    }
    ?>
    <?php foreach ($grouped as $group => $perms): ?>
        <fieldset style="margin-bottom:12px;">
            <legend><?php echo htmlspecialchars($group); ?></legend>
            <?php foreach ($perms as $p): ?>
            <label style="display:block;font-weight:normal;">
                <input type="checkbox" name="permissions[]" value="<?php echo $p->id; ?>" <?php echo in_array($p->id, $assigned) ? 'checked' : ''; ?>>
                <?php echo htmlspecialchars($p->label); ?>
            </label>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>

    <br><button type="submit" class="dso-btn">Save</button>
</form>

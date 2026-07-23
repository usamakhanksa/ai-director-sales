<?php
/**
 * Server-side GET filter bar. Caller passes:
 *   $dso_filter_fields = array(
 *       array('name'=>'status','label'=>'Status','type'=>'select','options'=>array('Active'=>'Active', ...)),
 *       array('name'=>'q','label'=>'Search','type'=>'text'),
 *       array('name'=>'from','label'=>'From','type'=>'date'),
 *   );
 * Values are read back from $_GET so the bar reflects the current filter state.
 * Submits GET to the current URL (path only, so any URI-segment scope/tab is preserved).
 */
$dso_filter_action = strtok($_SERVER['REQUEST_URI'], '?');
?>
<?php if (!empty($dso_filter_fields)): ?>
<form method="get" action="<?php echo htmlspecialchars($dso_filter_action); ?>" class="dso-filters">
    <?php foreach ($dso_filter_fields as $f): ?>
        <?php $current = isset($_GET[$f['name']]) ? $_GET[$f['name']] : ''; ?>
        <?php if ($f['type'] === 'select'): ?>
            <select name="<?php echo htmlspecialchars($f['name']); ?>">
                <option value="">All <?php echo htmlspecialchars($f['label']); ?></option>
                <?php foreach ($f['options'] as $val => $label): ?>
                    <option value="<?php echo htmlspecialchars($val); ?>"<?php echo ($current !== '' && (string) $current === (string) $val) ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                <?php endforeach; ?>
            </select>
        <?php elseif ($f['type'] === 'date'): ?>
            <input type="date" name="<?php echo htmlspecialchars($f['name']); ?>" value="<?php echo htmlspecialchars($current); ?>" title="<?php echo htmlspecialchars($f['label']); ?>">
        <?php else: ?>
            <input type="text" name="<?php echo htmlspecialchars($f['name']); ?>" value="<?php echo htmlspecialchars($current); ?>" placeholder="<?php echo htmlspecialchars($f['label']); ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <button type="submit" class="dso-btn outline">Filter</button>
    <?php if (!empty($_GET)): ?><a href="<?php echo htmlspecialchars($dso_filter_action); ?>" class="dso-btn secondary">Reset</a><?php endif; ?>
</form>
<?php endif; ?>

<?php if (!empty($dso_tabs)): ?>
<div class="dso-tabs">
    <?php foreach ($dso_tabs as $tab): ?>
        <a href="<?php echo $tab['url']; ?>"<?php echo !empty($tab['active']) ? ' class="active"' : ''; ?>><?php echo htmlspecialchars($tab['label']); ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

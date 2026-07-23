<?php
/**
 * Entity-level tab bar for a single Corporate Account, tying together the
 * previously-disconnected view/view360/activities pages. Caller passes
 * $account (row with ->id, ->company_name) and $active_account_tab
 * ('info'|'360'|'activities'). Performance is a cross-account report (not
 * scoped to one account_id), so it links to the global report and is never
 * marked active from within an account's own tabs.
 */
$dso_account_tabs = array(
    array('label' => 'Account Info', 'url' => base_url('dyafa/accounts/view/' . $account->id), 'active' => $active_account_tab === 'info'),
    array('label' => '360° View',    'url' => base_url('dyafa/accounts/view360/' . $account->id), 'active' => $active_account_tab === '360'),
    array('label' => 'Activities',   'url' => base_url('dyafa/accounts/activities/' . $account->id), 'active' => $active_account_tab === 'activities'),
    array('label' => 'Performance',  'url' => base_url('dyafa/accounts/performance'), 'active' => false),
);
?>
<h2 style="margin-bottom:4px;"><?php echo htmlspecialchars($account->company_name); ?></h2>
<div class="dso-tabs">
    <?php foreach ($dso_account_tabs as $tab): ?>
        <a href="<?php echo $tab['url']; ?>"<?php echo $tab['active'] ? ' class="active"' : ''; ?>><?php echo htmlspecialchars($tab['label']); ?></a>
    <?php endforeach; ?>
</div>

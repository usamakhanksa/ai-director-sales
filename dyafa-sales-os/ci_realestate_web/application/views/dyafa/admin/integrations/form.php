<h2>Integrations</h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>
<p style="font-size:13px;color:var(--color-muted);">Each integration runs in <b>mock</b> (deterministic fake responses, no network call), <b>live</b> (real HTTP call, falls back to mock on failure), or <b>off</b> mode. API keys are encrypted at rest and never displayed - only the last 4 characters are shown.</p>
<form class="dso-form" method="post">
    <?php $labels = array('dso_pms' => 'PMS', 'dso_finance' => 'Finance / ERP', 'dso_maps' => 'Maps', 'dso_payment' => 'Payment Gateway', 'dso_reporting' => 'Reporting Platform'); ?>
    <?php foreach ($integrations as $prefix => $cfg): ?>
    <fieldset class="dso-card" style="margin-bottom:16px;">
        <legend><?php echo $labels[$prefix]; ?></legend>
        <label>Mode</label>
        <select name="<?php echo $prefix; ?>_mode">
            <?php foreach (array('mock', 'live', 'off') as $m): ?>
            <option value="<?php echo $m; ?>" <?php echo $cfg['mode'] === $m ? 'selected' : ''; ?>><?php echo ucfirst($m); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Endpoint</label>
        <input type="text" name="<?php echo $prefix; ?>_endpoint" value="<?php echo htmlspecialchars($cfg['endpoint']); ?>">

        <label>API Key<?php echo $cfg['key_last4'] ? ' <span style="font-weight:400;color:var(--color-muted);">(current key ends in ' . htmlspecialchars($cfg['key_last4']) . ' - leave blank to keep it)</span>' : ''; ?></label>
        <input type="text" name="<?php echo $prefix; ?>_api_key" value="" placeholder="<?php echo $cfg['key_last4'] ? str_repeat('•', 12) . htmlspecialchars($cfg['key_last4']) : 'Enter API key'; ?>" autocomplete="off">

        <label>Timeout (seconds)</label>
        <input type="number" name="<?php echo $prefix; ?>_timeout" value="<?php echo (int) $cfg['timeout']; ?>">
    </fieldset>
    <?php endforeach; ?>
    <button type="submit" class="dso-btn">Save Integrations</button>
</form>

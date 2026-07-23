<h2><?php echo $provider ? 'Edit Provider' : 'Add Provider'; ?></h2>
<?php if (!empty($error)): ?><div class="dso-alert error"><?php echo $error; ?></div><?php endif; ?>

<?php
$extra = array();
if ($provider && !empty($provider->extra_params)) {
    $decoded = is_array($provider->extra_params) ? $provider->extra_params : json_decode($provider->extra_params, true);
    if (is_array($decoded)) { $extra = $decoded; }
}
?>

<form class="dso-form" method="post" id="dso-ai-provider-form">
    <label>Provider</label>
    <select name="provider_key" id="provider_key">
        <?php foreach ($provider_meta as $key => $meta): ?>
        <option value="<?php echo htmlspecialchars($key); ?>"
            data-base-url="<?php echo htmlspecialchars($meta['base_url']); ?>"
            data-model="<?php echo htmlspecialchars($meta['default_model']); ?>"
            data-needs-key="<?php echo $meta['needs_key'] ? '1' : '0'; ?>"
            <?php echo ($provider && $provider->provider_key === $key) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($meta['label']); ?><?php echo $meta['free'] ? ' (free tier available)' : ''; ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Label</label>
    <input type="text" name="label" id="label" value="<?php echo $provider ? htmlspecialchars($provider->label) : ''; ?>" required>

    <label>Base URL</label>
    <input type="text" name="base_url" id="base_url" value="<?php echo $provider ? htmlspecialchars($provider->base_url) : ''; ?>" required>

    <label>Model</label>
    <input type="text" name="model" id="model" value="<?php echo $provider ? htmlspecialchars($provider->model) : ''; ?>" required>

    <label>API Key <?php echo $provider ? '(leave blank to keep current key)' : ''; ?></label>
    <input type="password" name="api_key" id="api_key" placeholder="<?php echo ($provider && $provider->key_last4) ? '••••' . htmlspecialchars($provider->key_last4) : 'sk-...'; ?>" autocomplete="new-password">

    <label>Temperature</label>
    <input type="text" name="temperature" value="<?php echo isset($extra['temperature']) ? htmlspecialchars($extra['temperature']) : '0.3'; ?>">

    <label>Max Tokens</label>
    <input type="number" name="max_tokens" value="<?php echo isset($extra['max_tokens']) ? (int) $extra['max_tokens'] : 300; ?>">

    <label>Advanced (raw JSON, merged into extra_params - e.g. Azure <code>api_version</code>/<code>azure_deployment</code>)</label>
    <textarea name="advanced_json" rows="2" placeholder="{}"><?php
        $advanced_only = $extra;
        unset($advanced_only['temperature'], $advanced_only['max_tokens']);
        echo !empty($advanced_only) ? htmlspecialchars(json_encode($advanced_only)) : '';
    ?></textarea>

    <label><input type="checkbox" name="is_enabled" value="1" style="width:auto;display:inline-block;" <?php echo (!$provider || $provider->is_enabled) ? 'checked' : ''; ?>> Enabled</label>

    <br>
    <button type="submit" class="dso-btn">Save</button>
    <button type="button" class="dso-btn" id="dso-test-btn">Test Connection</button>
    <div class="dso-test-result" id="dso-form-test-result"></div>
</form>

<script>
(function () {
    var select = document.getElementById('provider_key');
    select.addEventListener('change', function () {
        var opt = select.options[select.selectedIndex];
        if (!document.getElementById('base_url').value || document.getElementById('base_url').dataset.auto !== '0') {
            document.getElementById('base_url').value = opt.dataset.baseUrl;
        }
        if (!document.getElementById('model').value) {
            document.getElementById('model').value = opt.dataset.model;
        }
        if (!document.getElementById('label').value) {
            document.getElementById('label').value = opt.text.replace(/\s*\(free.*\)$/, '');
        }
    });

    document.getElementById('dso-test-btn').addEventListener('click', function () {
        var resultEl = document.getElementById('dso-form-test-result');
        var btn = this;
        btn.disabled = true;
        resultEl.textContent = 'Testing...';
        resultEl.className = 'dso-test-result';
        var body = new FormData();
        body.append('provider_key', document.getElementById('provider_key').value);
        body.append('base_url', document.getElementById('base_url').value);
        body.append('model', document.getElementById('model').value);
        body.append('api_key', document.getElementById('api_key').value);
        fetch('<?php echo base_url('dyafa/aiconfig/test'); ?>', { method: 'POST', body: body })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                resultEl.textContent = (data.success ? 'Success' : 'Failed') + ' (' + data.latency_ms + 'ms): ' + data.message;
                resultEl.className = 'dso-test-result ' + (data.success ? 'ok' : 'fail');
            })
            .catch(function (err) {
                resultEl.textContent = 'Request failed: ' + err;
                resultEl.className = 'dso-test-result fail';
            })
            .finally(function () { btn.disabled = false; });
    });
})();
</script>

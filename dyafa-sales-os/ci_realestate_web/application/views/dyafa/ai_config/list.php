<h2>AI Config</h2>
<p style="font-size:12px;color:var(--color-muted);">Configure the LLM provider used to enhance AI Sales Assistant recommendations (<a href="<?php echo base_url('dyafa/aiassistant'); ?>">view recommendations</a>). Exactly one enabled provider can be the default at a time; if none is set, the assistant falls back to its built-in heuristic text.</p>

<?php if ($this->session->flashdata('dso_success')): ?>
<div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div>
<?php endif; ?>

<p><a class="dso-btn" href="<?php echo base_url('dyafa/aiconfig/add'); ?>">+ Add Provider</a></p>

<div class="dso-provider-grid">
<?php if (empty($providers)): ?>
    <div class="dso-card">No providers configured yet.</div>
<?php endif; ?>
<?php foreach ($providers as $p): ?>
    <div class="dso-card dso-provider-card">
        <div class="dso-provider-card-head">
            <strong><?php echo htmlspecialchars($p->label); ?></strong>
            <?php if ($p->is_default): ?><span class="dso-badge accent">Default</span><?php endif; ?>
            <?php if (!$p->is_enabled): ?><span class="dso-badge muted">Disabled</span><?php endif; ?>
        </div>
        <div class="dso-provider-meta">
            <div><?php echo htmlspecialchars(isset($provider_meta[$p->provider_key]['label']) ? $provider_meta[$p->provider_key]['label'] : $p->provider_key); ?></div>
            <div>Model: <code><?php echo htmlspecialchars($p->model); ?></code></div>
            <div>Key: <?php echo $p->key_last4 ? '&bull;&bull;&bull;&bull;' . htmlspecialchars($p->key_last4) : '<em>none (local)</em>'; ?></div>
            <div>Last test:
                <span class="dso-badge <?php echo $p->last_test_status === 'Success' ? 'success' : ($p->last_test_status === 'Failed' ? 'danger' : 'muted'); ?>">
                    <?php echo $p->last_test_status; ?>
                </span>
                <?php if ($p->last_tested_at): ?><span style="font-size:11px;color:var(--color-muted);"><?php echo $p->last_tested_at; ?></span><?php endif; ?>
            </div>
        </div>
        <div class="dso-provider-actions">
            <a class="dso-btn" href="<?php echo base_url('dyafa/aiconfig/edit/' . $p->id); ?>">Edit</a>
            <button type="button" class="dso-btn" onclick="dsoTestProvider(<?php echo (int) $p->id; ?>, this)">Test</button>
            <?php if (!$p->is_default): ?>
            <form method="post" action="<?php echo base_url('dyafa/aiconfig/set_default/' . $p->id); ?>" style="display:inline;">
                <button type="submit" class="dso-btn">Set Default</button>
            </form>
            <?php endif; ?>
            <form method="post" action="<?php echo base_url('dyafa/aiconfig/delete/' . $p->id); ?>" style="display:inline;" onsubmit="return confirm('Delete this provider?');">
                <button type="submit" class="dso-btn danger">Delete</button>
            </form>
        </div>
        <div class="dso-test-result" id="test-result-<?php echo (int) $p->id; ?>"></div>
    </div>
<?php endforeach; ?>
</div>

<script>
function dsoTestProvider(id, btn) {
    var resultEl = document.getElementById('test-result-' + id);
    btn.disabled = true;
    resultEl.textContent = 'Testing...';
    resultEl.className = 'dso-test-result';
    var body = new FormData();
    body.append('id', id);
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
}
</script>

<h2>Lead Scoring Config</h2>
<p style="font-size:12px;color:var(--color-muted);">Tune how much each signal contributes to a lead's score out of 100 (see the AI Lead Generation heuristic in Dso_lead_scoring.php). Weights don't need to add up to any particular total; each is applied as its own capped component.</p>

<?php if ($this->session->flashdata('dso_success')): ?>
<div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div>
<?php endif; ?>

<form class="dso-form" method="post" action="<?php echo base_url('dyafa/leadscoringconfig/save'); ?>">
    <?php foreach ($weights as $w): ?>
    <label><?php echo htmlspecialchars($w->label); ?> <span style="color:var(--color-muted);">(<?php echo htmlspecialchars($w->signal_key); ?>)</span></label>
    <input type="number" min="0" max="100" name="weights[<?php echo htmlspecialchars($w->signal_key); ?>]" value="<?php echo (int) $w->weight; ?>">
    <?php endforeach; ?>

    <?php if (empty($weights)): ?>
    <div class="dso-alert">No scoring config rows found yet - run migration 008 to seed defaults. Dso_lead_scoring.php falls back to its built-in defaults until then.</div>
    <?php endif; ?>

    <br>
    <button type="submit" class="dso-btn">Save Weights</button>
</form>

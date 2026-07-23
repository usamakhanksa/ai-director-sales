<h2>Generate Leads</h2>
<p style="font-size:12px;color:var(--color-muted);">
    Synthesizes candidate leads from the seeded Market Intelligence signals, scores each through the
    AI Lead Scoring heuristic, discards anything scored 'Discard', de-dupes by company name against
    existing leads, and inserts the rest assigned to the current HOD Sales user. This is the same
    logic the scheduled cron job runs automatically - use this to run it on demand.
</p>

<div class="dso-card">
    <form method="post" action="<?php echo base_url('dyafa/leadgeneration/generate'); ?>" onsubmit="return confirm('Generate new AI leads now?');">
        <button type="submit" class="dso-btn">Generate Leads Now</button>
    </form>
</div>

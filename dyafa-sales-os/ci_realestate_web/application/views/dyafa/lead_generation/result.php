<h2>Generate Leads</h2>

<div class="dso-alert success">
    Lead generation run complete: <?php echo (int) $result['inserted']; ?> lead(s) created
    out of <?php echo (int) $result['candidates']; ?> candidate(s) synthesized
    (duplicates by company name and 'Discard'-scored candidates are skipped).
</div>

<p>
    <a class="dso-btn" href="<?php echo base_url('dyafa/leadgeneration'); ?>">Run Again</a>
    <a class="dso-btn" href="<?php echo base_url('dyafa/leads'); ?>">View Leads</a>
</p>

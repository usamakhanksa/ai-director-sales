<h2><?php echo $lead ? 'Edit Lead' : 'Add Lead'; ?></h2>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post">
    <label>Company Name</label>
    <input type="text" name="company_name" value="<?php echo $lead ? htmlspecialchars($lead->company_name) : ''; ?>" required>

    <label>Industry</label>
    <input type="text" name="industry" value="<?php echo $lead ? htmlspecialchars($lead->industry) : ''; ?>">

    <label>Contact Person</label>
    <input type="text" name="contact_person" value="<?php echo $lead ? htmlspecialchars($lead->contact_person) : ''; ?>" required>

    <label>Mobile</label>
    <input type="text" name="mobile" value="<?php echo $lead ? htmlspecialchars($lead->mobile) : ''; ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo $lead ? htmlspecialchars($lead->email) : ''; ?>">

    <label>City</label>
    <input type="text" name="city" value="<?php echo $lead ? htmlspecialchars($lead->city) : ''; ?>">

    <label>Estimated Revenue</label>
    <input type="number" step="0.01" name="estimated_revenue" value="<?php echo $lead ? $lead->estimated_revenue : ''; ?>" required>

    <label>Estimated Room Nights</label>
    <input type="number" name="estimated_room_nights" value="<?php echo $lead ? $lead->estimated_room_nights : ''; ?>" required>

    <label>Priority</label>
    <select name="priority">
        <?php foreach (array('Low','Medium','High') as $p): ?>
        <option value="<?php echo $p; ?>" <?php echo ($lead && $lead->priority == $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
        <?php endforeach; ?>
    </select>

    <label>Source</label>
    <select name="source">
        <?php foreach (array('Referral','Website','ColdCall','Event','Partner','Other') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($lead && $lead->source == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <?php if ($lead): ?>
    <label>Status</label>
    <select name="status">
        <?php foreach (array('New','Contacted','Qualified','ProposalSent','Negotiation','Won','Lost') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($lead->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>

    <br><button type="submit" class="dso-btn">Save</button>
</form>
<p style="font-size:12px;color:var(--color-muted);">Lead score, category and suggested next action are computed automatically by the heuristic scoring engine on save.</p>

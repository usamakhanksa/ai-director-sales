<h2><?php echo htmlspecialchars($lead->company_name); ?></h2>
<div class="dso-card">
    <table class="dso-table">
        <tr><th>Contact</th><td><?php echo htmlspecialchars($lead->contact_person); ?> (<?php echo htmlspecialchars($lead->mobile); ?>)</td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($lead->email); ?></td></tr>
        <tr><th>City</th><td><?php echo htmlspecialchars($lead->city); ?></td></tr>
        <tr><th>Estimated Revenue</th><td><?php echo number_format($lead->estimated_revenue, 2); ?></td></tr>
        <tr><th>Estimated Room Nights</th><td><?php echo $lead->estimated_room_nights; ?></td></tr>
        <tr><th>Priority</th><td><?php echo $lead->priority; ?></td></tr>
        <tr><th>Score</th><td><?php echo $lead->lead_score; ?></td></tr>
        <tr><th>Category</th><td><span class="dso-badge"><?php echo $lead->lead_category; ?></span></td></tr>
        <tr><th>Suggested Next Action</th><td><?php echo htmlspecialchars($lead->suggested_next_action); ?></td></tr>
        <tr><th>Owner</th><td><?php echo $owner ? htmlspecialchars($owner->name) : '-'; ?></td></tr>
        <tr><th>Status</th><td><?php echo $lead->status; ?></td></tr>
    </table>
</div>
<a class="dso-btn" href="<?php echo base_url('dyafa/leads'); ?>">Back to Leads</a>

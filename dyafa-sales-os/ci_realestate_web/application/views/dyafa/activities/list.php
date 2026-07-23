<h2><?php echo $scope === 'team' ? 'Team Activities' : 'My Activities'; ?></h2>
<?php if ($this->session->flashdata('dso_success')): ?><div class="dso-alert success"><?php echo $this->session->flashdata('dso_success'); ?></div><?php endif; ?>

<a class="dso-btn" href="<?php echo base_url('dyafa/activities/add'); ?>">+ Log Activity</a>
<br><br>

<?php $this->load->view('dyafa/partials/list_tabs', array('dso_tabs' => $dso_tabs)); ?>
<?php $this->load->view('dyafa/partials/list_filters', array('dso_filter_fields' => $dso_filter_fields)); ?>

<?php if (!empty($team_message)): ?>
<div class="dso-alert">Your team has no members yet.</div>
<?php endif; ?>

<table class="dso-table">
<tr><th>Date</th><th>Type</th><th>Notes</th><th>Account</th></tr>
<?php foreach ($activities as $a): ?>
<tr>
    <td><?php echo $a->activity_date; ?></td>
    <td><?php echo $a->activity_type; ?></td>
    <td><?php echo htmlspecialchars($a->notes); ?></td>
    <td><?php echo $a->account_id ? '<a href="' . base_url('dyafa/accounts/view/' . $a->account_id) . '">#' . $a->account_id . '</a>' : '-'; ?></td>
</tr>
<?php endforeach; ?>
</table>

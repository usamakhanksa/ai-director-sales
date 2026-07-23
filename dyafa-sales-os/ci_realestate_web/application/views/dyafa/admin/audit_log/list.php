<h2>Audit Log</h2>
<p style="font-size:12px;color:var(--color-muted);">Every create/update/delete on Contracts, Corporate Accounts, Adhoc Sales, Properties, Collections, Targets, Roles, and Teams, most recent 200 shown. Deletes are soft (see the <code>deleted_at</code> column on each table) - nothing here is ever un-recoverable.</p>

<form class="dso-filters" method="get">
    <select name="table_name">
        <option value="">All tables</option>
        <?php foreach ($tables as $t): ?>
        <option value="<?php echo $t; ?>" <?php echo $selected_table === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="row_id" placeholder="Row ID" value="<?php echo htmlspecialchars((string) $selected_row_id); ?>">
    <button type="submit" class="dso-btn">Filter</button>
</form>

<table class="dso-table">
<tr><th>When</th><th>User</th><th>Table</th><th>Row ID</th><th>Action</th><th>Before</th><th>After</th></tr>
<?php foreach ($entries as $e): ?>
<tr>
    <td><?php echo $e->created_at; ?></td>
    <td><?php echo htmlspecialchars($e->user_name ?: 'System'); ?></td>
    <td><?php echo htmlspecialchars($e->table_name); ?></td>
    <td><?php echo (int) $e->row_id; ?></td>
    <td><span class="dso-badge <?php echo $e->action === 'delete' ? 'danger' : ($e->action === 'create' ? 'accent' : ''); ?>"><?php echo ucfirst($e->action); ?></span></td>
    <td style="max-width:260px;overflow-wrap:break-word;font-size:11px;"><?php echo $e->before_json ? htmlspecialchars($e->before_json) : '&mdash;'; ?></td>
    <td style="max-width:260px;overflow-wrap:break-word;font-size:11px;"><?php echo $e->after_json ? htmlspecialchars($e->after_json) : '&mdash;'; ?></td>
</tr>
<?php endforeach; ?>
</table>

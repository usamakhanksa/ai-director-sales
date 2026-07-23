<h2>Team Performance - <?php echo $month; ?></h2>

<div class="dso-card">
    <table class="dso-table">
        <tr><th>Team</th><th>Revenue</th><th>Room Nights</th><th>Collections</th></tr>
        <?php foreach ($teams as $t): ?>
        <tr>
            <td><?php echo htmlspecialchars($t->name); ?></td>
            <td><?php echo number_format($t->revenue, 2); ?></td>
            <td><?php echo $t->room_nights; ?></td>
            <td><?php echo number_format($t->collections, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

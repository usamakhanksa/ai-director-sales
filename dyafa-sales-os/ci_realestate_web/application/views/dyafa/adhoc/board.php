<h2>Opportunities Board</h2>
<p style="color:var(--color-muted);font-size:13px;margin-top:-8px;">
    Drag a card to a new column to update its status.
</p>

<div class="dso-board" id="dsoBoard">
<?php foreach ($statuses as $status): $cards = isset($columns[$status]) ? $columns[$status] : array(); ?>
    <div class="dso-board-col" data-status="<?php echo htmlspecialchars($status); ?>">
        <div class="dso-board-col-head">
            <span><?php echo htmlspecialchars($status); ?></span>
            <span class="dso-badge"><?php echo count($cards); ?></span>
        </div>
        <div class="dso-board-col-body" data-status="<?php echo htmlspecialchars($status); ?>">
        <?php foreach ($cards as $c): ?>
            <div class="dso-board-card" draggable="true" data-id="<?php echo (int) $c->id; ?>">
                <div class="dso-board-card-type"><?php echo htmlspecialchars($c->event_type); ?></div>
                <div class="dso-board-card-account"><?php echo htmlspecialchars($c->account_id ? ('Account #' . $c->account_id) : '-'); ?></div>
                <div class="dso-board-card-meta">
                    <span><?php echo htmlspecialchars($c->event_date); ?></span>
                    <span><?php echo number_format($c->estimated_value, 2); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

<style>
    .dso-board{display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;}
    .dso-board-col{
        flex:0 0 250px;background:var(--color-card-bg);border:1px solid var(--color-border-soft);
        border-radius:var(--radius-md);box-shadow:var(--shadow-sm);display:flex;flex-direction:column;max-height:75vh;
    }
    .dso-board-col-head{
        display:flex;align-items:center;justify-content:space-between;
        padding:12px 14px;font-weight:700;font-size:13px;
        border-bottom:1px solid var(--color-border-soft);background:var(--color-table-header-bg);
        border-radius:var(--radius-md) var(--radius-md) 0 0;
    }
    .dso-board-col-body{flex:1;overflow-y:auto;padding:10px;display:flex;flex-direction:column;gap:10px;min-height:60px;}
    .dso-board-col-body.dso-drag-over{background:var(--color-accent-soft);}
    .dso-board-card{
        background:var(--color-bg);border:1px solid var(--color-border-soft);border-radius:var(--radius-sm);
        padding:10px 12px;cursor:grab;box-shadow:var(--shadow-sm);font-size:12.5px;
    }
    .dso-board-card:active{cursor:grabbing;}
    .dso-board-card.dso-dragging{opacity:0.4;}
    .dso-board-card-type{font-weight:700;margin-bottom:4px;}
    .dso-board-card-account{color:var(--color-muted);margin-bottom:6px;}
    .dso-board-card-meta{display:flex;justify-content:space-between;color:var(--color-text);font-weight:600;}
</style>

<script>
(function () {
    'use strict';
    var board = document.getElementById('dsoBoard');
    if (!board) { return; }

    var moveUrl = '<?php echo base_url('dyafa/adhoc/board_move/'); ?>';
    var dragged = null;

    board.querySelectorAll('.dso-board-card').forEach(function (card) {
        card.addEventListener('dragstart', function () {
            dragged = card;
            card.classList.add('dso-dragging');
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('dso-dragging');
            dragged = null;
        });
    });

    board.querySelectorAll('.dso-board-col-body').forEach(function (col) {
        col.addEventListener('dragover', function (e) {
            e.preventDefault();
            col.classList.add('dso-drag-over');
        });
        col.addEventListener('dragleave', function () {
            col.classList.remove('dso-drag-over');
        });
        col.addEventListener('drop', function (e) {
            e.preventDefault();
            col.classList.remove('dso-drag-over');
            if (!dragged) { return; }

            var id = dragged.getAttribute('data-id');
            var newStatus = col.getAttribute('data-status');
            var previousParent = dragged.parentNode;
            var previousNext = dragged.nextSibling;

            col.appendChild(dragged);

            fetch(moveUrl + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=' + encodeURIComponent(newStatus)
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) {
                    previousParent.insertBefore(dragged, previousNext);
                    alert(res.message || 'Could not update status.');
                }
            })
            .catch(function () {
                previousParent.insertBefore(dragged, previousNext);
                alert('Network error while updating status.');
            });
        });
    });
})();
</script>

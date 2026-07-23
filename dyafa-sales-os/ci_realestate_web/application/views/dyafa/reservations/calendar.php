<h2>Reservation Calendar</h2>

<p>
    <a class="dso-btn" href="<?php echo base_url('dyafa/reservations/calendar?month=' . $prev_month); ?>">&laquo; Prev</a>
    <strong style="margin:0 10px;"><?php echo date('F Y', strtotime($month . '-01')); ?></strong>
    <a class="dso-btn" href="<?php echo base_url('dyafa/reservations/calendar?month=' . $next_month); ?>">Next &raquo;</a>
    <a class="dso-btn" href="<?php echo base_url('dyafa/reservations'); ?>" style="float:right;">Back to List</a>
</p>

<div id="dso-calendar-message"></div>

<div class="dso-calendar-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
    <?php foreach (array('Mon','Tue','Wed','Thu','Fri','Sat','Sun') as $wd): ?>
        <div style="font-weight:bold;text-align:center;padding:4px;"><?php echo $wd; ?></div>
    <?php endforeach; ?>

    <?php for ($i = 1; $i < $start_weekday; $i++): ?>
        <div></div>
    <?php endfor; ?>

    <?php for ($d = 1; $d <= $days_in_month; $d++):
        $date_str = $month . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
    ?>
    <div class="dso-card dso-calendar-day" data-date="<?php echo $date_str; ?>"
         ondragover="dsoAllowDrop(event)" ondrop="dsoHandleDrop(event, '<?php echo $date_str; ?>')"
         style="min-height:90px;padding:4px;font-size:12px;">
        <div style="font-weight:bold;color:var(--color-muted);"><?php echo $d; ?></div>
        <?php foreach ($by_day[$d] as $r): ?>
            <div class="dso-badge dso-calendar-item" draggable="true"
                 ondragstart="dsoHandleDragStart(event, <?php echo (int) $r->id; ?>)"
                 data-id="<?php echo (int) $r->id; ?>"
                 data-check-in="<?php echo $r->check_in; ?>"
                 data-check-out="<?php echo $r->check_out; ?>"
                 title="<?php echo htmlspecialchars($r->property . ' (' . $r->status . ')'); ?>"
                 style="display:block;margin-top:2px;cursor:move;">
                #<?php echo (int) $r->id; ?> <?php echo htmlspecialchars($r->property); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endfor; ?>
</div>

<script>
function dsoAllowDrop(ev) {
    ev.preventDefault();
}

function dsoHandleDragStart(ev, id) {
    ev.dataTransfer.setData('text/plain', String(id));
}

function dsoHandleDrop(ev, newDate) {
    ev.preventDefault();
    var id = ev.dataTransfer.getData('text/plain');
    var item = document.querySelector('.dso-calendar-item[data-id="' + id + '"]');
    if (!item) {
        return;
    }
    var oldCheckIn = item.getAttribute('data-check-in');
    var oldCheckOut = item.getAttribute('data-check-out');
    var oldStart = new Date(oldCheckIn);
    var oldEnd = new Date(oldCheckOut);
    var nights = Math.round((oldEnd - oldStart) / 86400000);
    if (nights < 0) {
        nights = 0;
    }
    var newStart = new Date(newDate);
    var newEnd = new Date(newStart.getTime() + nights * 86400000);

    function fmt(d) {
        var y = d.getFullYear();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return y + '-' + m + '-' + day;
    }

    var messageEl = document.getElementById('dso-calendar-message');
    messageEl.textContent = 'Moving reservation #' + id + '...';

    var body = new FormData();
    body.append('check_in', fmt(newStart));
    body.append('check_out', fmt(newEnd));

    fetch('<?php echo base_url('dyafa/reservations/calendar_move/'); ?>' + id, {
        method: 'POST',
        body: body
    })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                messageEl.textContent = 'Reservation #' + id + ' moved.';
                location.reload();
            } else {
                messageEl.textContent = 'Failed: ' + data.message;
            }
        })
        .catch(function (err) {
            messageEl.textContent = 'Request failed: ' + err;
        });
}
</script>

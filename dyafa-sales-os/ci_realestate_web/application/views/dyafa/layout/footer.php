        </div>
    </div>
</div>
<script>
(function () {
    'use strict';
    var STORAGE_KEY = 'dso_open_menu_groups';
    var groups = document.querySelectorAll('.dso-nav-group');
    var openKeys = [];
    try {
        openKeys = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch (e) {
        openKeys = [];
    }

    function persist() {
        var keys = [];
        groups.forEach(function (g) {
            if (g.classList.contains('dso-open')) {
                keys.push(g.getAttribute('data-menu-key'));
            }
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(keys));
    }

    groups.forEach(function (group) {
        var key = group.getAttribute('data-menu-key');
        if (!group.classList.contains('dso-open') && openKeys.indexOf(key) !== -1) {
            group.classList.add('dso-open');
        }
        var toggle = group.querySelector('.dso-nav-group-toggle');
        toggle.addEventListener('click', function () {
            var wasOpen = group.classList.contains('dso-open');
            // Accordion: close every other group before opening this one.
            groups.forEach(function (g) { g.classList.remove('dso-open'); });
            if (!wasOpen) {
                group.classList.add('dso-open');
            }
            persist();
        });
    });
})();
</script>
<script>
(function () {
    'use strict';
    // Every dso list page renders <table class="dso-table"><tr>header</tr>...data rows...</table>
    // with no <thead>/<tbody> - DataTables needs those to tell header from data, so we
    // synthesize them here rather than editing every one of the ~50 list views.
    // Tables marked data-server-paginated already receive a real LIMIT/OFFSET-backed
    // page of rows plus their own <div class="dso-pagination"> links (see
    // Dso_Controller::paginate(), used by Leads/Reservations/Notifications) - re-wrapping
    // those in a second, client-side pager would page an already-limited slice and show
    // two conflicting pagination controls, so they are skipped here.
    document.querySelectorAll('table.dso-table:not([data-server-paginated])').forEach(function (table) {
        if (table.tHead || !window.jQuery || !jQuery.fn.DataTable) {
            return;
        }
        var rows = table.querySelectorAll(':scope > tr');
        if (rows.length < 2) {
            return; // no data rows to page/search - leave as a plain table
        }
        var headerRow = rows[0];
        var onlyHeaderCells = Array.prototype.every.call(headerRow.children, function (c) {
            return c.tagName === 'TH';
        });
        if (!onlyHeaderCells) {
            return;
        }

        var thead = document.createElement('thead');
        thead.appendChild(headerRow);
        var tbody = document.createElement('tbody');
        Array.prototype.slice.call(table.querySelectorAll(':scope > tr')).forEach(function (r) {
            tbody.appendChild(r);
        });
        table.insertBefore(thead, table.firstChild);
        table.appendChild(tbody);

        jQuery(table).DataTable({
            pagingType: 'simple_numbers',
            pageLength: 10,
            lengthChange: false,
            language: { search: '', searchPlaceholder: 'Search...' },
        });
    });
})();
</script>
</body>
</html>

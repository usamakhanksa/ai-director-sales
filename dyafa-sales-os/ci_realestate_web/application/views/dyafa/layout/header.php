<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dyafa Sales OS</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%232a273c'/%3E%3Crect x='4' y='4' width='56' height='56' rx='11' fill='%23e95a54'/%3E%3Ctext x='32' y='43' font-family='Segoe UI,Arial,sans-serif' font-size='26' font-weight='800' fill='%23ffffff' text-anchor='middle'%3EDS%3C/text%3E%3C/svg%3E">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
<style>
    /*
     * Dyafa Sales OS brand palette - exactly 5 hex values, everything else
     * derived from them with color-mix(). Do not introduce new raw hex
     * colors below; add a variable here instead.
     */
    :root{
        --color-bg: #f2f0eb;
        --color-nav-bg: #2a273c;
        --color-nav-bg-2: color-mix(in srgb, #2a273c 88%, black);
        --color-nav-text: #f2f0eb;
        --color-nav-text-muted: color-mix(in srgb, #f2f0eb 55%, #2a273c);
        --color-text: #2a273c;
        --color-accent: #e95a54;
        --color-accent-hover: color-mix(in srgb, #e95a54 85%, black);
        --color-accent-soft: color-mix(in srgb, #e95a54 14%, white);
        --color-accent-2: #fbcdab;
        --color-accent-2-soft: color-mix(in srgb, #fbcdab 35%, white);
        --color-muted: #8f9793;
        --color-muted-soft: color-mix(in srgb, #8f9793 16%, white);
        --color-border: color-mix(in srgb, #8f9793 35%, white);
        --color-border-soft: color-mix(in srgb, #8f9793 18%, white);
        --color-table-header-bg: color-mix(in srgb, #f2f0eb 70%, #8f9793 30%);
        --color-danger: #e95a54;
        --color-danger-bg: color-mix(in srgb, #e95a54 12%, white);
        --color-warning: color-mix(in srgb, #fbcdab 55%, #2a273c 20%);
        --color-warning-bg: color-mix(in srgb, #fbcdab 30%, white);
        --color-success: #8f9793;
        --color-success-bg: color-mix(in srgb, #8f9793 15%, white);
        --color-card-bg: #ffffff;
        --shadow-sm: 0 1px 2px color-mix(in srgb, #2a273c 8%, transparent);
        --shadow-md: 0 4px 16px color-mix(in srgb, #2a273c 10%, transparent);
        --shadow-lg: 0 12px 32px color-mix(in srgb, #2a273c 16%, transparent);
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 16px;
        --sidebar-width: 250px;
        --topbar-height: 64px;
    }
    *{box-sizing:border-box;}
    html,body{height:100%;}
    body{
        font-family:"Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,Helvetica,sans-serif;
        background:var(--color-bg);
        margin:0;
        color:var(--color-text);
        -webkit-font-smoothing:antialiased;
    }
    a{color:inherit;}
    h1,h2,h3,h4{margin:0 0 12px;color:var(--color-text);letter-spacing:-0.01em;}
    h2{font-size:22px;font-weight:700;}
    h3{font-size:16px;font-weight:700;}

    /* ---------- App shell ---------- */
    .dso-shell{display:flex;min-height:100vh;}

    /* ---------- Sidebar ---------- */
    .dso-nav{
        position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-width);
        background:linear-gradient(180deg, var(--color-nav-bg) 0%, var(--color-nav-bg-2) 100%);
        color:var(--color-nav-text);
        display:flex;flex-direction:column;
        z-index:100;
        box-shadow:var(--shadow-lg);
        transition:transform .25s ease;
    }
    .dso-brand{
        display:flex;align-items:center;gap:10px;
        padding:20px 18px;
        border-bottom:1px solid color-mix(in srgb, var(--color-nav-text) 12%, transparent);
        font-size:17px;font-weight:800;letter-spacing:0.01em;
    }
    .dso-brand .dso-brand-mark{
        width:34px;height:34px;border-radius:9px;
        background:var(--color-accent);
        display:flex;align-items:center;justify-content:center;
        font-weight:800;font-size:15px;color:#fff;
        box-shadow:var(--shadow-sm);
        flex-shrink:0;
    }
    .dso-brand a{text-decoration:none;color:var(--color-nav-text);display:flex;align-items:center;gap:10px;}

    .dso-nav-links{flex:1;overflow-y:auto;padding:12px 10px;display:flex;flex-direction:column;gap:2px;}
    .dso-nav-links a{
        display:flex;align-items:center;gap:12px;
        padding:10px 12px;border-radius:var(--radius-sm);
        text-decoration:none;font-size:13.5px;font-weight:500;
        color:var(--color-nav-text-muted);
        transition:background .15s ease,color .15s ease;
        white-space:nowrap;
    }
    .dso-nav-links a svg{flex-shrink:0;width:18px;height:18px;opacity:0.85;}
    .dso-nav-links a:hover{
        background:color-mix(in srgb, var(--color-nav-text) 8%, transparent);
        color:var(--color-nav-text);
        text-decoration:none;
    }
    .dso-nav-links a.active{
        background:var(--color-accent);
        color:#fff;
        box-shadow:var(--shadow-sm);
    }
    .dso-nav-links a.active svg{opacity:1;}

    /* ---------- Sidebar submenus ---------- */
    .dso-nav-group{display:flex;flex-direction:column;}
    .dso-nav-group-toggle{
        display:flex;align-items:center;gap:12px;width:100%;
        background:none;border:none;cursor:pointer;text-align:left;
        padding:10px 12px;border-radius:var(--radius-sm);
        font-size:13.5px;font-weight:500;color:var(--color-nav-text-muted);
        transition:background .15s ease,color .15s ease;
    }
    .dso-nav-group-toggle svg.dso-nav-icon{flex-shrink:0;width:18px;height:18px;opacity:0.85;}
    .dso-nav-group-toggle span.dso-nav-label{flex:1;}
    .dso-nav-group-toggle svg.dso-nav-chevron{width:14px;height:14px;flex-shrink:0;transition:transform .15s ease;opacity:0.7;}
    .dso-nav-group-toggle:hover{background:color-mix(in srgb, var(--color-nav-text) 8%, transparent);color:var(--color-nav-text);}
    .dso-nav-group.dso-open > .dso-nav-group-toggle svg.dso-nav-chevron{transform:rotate(90deg);}
    .dso-nav-group.dso-has-active > .dso-nav-group-toggle{color:var(--color-nav-text);}
    .dso-submenu{
        display:grid;grid-template-rows:0fr;
        list-style:none;margin:0;padding:0;
        overflow:hidden;transition:grid-template-rows .2s ease;
    }
    .dso-submenu > *{overflow:hidden;padding-left:30px;}
    .dso-nav-group.dso-open > .dso-submenu{grid-template-rows:1fr;}
    .dso-submenu li a{
        display:block;padding:8px 10px;border-radius:var(--radius-sm);
        font-size:12.5px;color:var(--color-nav-text-muted);text-decoration:none;
        transition:background .15s ease,color .15s ease;
    }
    .dso-submenu li a:hover{background:color-mix(in srgb, var(--color-nav-text) 8%, transparent);color:var(--color-nav-text);text-decoration:none;}
    .dso-submenu li a.active{color:var(--color-accent-2);font-weight:700;}

    /* De-emphasized link to the separate legacy CMS - deliberately lower visual
       weight than the primary Dyafa Sales OS menu items above, so it doesn't
       read as a first-class part of this app (see implementation.md). */
    .dso-nav-legacy-divider{
        margin:10px 12px 2px;padding-top:10px;
        border-top:1px dashed color-mix(in srgb, var(--color-nav-text) 18%, transparent);
        font-size:10px;text-transform:uppercase;letter-spacing:0.06em;
        color:color-mix(in srgb, var(--color-nav-text) 40%, transparent);
    }
    .dso-nav-legacy-link{opacity:0.62;}
    .dso-nav-legacy-link:hover{opacity:0.9;}
    .dso-nav-legacy-external{width:13px!important;height:13px!important;opacity:0.7;margin-left:auto;}

    .dso-nav-footer{
        padding:14px 16px;border-top:1px solid color-mix(in srgb, var(--color-nav-text) 12%, transparent);
        font-size:11px;color:var(--color-nav-text-muted);
    }

    /* ---------- Topbar ---------- */
    .dso-main{flex:1;margin-left:var(--sidebar-width);min-width:0;display:flex;flex-direction:column;}
    .dso-topbar{
        position:sticky;top:0;z-index:90;
        height:var(--topbar-height);
        background:var(--color-card-bg);
        border-bottom:1px solid var(--color-border-soft);
        display:flex;align-items:center;justify-content:space-between;
        padding:0 24px;
        box-shadow:var(--shadow-sm);
    }
    .dso-topbar-toggle{
        display:none;background:none;border:none;cursor:pointer;
        width:36px;height:36px;border-radius:var(--radius-sm);
        align-items:center;justify-content:center;color:var(--color-text);
    }
    .dso-topbar-toggle:hover{background:var(--color-border-soft);}
    .dso-user{display:flex;align-items:center;gap:12px;font-size:13.5px;}
    .dso-user-avatar{
        width:34px;height:34px;border-radius:50%;
        background:var(--color-accent-2);color:var(--color-nav-bg);
        display:flex;align-items:center;justify-content:center;
        font-weight:700;font-size:13px;flex-shrink:0;
    }
    .dso-user-meta{line-height:1.3;}
    .dso-user-meta b{display:block;font-size:13.5px;color:var(--color-text);}
    .dso-user-meta span{font-size:11.5px;color:var(--color-muted);text-transform:capitalize;}
    .dso-logout{
        margin-left:6px;color:var(--color-muted);text-decoration:none;font-size:12.5px;
        padding:6px 10px;border-radius:var(--radius-sm);border:1px solid var(--color-border);
        transition:all .15s ease;
    }
    .dso-logout:hover{color:var(--color-danger);border-color:var(--color-danger);text-decoration:none;}

    /* ---------- Content wrap ---------- */
    .dso-wrap{padding:28px 32px 60px;max-width:1280px;width:100%;margin:0 auto;}

    /* ---------- Cards ---------- */
    .dso-card{
        background:var(--color-card-bg);
        border:1px solid var(--color-border-soft);
        border-radius:var(--radius-md);
        padding:20px 22px;
        margin-bottom:20px;
        box-shadow:var(--shadow-sm);
        transition:box-shadow .2s ease;
    }
    .dso-card:hover{box-shadow:var(--shadow-md);}

    /* ---------- Stat tiles ---------- */
    .dso-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;}
    .dso-stat{
        background:var(--color-card-bg);border:1px solid var(--color-border-soft);
        border-radius:var(--radius-md);padding:18px 20px;box-shadow:var(--shadow-sm);
        position:relative;overflow:hidden;transition:transform .18s ease,box-shadow .18s ease;
    }
    .dso-stat:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
    .dso-stat::before{
        content:"";position:absolute;top:0;left:0;width:4px;height:100%;
        background:var(--stat-accent, var(--color-accent));
    }
    .dso-stat-label{font-size:12px;color:var(--color-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;}
    .dso-stat-value{font-size:26px;font-weight:800;color:var(--color-text);line-height:1.1;}
    .dso-stat-icon{
        position:absolute;top:16px;right:16px;width:38px;height:38px;border-radius:10px;
        background:var(--color-accent-soft);color:var(--color-accent);
        display:flex;align-items:center;justify-content:center;
    }
    .dso-stat-icon svg{width:19px;height:19px;}

    /* ---------- Tables ---------- */
    table.dso-table{border-collapse:separate;border-spacing:0;width:100%;background:var(--color-card-bg);font-size:13.5px;border-radius:var(--radius-sm);overflow:hidden;}
    table.dso-table th,table.dso-table td{padding:11px 14px;text-align:left;border-bottom:1px solid var(--color-border-soft);}
    table.dso-table th{background:var(--color-table-header-bg);font-weight:700;font-size:11.5px;text-transform:uppercase;letter-spacing:0.03em;color:var(--color-text);}
    table.dso-table tbody tr:hover{background:color-mix(in srgb, var(--color-accent) 5%, transparent);}
    table.dso-table tr:last-child td{border-bottom:none;}

    /* ---------- DataTables (search + pagination) brand overrides ---------- */
    .dso-table-wrap{margin-bottom:8px;}
    .dataTables_wrapper{font-size:13.5px;color:var(--color-text);}
    .dataTables_wrapper .dataTables_filter{margin-bottom:10px;}
    .dataTables_wrapper .dataTables_filter input{
        border:1px solid var(--color-border);border-radius:var(--radius-sm);
        padding:7px 10px;margin-left:8px;font-size:13.5px;
    }
    .dataTables_wrapper .dataTables_length select{
        border:1px solid var(--color-border);border-radius:var(--radius-sm);padding:4px 8px;
    }
    .dataTables_wrapper .dataTables_info{color:var(--color-muted);padding-top:10px;}
    .dataTables_wrapper .dataTables_paginate{padding-top:10px;}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        border-radius:var(--radius-sm);padding:5px 11px;margin-left:4px;
        border:1px solid var(--color-border)!important;background:var(--color-card-bg)!important;color:var(--color-text)!important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
        background:var(--color-accent)!important;border-color:var(--color-accent)!important;color:#fff!important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:.45;}
    table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:after{opacity:.5;}

    /* ---------- Dashboard charts (Daily/HOD) ---------- */
    .dso-charts-row{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;}
    .dso-chart-card{margin-bottom:0;}
    @media (max-width: 900px){.dso-charts-row{grid-template-columns:1fr;}}

    /* ---------- Server-side pagination (Leads/Reservations/Notifications) ---------- */
    .dso-pagination{display:flex;flex-wrap:wrap;gap:4px;margin:18px 0;}
    .dso-pagination a,.dso-pagination span.dso-page-num{
        display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;
        border-radius:var(--radius-sm);border:1px solid var(--color-border);
        font-size:13px;font-weight:600;color:var(--color-text);text-decoration:none;
        transition:all .15s ease;
    }
    .dso-pagination a:hover{background:var(--color-accent-soft);border-color:var(--color-accent);color:var(--color-accent-hover);text-decoration:none;}
    .dso-pagination span.dso-page-current{background:var(--color-accent);border-color:var(--color-accent);color:#fff;}
    .dso-pagination span.dso-page-nav{color:var(--color-muted);}

    /* ---------- Buttons ---------- */
    .dso-btn{
        display:inline-flex;align-items:center;gap:6px;
        background:var(--color-accent);color:#fff;
        padding:9px 16px;border-radius:var(--radius-sm);
        text-decoration:none;font-size:13.5px;font-weight:600;
        border:none;cursor:pointer;box-shadow:var(--shadow-sm);
        transition:background .15s ease,transform .15s ease,box-shadow .15s ease;
    }
    .dso-btn:hover{background:var(--color-accent-hover);transform:translateY(-1px);box-shadow:var(--shadow-md);color:#fff;text-decoration:none;}
    .dso-btn:active{transform:translateY(0);}
    .dso-btn.danger{background:var(--color-danger);}
    .dso-btn.secondary{background:var(--color-muted);}
    .dso-btn.outline{background:transparent;color:var(--color-accent);border:1.5px solid var(--color-accent);box-shadow:none;}
    .dso-btn.outline:hover{background:var(--color-accent-soft);color:var(--color-accent-hover);}

    /* ---------- Forms ---------- */
    .dso-form label{display:block;margin:14px 0 6px;font-weight:600;font-size:13px;color:var(--color-text);}
    .dso-form input,.dso-form select,.dso-form textarea{
        width:100%;padding:9px 12px;box-sizing:border-box;
        border:1.5px solid var(--color-border);border-radius:var(--radius-sm);
        background:var(--color-card-bg);color:var(--color-text);font-size:13.5px;
        transition:border-color .15s ease,box-shadow .15s ease;
    }
    .dso-form input:focus,.dso-form select:focus,.dso-form textarea:focus{
        outline:none;border-color:var(--color-accent);
        box-shadow:0 0 0 3px var(--color-accent-soft);
    }

    /* ---------- Alerts ---------- */
    .dso-alert{padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13.5px;border-left:4px solid transparent;}
    .dso-alert.error{background:var(--color-danger-bg);color:var(--color-danger);border-left-color:var(--color-danger);}
    .dso-alert.success{background:var(--color-success-bg);color:var(--color-nav-bg);border-left-color:var(--color-muted);}

    /* ---------- Scope tabs (list-page top nav, e.g. All / Active / Pending) ---------- */
    .dso-tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:1px solid var(--color-border-soft);flex-wrap:wrap;}
    .dso-tabs a{
        padding:10px 16px;font-size:13.5px;font-weight:600;color:var(--color-muted);
        text-decoration:none;border-bottom:2.5px solid transparent;margin-bottom:-1px;
        transition:color .15s ease,border-color .15s ease;
    }
    .dso-tabs a:hover{color:var(--color-text);text-decoration:none;}
    .dso-tabs a.active{color:var(--color-accent);border-bottom-color:var(--color-accent);}

    /* ---------- Server-side filter bar (list pages) ---------- */
    .dso-filters{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:18px;}
    .dso-filters input,.dso-filters select{
        padding:8px 12px;border:1.5px solid var(--color-border);border-radius:var(--radius-sm);
        background:var(--color-card-bg);color:var(--color-text);font-size:13.5px;min-width:160px;
    }
    .dso-filters input:focus,.dso-filters select:focus{outline:none;border-color:var(--color-accent);box-shadow:0 0 0 3px var(--color-accent-soft);}
    .dso-filters .dso-btn{padding:8px 14px;}

    /* ---------- Badges ---------- */
    .dso-badge{
        display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;
        font-weight:700;letter-spacing:0.02em;color:#fff;background:var(--color-muted);
    }
    .dso-badge.accent{background:var(--color-accent);}
    .dso-badge.peach{background:var(--color-accent-2);color:var(--color-nav-bg);}
    .dso-badge.danger{background:var(--color-danger);}
    .dso-badge.warning{background:var(--color-warning);color:#fff;}

    /* ---------- Responsive ---------- */
    @media (max-width: 900px){
        .dso-nav{transform:translateX(-100%);}
        .dso-nav.dso-open{transform:translateX(0);}
        .dso-main{margin-left:0;}
        .dso-topbar-toggle{display:flex;}
        .dso-wrap{padding:20px 16px 48px;}
    }
</style>
</head>
<body>
<?php
$CI =& get_instance();
$CI->config->load('dso_roles');
$CI->config->load('dso_menu');
$dso_hod_roles = $CI->config->item('dso_hod_roles');
$dso_is_hod = $dso_hod_roles && in_array($CI->session->userdata('dso_role'), $dso_hod_roles, true);
$dso_logged_in = $this->session->userdata('dso_user_id');
$dso_role = $this->session->userdata('dso_role');
$dso_active_segment = $this->uri->segment(2);
$dso_active_method = $this->uri->segment(3);
$dso_menu = $CI->config->item('dso_menu');

/** True if $roles is null (open to every staff role) or contains the current session role. */
function dso_menu_role_ok($roles, $current_role) {
    return $roles === null || in_array($current_role, $roles, true);
}

/** Renders one child <li><a>, marking it active when the URI matches its route. */
function dso_nav_child($child, $active_segment, $active_method) {
    $route_parts = explode('/', $child['route'], 2);
    $child_segment = $route_parts[0];
    $child_method = isset($route_parts[1]) ? $route_parts[1] : '';
    $is_active = ($child_segment === $active_segment)
        && ($child_method === '' || $child_method === (string) $active_method
            || ($child_method === 'index' && $active_method === null));
    echo '<li><a href="' . base_url('dyafa/' . $child['route']) . '"' . ($is_active ? ' class="active"' : '') . '>'
        . htmlspecialchars($child['label']) . '</a></li>';
    return $is_active;
}

/**
 * Renders the full role-filtered sidebar from application/config/dso_menu.php.
 * Single-route nodes render as a plain link; nodes with children render as a
 * collapsible group, auto-expanded (dso-open/dso-has-active) when one of
 * their children matches the current URL.
 */
function dso_render_menu($menu, $icons, $active_segment, $active_method, $current_role) {
    foreach ($menu as $node) {
        if (!dso_menu_role_ok($node['roles'], $current_role)) {
            continue;
        }
        $icon_svg = isset($icons[$node['icon']]) ? $icons[$node['icon']] : '';

        if (empty($node['children'])) {
            $is_active = ($node['segment'] === $active_segment);
            echo '<a href="' . base_url('dyafa/' . $node['route']) . '"' . ($is_active ? ' class="active"' : '') . '>'
                . $icon_svg . '<span>' . htmlspecialchars($node['label']) . '</span></a>';
            continue;
        }

        $visible_children = array();
        foreach ($node['children'] as $child) {
            $child_roles = isset($child['roles']) ? $child['roles'] : $node['roles'];
            if (dso_menu_role_ok($child_roles, $current_role)) {
                $visible_children[] = $child;
            }
        }
        if (empty($visible_children)) {
            continue;
        }

        $has_active_child = ($node['segment'] === $active_segment);
        if (!$has_active_child) {
            foreach ($visible_children as $child) {
                if (explode('/', $child['route'], 2)[0] === $active_segment) {
                    $has_active_child = true;
                    break;
                }
            }
        }
        $group_classes = 'dso-nav-group' . ($has_active_child ? ' dso-open dso-has-active' : '');
        echo '<div class="' . $group_classes . '" data-menu-key="' . htmlspecialchars($node['key']) . '">';
        echo '<button type="button" class="dso-nav-group-toggle">'
            . str_replace('<svg ', '<svg class="dso-nav-icon" ', $icon_svg)
            . '<span class="dso-nav-label">' . htmlspecialchars($node['label']) . '</span>'
            . '<svg class="dso-nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>'
            . '</button>';
        echo '<ul class="dso-submenu">';
        foreach ($visible_children as $child) {
            dso_nav_child($child, $active_segment, $active_method);
        }
        echo '</ul></div>';
    }
}

$dso_icons = array(
    'dashboard'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
    'leads'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.6 3.1-6.5 7-6.5s7 2.9 7 6.5"/></svg>',
    'accounts'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="13" rx="1.5"/><path d="M8 7V5.5A2.5 2.5 0 0 1 10.5 3h3A2.5 2.5 0 0 1 16 5.5V7"/></svg>',
    'contracts'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4"/><path d="M9.5 13h5M9.5 16.5h5"/></svg>',
    'reservations' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="1.5"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>',
    'adhoc'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5"/></svg>',
    'properties'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 11l8-6 8 6"/><path d="M6 10v9h12v-9"/><path d="M10 19v-5h4v5"/></svg>',
    'collections'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5v9M9.5 9.7c0-1.2 1.1-2.2 2.5-2.2s2.5 1 2.5 2c0 2.4-5 1.6-5 4 0 1 1.1 2 2.5 2s2.5-1 2.5-2.2"/></svg>',
    'targets'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="0.8" fill="currentColor"/></svg>',
    'aiassistant'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="7" width="16" height="12" rx="2.5"/><circle cx="9" cy="13" r="1.2" fill="currentColor" stroke="none"/><circle cx="15" cy="13" r="1.2" fill="currentColor" stroke="none"/><path d="M12 7V4M9 4h6"/></svg>',
    'aiconfig'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 13.5a7.6 7.6 0 0 0 0-3l2-1.4-2-3.4-2.3.8a7.5 7.5 0 0 0-2.6-1.5L14 2.5h-4l-.5 2.5a7.5 7.5 0 0 0-2.6 1.5l-2.3-.8-2 3.4 2 1.4a7.6 7.6 0 0 0 0 3l-2 1.4 2 3.4 2.3-.8a7.5 7.5 0 0 0 2.6 1.5l.5 2.5h4l.5-2.5a7.5 7.5 0 0 0 2.6-1.5l2.3.8 2-3.4z"/></svg>',
    'reports'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>',
    'notifications'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>',
    'activities'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h4l2-8 4 16 2-8h6"/></svg>',
    'admin'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 4.5a3.2 3.2 0 0 1 0 6.4M18.5 14a5.5 5.5 0 0 1 3.5 5.5"/></svg>',
);

?>
<div class="dso-shell">
    <nav class="dso-nav" id="dsoNav">
        <div class="dso-brand">
            <a href="<?php echo base_url('dyafa/dashboard'); ?>">
                <span class="dso-brand-mark">DS</span>
                <span>Dyafa Sales OS</span>
            </a>
        </div>
        <?php if ($dso_logged_in): ?>
        <div class="dso-nav-links">
            <?php dso_render_menu($dso_menu, $dso_icons, $dso_active_segment, $dso_active_method, $dso_role); ?>
            <?php if ($dso_is_hod): ?>
            <div class="dso-nav-legacy-divider">Separate legacy system</div>
            <a class="dso-nav-legacy-link" href="<?php echo base_url('admin'); ?>" target="_blank" title="Legacy CMS admin panel - a separate, older application with its own login and no shared RBAC/session with Dyafa Sales OS.">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2l8 4v6c0 5-3.4 8.4-8 10-4.6-1.6-8-5-8-10V6z"/><path d="M9.5 12l2 2 3.5-4"/></svg>
                <span>Legacy CMS Admin</span>
                <svg class="dso-nav-legacy-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M9 7h8v8"/></svg>
            </a>
            <?php endif; ?>
        </div>
        <div class="dso-nav-footer">&copy; <?php echo date('Y'); ?> Dyafa Sales OS</div>
        <?php endif; ?>
    </nav>

    <div class="dso-main">
        <div class="dso-topbar">
            <button type="button" class="dso-topbar-toggle" onclick="document.getElementById('dsoNav').classList.toggle('dso-open')" aria-label="Toggle navigation">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
            <div></div>
            <?php if ($dso_logged_in): ?>
            <div class="dso-user">
                <div class="dso-user-avatar"><?php echo strtoupper(substr($this->session->userdata('dso_name'), 0, 1)); ?></div>
                <div class="dso-user-meta">
                    <b><?php echo htmlspecialchars($this->session->userdata('dso_name')); ?></b>
                    <span><?php echo htmlspecialchars($this->session->userdata('dso_role')); ?></span>
                </div>
                <a class="dso-logout" href="<?php echo base_url('dyafa/logout'); ?>">Logout</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="dso-wrap">

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dyafa Sales OS</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%232a273c'/%3E%3Crect x='4' y='4' width='56' height='56' rx='11' fill='%23e95a54'/%3E%3Ctext x='32' y='43' font-family='Segoe UI,Arial,sans-serif' font-size='26' font-weight='800' fill='%23ffffff' text-anchor='middle'%3EDS%3C/text%3E%3C/svg%3E">
<style>
    /*
     * Minimal standalone layout for guest-facing screens (login, 2FA
     * enrollment/verification) - deliberately does NOT load the full
     * authenticated-app shell (sidebar/topbar), since $dso_logged_in is
     * always false here and that chrome was previously rendered (mostly
     * empty) for no reason. Same 5-hex brand palette as the main app shell
     * (application/views/dyafa/layout/header.php) for visual consistency.
     */
    :root{
        --color-bg: #f2f0eb;
        --color-nav-bg: #2a273c;
        --color-nav-bg-2: color-mix(in srgb, #2a273c 88%, black);
        --color-text: #2a273c;
        --color-accent: #e95a54;
        --color-accent-hover: color-mix(in srgb, #e95a54 85%, black);
        --color-accent-soft: color-mix(in srgb, #e95a54 14%, white);
        --color-accent-2: #fbcdab;
        --color-muted: #8f9793;
        --color-muted-soft: color-mix(in srgb, #8f9793 16%, white);
        --color-border: color-mix(in srgb, #8f9793 35%, white);
        --color-border-soft: color-mix(in srgb, #8f9793 18%, white);
        --color-danger: #e95a54;
        --color-danger-bg: color-mix(in srgb, #e95a54 12%, white);
        --color-card-bg: #ffffff;
        --shadow-lg: 0 12px 32px color-mix(in srgb, #2a273c 16%, transparent);
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 20px;
    }
    *{box-sizing:border-box;}
    html,body{height:100%;}
    body{
        margin:0;
        font-family:"Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,Helvetica,sans-serif;
        color:var(--color-text);
        -webkit-font-smoothing:antialiased;
        background:
            radial-gradient(circle at 15% 15%, color-mix(in srgb, var(--color-accent-2) 35%, transparent) 0%, transparent 45%),
            radial-gradient(circle at 85% 85%, color-mix(in srgb, var(--color-accent) 22%, transparent) 0%, transparent 50%),
            linear-gradient(160deg, var(--color-nav-bg) 0%, var(--color-nav-bg-2) 100%);
        min-height:100%;
        display:flex;align-items:center;justify-content:center;
        padding:32px 16px;
    }
    a{color:var(--color-accent);}
    h1,h2{margin:0 0 6px;color:var(--color-text);letter-spacing:-0.01em;}
    .dso-guest-shell{width:100%;max-width:420px;}
    .dso-guest-brand{
        display:flex;align-items:center;justify-content:center;gap:12px;
        margin-bottom:26px;color:#fff;
    }
    .dso-guest-brand .dso-brand-mark{
        width:44px;height:44px;border-radius:12px;
        background:var(--color-accent);
        display:flex;align-items:center;justify-content:center;
        font-weight:800;font-size:18px;color:#fff;
        box-shadow:var(--shadow-lg);flex-shrink:0;
    }
    .dso-guest-brand-name{font-size:19px;font-weight:800;letter-spacing:0.01em;}
    .dso-guest-brand-tagline{font-size:12px;opacity:0.75;font-weight:500;}

    .dso-card{
        background:var(--color-card-bg);
        border:1px solid var(--color-border-soft);
        border-radius:var(--radius-lg);
        padding:30px 32px;
        box-shadow:var(--shadow-lg);
        animation:dsoGuestCardIn .35s ease both;
    }
    @keyframes dsoGuestCardIn{
        from{opacity:0;transform:translateY(10px);}
        to{opacity:1;transform:translateY(0);}
    }
    h2{font-size:20px;font-weight:800;}
    .dso-guest-subtitle{font-size:12.5px;color:var(--color-muted);margin-bottom:18px;}

    .dso-form label{display:block;margin:14px 0 6px;font-weight:600;font-size:13px;color:var(--color-text);}
    .dso-form input,.dso-form select{
        width:100%;padding:10px 12px;box-sizing:border-box;
        border:1.5px solid var(--color-border);border-radius:var(--radius-sm);
        background:var(--color-card-bg);color:var(--color-text);font-size:14px;
        transition:border-color .15s ease,box-shadow .15s ease;
    }
    .dso-form input:focus,.dso-form select:focus{
        outline:none;border-color:var(--color-accent);
        box-shadow:0 0 0 3px var(--color-accent-soft);
    }
    .dso-btn{
        display:inline-flex;align-items:center;justify-content:center;gap:6px;width:100%;
        background:var(--color-accent);color:#fff;
        padding:11px 16px;border-radius:var(--radius-sm);
        text-decoration:none;font-size:14px;font-weight:700;
        border:none;cursor:pointer;
        transition:background .15s ease,transform .15s ease;
    }
    .dso-btn:hover{background:var(--color-accent-hover);transform:translateY(-1px);}
    .dso-btn:active{transform:translateY(0);}
    .dso-alert{padding:11px 14px;border-radius:var(--radius-sm);margin-bottom:14px;font-size:13px;border-left:4px solid transparent;}
    .dso-alert.error{background:var(--color-danger-bg);color:var(--color-danger);border-left-color:var(--color-danger);}
</style>
</head>
<body>
<div class="dso-guest-shell">
    <div class="dso-guest-brand">
        <span class="dso-brand-mark">DS</span>
        <span>
            <span class="dso-guest-brand-name" style="display:block;">Dyafa Sales OS</span>
            <span class="dso-guest-brand-tagline">Hospitality Sales Management Platform</span>
        </span>
    </div>

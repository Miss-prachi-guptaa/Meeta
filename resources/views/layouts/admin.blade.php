<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MEETA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-body: #f7f1e8;
            --bg-sidebar: #fffbf5;
            --bg-card: #fffcf7;
            --bg-card-hover: #fbf3e7;
            --bg-input: #fffcf7;

            --border-color: #e8dcc9;
            --border-color-strong: #dcc9ab;

            --text-primary: #3c2e21;
            --text-secondary: #7a6853;
            --text-muted: #a5947c;

            --terracotta: #c1622d;
            --terracotta-dark: #a8511f;
            --terracotta-soft: rgba(193, 98, 45, 0.10);

            --gold: #b8862b;
            --gold-soft: rgba(184, 134, 43, 0.12);

            --success: #4c7a52;
            --success-soft: rgba(76, 122, 82, 0.12);

            --danger: #b8483c;
            --danger-soft: rgba(184, 72, 60, 0.10);

            --neutral: #8a7a64;
            --neutral-soft: rgba(138, 122, 100, 0.12);

            --radius: 14px;
            --sidebar-width: 264px;
            --shadow-soft: 0 2px 10px rgba(60, 46, 33, 0.06);
            --shadow-card: 0 4px 18px rgba(60, 46, 33, 0.07);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
        }

        h1, h2, h3, .brand-font { font-family: 'Fraunces', serif; }

        a { text-decoration: none; color: inherit; }

        .admin-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            padding: 30px 18px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.25s ease;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 0 10px 24px;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand .logo {
            font-family: 'Fraunces', serif;
            font-size: 25px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.3px;
        }

        .sidebar-brand .logo span { color: var(--terracotta); }

        .sidebar-brand .subtitle {
            font-size: 11px;
            letter-spacing: 1.6px;
            color: var(--text-muted);
            margin-top: 3px;
            text-transform: uppercase;
        }

        .nav-section { display: flex; flex-direction: column; gap: 3px; margin-top: 16px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-size: 14.5px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
        .nav-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }

        .nav-item.active {
            background: var(--terracotta-soft);
            color: var(--terracotta-dark);
            font-weight: 600;
        }

        .nav-item.logout { color: var(--danger); margin-top: 16px; }
        .nav-item.logout:hover { background: var(--danger-soft); }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 36px 42px 60px;
            min-width: 0;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 17px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: opacity 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }

        .btn:hover { opacity: 0.9; }
        .btn-terracotta { background: var(--terracotta); color: #fff; }
        .btn-outline { background: #fff; border-color: var(--border-color-strong); color: var(--text-primary); }
        .btn-outline:hover { background: var(--bg-card-hover); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-sm { padding: 6px 13px; font-size: 12.5px; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        input, select, textarea {
            font-family: inherit;
            background: var(--bg-input);
            border: 1px solid var(--border-color-strong);
            color: var(--text-primary);
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13.5px;
            outline: none;
        }

        input::placeholder { color: var(--text-muted); }
        input:focus, select:focus { border-color: var(--terracotta); box-shadow: 0 0 0 3px var(--terracotta-soft); }

        .sidebar-toggle {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border-color-strong);
            color: var(--text-primary);
            border-radius: 9px;
            padding: 8px 12px;
            margin-bottom: 18px;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); box-shadow: var(--shadow-card); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 22px 18px 50px; }
            .sidebar-toggle { display: inline-flex; }
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: var(--border-color-strong); border-radius: 8px; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <div class="logo">MEE<span>TA</span></div>
                <div class="subtitle">Admin Panel</div>
            </div>

            <nav class="nav-section">
                <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                    Dashboard
                </a>
                <a href="/admin/vendors" class="nav-item {{ request()->is('admin/vendors*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Vendors
                </a>
                <a href="/admin/products/pending" class="nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3.24H4a1 1 0 0 0-1 1v5.59a2 2 0 0 0 .59 1.41l9.58 9.59a2 2 0 0 0 2.83 0l5.59-5.59a2 2 0 0 0 0-2.83Z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                    Products
                </a>
                <a href="/admin/categories" class="nav-item {{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    Categories
                </a>
                <a href="/admin/brands" class="nav-item {{ request()->is('admin/brands*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8.5 22 9.5 17 14.5 18.5 21.5 12 18 5.5 21.5 7 14.5 2 9.5 9 8.5"/></svg>
                    Brands
                </a>
                <a href="/admin/attributes" class="nav-item {{ request()->is('admin/attributes*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 4-8"/></svg>
                    Attributes
                </a>
                <a href="/admin/commissions" class="nav-item {{ request()->is('admin/commissions*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 6v12"/></svg>
                    Commissions
                </a>
                <a href="/admin/disputes" class="nav-item {{ request()->is('admin/disputes*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Disputes
                </a>
                <a href="/admin/payouts" class="nav-item {{ request()->is('admin/payouts*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Payouts
                </a>
                <a href="#" onclick="event.preventDefault(); localStorage.removeItem('meeta_token'); localStorage.removeItem('meeta_user'); localStorage.removeItem('meeta_vendor'); window.location.href='/admin/login';" class="nav-item logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>



            </nav>
        </aside>

        <main class="main-content">
            <button class="sidebar-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')">☰ Menu</button>
            @yield('content')
        </main>
    </div>

    <script>
        if (!localStorage.getItem('meeta_token')) {
            window.location.href = "/admin/login";
        }
    </script>

    @stack('scripts')
</body>
</html>

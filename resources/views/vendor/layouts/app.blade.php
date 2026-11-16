<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Vendor Dashboard') · MEETA</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  @csrf
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root {
      --bg-page: #faf3ea;
      --bg-card: #fffdf9;
      --bg-card-hover: #f6ece0;
      --border-color: #e8dcc9;
      --border-color-strong: #d9c7a8;
      --text-primary: #3c2e21;
      --text-secondary: #6b5a45;
      --text-muted: #9c8b73;
      --terracotta: #c1592c;
      --terracotta-dark: #a5451f;
      --terracotta-soft: #f3ddce;
      --success: #3f7d4f;
      --success-soft: #dcefdf;
      --danger: #b23a3a;
      --danger-soft: #fadada;
      --neutral: #8a7a63;
      --neutral-soft: #ece4d6;
      --radius: 16px;
      --shadow-soft: 0 4px 18px rgba(60, 46, 33, 0.08);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-page);
      color: var(--text-primary);
      font-size: 14px;
      line-height: 1.5;
    }

    h1,
    h2,
    h3 {
      font-family: 'Fraunces', serif;
      font-weight: 700;
      color: var(--text-primary);
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .app-shell {
      display: flex;
      min-height: 100vh;
    }

    /* ---------- Sidebar ---------- */
    .vendor-sidebar {
      width: 250px;
      flex-shrink: 0;
      background: var(--bg-card);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      z-index: 100;
      transition: transform 0.25s ease;
    }

    .sidebar-brand {
      padding: 26px 24px 20px;
      border-bottom: 1px solid var(--border-color);
    }

    .sidebar-brand .logo {
      font-family: 'Fraunces', serif;
      font-size: 22px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .sidebar-brand .logo span {
      color: var(--terracotta);
    }

    .sidebar-brand .sub {
      font-size: 11px;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .sidebar-nav {
      flex: 1;
      padding: 18px 14px;
      overflow-y: auto;
    }

    .sidebar-nav a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-secondary);
      margin-bottom: 4px;
      transition: background 0.15s, color 0.15s;
    }

    .sidebar-nav a svg {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
    }

    .sidebar-nav a:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .sidebar-nav a.active {
      background: var(--terracotta-soft);
      color: var(--terracotta-dark);
      font-weight: 600;
    }

    .sidebar-nav a.disabled {
      opacity: 0.45;
      cursor: default;
      pointer-events: none;
    }

    .sidebar-footer {
      padding: 14px;
      border-top: 1px solid var(--border-color);
    }

    .sidebar-footer button {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: var(--danger);
      background: none;
      border: none;
      cursor: pointer;
      font-family: inherit;
    }

    .sidebar-footer button:hover {
      background: var(--danger-soft);
    }

    .sidebar-footer svg {
      width: 18px;
      height: 18px;
    }

    /* ---------- Mobile drawer ---------- */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(60, 46, 33, 0.35);
      z-index: 90;
    }

    .mobile-topbar {
      display: none;
    }

    @media (max-width: 900px) {
      .vendor-sidebar {
        transform: translateX(-100%);
      }

      .vendor-sidebar.open {
        transform: translateX(0);
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
      }

      .sidebar-overlay.open {
        display: block;
      }

      .main-content {
        margin-left: 0 !important;
      }

      .mobile-topbar {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 50;
      }

      .mobile-topbar .logo {
        font-family: 'Fraunces', serif;
        font-weight: 700;
        font-size: 18px;
      }

      .mobile-topbar .logo span {
        color: var(--terracotta);
      }

      .mobile-topbar button {
        background: none;
        border: 1px solid var(--border-color-strong);
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }

      .mobile-topbar button svg {
        width: 18px;
        height: 18px;
        color: var(--text-primary);
      }
    }

    /* ---------- Main content ---------- */
    .main-content {
      margin-left: 250px;
      flex: 1;
      min-width: 0;
      padding: 34px 40px;
    }

    @media (max-width: 700px) {
      .main-content {
        padding: 22px 18px;
      }
    }

    /* ---------- Shared UI kit (reused by dashboard + future vendor pages) ---------- */
    .card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 11px 20px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      border: 1px solid transparent;
      cursor: pointer;
      font-family: inherit;
      transition: background 0.15s, opacity 0.15s;
    }

    .btn-terracotta {
      background: var(--terracotta);
      color: #fff;
    }

    .btn-terracotta:hover {
      background: var(--terracotta-dark);
    }

    .btn-outline {
      background: #fff;
      border-color: var(--border-color-strong);
      color: var(--text-primary);
    }

    .btn-outline:hover {
      background: var(--bg-card-hover);
    }

    .btn-sm {
      padding: 7px 14px;
      font-size: 13px;
    }

    .badge {
      display: inline-block;
      padding: 4px 11px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    select,
    input {
      border: 1px solid var(--border-color-strong);
      border-radius: 9px;
      background: #fff;
      color: var(--text-primary);
      font-family: inherit;
      font-size: 14px;
      padding: 10px 12px;
    }

    select:focus,
    input:focus {
      outline: none;
      border-color: var(--terracotta);
    }
  </style>

  @stack('styles')
</head>

<body>

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  <div class="app-shell">
    @include('vendor.components.sidebar')

    <div class="main-content">
      <div class="mobile-topbar">
        <button onclick="openSidebar()" aria-label="Open menu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
        </button>
        <div class="logo">MEE<span>TA</span></div>
      </div>

      @yield('content')
    </div>
  </div>

  <script>
    function openSidebar() {
      document.querySelector('.vendor-sidebar').classList.add('open');
      document.getElementById('sidebarOverlay').classList.add('open');
    }

    function closeSidebar() {
      document.querySelector('.vendor-sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.remove('open');
    }
  </script>

  @stack('scripts')
</body>

</html>
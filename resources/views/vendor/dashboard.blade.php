@extends('vendor.layouts.app')

@section('title', 'Vendor Dashboard')

@push('styles')
<style>
  .dash-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 26px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .dash-header h1 {
    font-size: 28px;
    margin-bottom: 5px;
    color: var(--text-primary);
  }

  .dash-header p {
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  .header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .wallet-pill {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    padding: 8px 16px;
    border-radius: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    box-shadow: var(--shadow-soft);
  }

  .wallet-pill svg {
    color: var(--terracotta);
    width: 18px;
    height: 18px;
  }

  .wallet-pill strong {
    font-size: 15px;
    color: var(--terracotta-dark);
    font-family: 'Fraunces', serif;
  }

  /* ---------- Stat cards (5 columns) ---------- */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 26px;
  }

  @media (max-width: 1200px) {
    .stats-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 480px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }
  }

  .stat-card {
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    box-shadow: var(--shadow-soft);
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--terracotta-soft);
    color: var(--terracotta-dark);
  }

  .stat-icon svg {
    width: 22px;
    height: 22px;
  }

  .stat-card .stat-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
    font-weight: 600;
  }

  .stat-card .stat-value {
    font-family: 'Fraunces', serif;
    font-size: 23px;
    font-weight: 700;
    color: var(--text-primary);
  }

  /* ---------- Section layout ---------- */
  .dash-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 20px;
    margin-bottom: 22px;
    align-items: start;
  }

  @media (max-width: 1000px) {
    .dash-grid {
      grid-template-columns: 1fr;
    }
  }

  .section-card {
    padding: 22px 24px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    box-shadow: var(--shadow-soft);
  }

  .section-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
    flex-wrap: wrap;
    gap: 10px;
  }

  .section-card-header h2 {
    font-size: 17px;
    font-weight: 700;
  }

  .range-toggle {
    display: flex;
    background: var(--bg-card-hover);
    border-radius: 9px;
    padding: 3px;
    gap: 2px;
  }

  .range-toggle button {
    border: none;
    background: none;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    font-family: inherit;
    transition: 0.15s;
  }

  .range-toggle button.active {
    background: var(--terracotta);
    color: #fff;
  }

  #salesChartWrap {
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }

  .chart-empty,
  .chart-loading {
    color: var(--text-muted);
    font-size: 13.5px;
    padding: 40px 0;
    text-align: center;
    width: 100%;
  }

  /* ---------- Status list ---------- */
  .status-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid var(--border-color);
  }

  .status-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .status-row .status-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 10px;
  }

  .status-row .status-name {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-primary);
    display: flex;
    align-items: center;
  }

  .status-row .status-count {
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Fraunces', serif;
  }

  .dot-draft { background: var(--neutral); }
  .dot-pending { background: #d8963a; }
  .dot-active { background: var(--success); }
  .dot-rejected { background: var(--danger); }

  /* ---------- Insights Grid (4 cards) ---------- */
  .insights-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
  }

  @media (max-width: 1200px) {
    .insights-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 640px) {
    .insights-grid {
      grid-template-columns: 1fr;
    }
  }

  .metric-line,
  .compact-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
  }

  .metric-line:last-child,
  .compact-row:last-child { border-bottom: 0; }
  .metric-line strong { font-size: 16px; color: var(--terracotta-dark); font-family: 'Fraunces', serif; }
  .compact-row span { color: var(--text-muted); }
  .compact-row b { font-size: 13px; text-align: right; }
  .alert-count { color: var(--danger) !important; font-weight: 700; }
  .section-link { color: var(--terracotta-dark); font-size: 12px; font-weight: 600; text-decoration: none; }
  .section-link:hover { text-decoration: underline; }
  .sales-summary { display:flex; align-items:baseline; gap:10px; margin:8px 0 2px; }
  .sales-summary strong { font-size:24px; font-family:'Fraunces', serif; }
  .sales-change { font-size:12px; color:var(--text-muted); }

  /* ---------- Recent orders table ---------- */
  .table-wrap {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
  }

  thead th {
    text-align: left;
    font-size: 11px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-color);
    background: #fdf8f0;
    white-space: nowrap;
  }

  tbody td {
    padding: 13px 14px;
    border-bottom: 1px solid var(--border-color);
    font-size: 13.5px;
    vertical-align: middle;
  }

  tbody tr:last-child td {
    border-bottom: none;
  }

  tbody tr:hover {
    background: #fffdf9;
  }

  .empty-state,
  .loading-state {
    padding: 35px 20px;
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
  }

  .badge.order-confirmed,
  .badge.order-pending {
    background: #fbead1;
    color: #9a6a15;
  }

  .badge.order-processing {
    background: #e0e9f5;
    color: #2c5aa0;
  }

  .badge.order-ready_to_ship {
    background: #f0e5f8;
    color: #835ca3;
  }

  .badge.order-shipped {
    background: #dff2f3;
    color: #176b77;
  }

  .badge.order-completed,
  .badge.order-delivered {
    background: var(--success-soft);
    color: var(--success);
  }

  .badge.order-cancelled {
    background: var(--danger-soft);
    color: var(--danger);
  }

  /* ---------- Quick actions (5 cards) ---------- */
  .quick-actions {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-top: 4px;
  }

  @media (max-width: 1200px) {
    .quick-actions {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .quick-actions {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 480px) {
    .quick-actions {
      grid-template-columns: 1fr;
    }
  }

  .quick-action-card {
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    text-decoration: none;
    color: inherit;
    box-shadow: var(--shadow-soft);
    transition: box-shadow 0.15s, transform 0.15s, border-color 0.15s;
  }

  .quick-action-card:hover {
    box-shadow: 0 8px 22px rgba(60, 46, 33, 0.12);
    transform: translateY(-2px);
    border-color: var(--terracotta);
  }

  .quick-action-card.disabled {
    opacity: 0.5;
    pointer-events: none;
  }

  .quick-action-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: var(--terracotta-soft);
    color: var(--terracotta-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .quick-action-icon svg {
    width: 20px;
    height: 20px;
  }

  .quick-action-card .qa-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
  }

  .quick-action-card .qa-sub {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
  }
</style>
@endpush

@section('content')

<div class="dash-header">
  <div>
    <h1>Welcome back, <span id="vendorStoreHeader">{{ $vendorDisplayName ?? 'Partner' }}</span></h1>
    <p>Complete control over your products, orders, returns, and payout settlements.</p>
  </div>
  <div class="header-actions">
    <div class="wallet-pill">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="5" width="20" height="14" rx="2" />
        <line x1="2" y1="10" x2="22" y2="10" />
      </svg>
      <span>Wallet Balance: <strong id="topWalletBalance">₹0.00</strong></span>
    </div>
    <a href="{{ route('vendor.orders.index') }}" class="btn btn-primary" style="background:var(--terracotta); color:#fff; padding:9px 16px; border-radius:9px; text-decoration:none; font-weight:600; font-size:13.5px; display:inline-flex; align-items:center; gap:6px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px;">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
        <path d="M3 6h18" />
        <path d="M16 10a4 4 0 0 1-8 0" />
      </svg>
      View Orders
    </a>
  </div>
</div>

<!-- Stat cards (5 metrics) -->
<div class="stats-grid" id="statsGrid">
  <div class="stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z" />
        <circle cx="7" cy="7" r="1.2" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Products</div>
      <div class="stat-value" id="statTotalProducts">—</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" />
        <polyline points="12 6 12 12 16 14" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Pending Approval</div>
      <div class="stat-value" id="statPendingProducts">—</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Active Listings</div>
      <div class="stat-value" id="statActiveProducts">—</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
        <path d="M3 6h18" />
        <path d="M16 10a4 4 0 0 1-8 0" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Orders</div>
      <div class="stat-value" id="statTotalOrders">—</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="5" width="20" height="14" rx="2" />
        <line x1="2" y1="10" x2="22" y2="10" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Settled Earnings</div>
      <div class="stat-value" id="statSettledEarnings">₹0</div>
    </div>
  </div>
</div>

<div class="dash-grid">
  <!-- Sales overview -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Sales Revenue Overview</h2>
      <div class="range-toggle" id="salesRangeToggle">
        <button class="active" data-range="week">This Week</button>
        <button data-range="month">This Month</button>
      </div>
    </div>
    <div id="salesChartWrap">
      <div class="chart-loading">Loading sales data...</div>
    </div>
  </div>

  <!-- Product status distribution -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Catalogue Health</h2>
      <a class="section-link" href="{{ route('vendor.products.index') }}">View catalogue →</a>
    </div>
    <div class="status-list" id="productStatusList">
      <div class="loading-state">Loading...</div>
    </div>
  </div>
</div>

<!-- Operational Insights Grid (4 cards) -->
<div class="insights-grid">
  <!-- Order Work Queue -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Work Queue</h2>
      <a class="section-link" href="{{ route('vendor.orders.index') }}">Orders →</a>
    </div>
    <div id="attentionList"><div class="loading-state">Loading...</div></div>
  </div>

  <!-- Finance & Settlement Overview -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Financial Summary</h2>
      <a class="section-link" href="{{ route('vendor.payouts.index') }}">Payouts →</a>
    </div>
    <div id="financeList"><div class="loading-state">Loading...</div></div>
  </div>

  <!-- Low Stock Alerts -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Low Stock Alerts</h2>
      <a class="section-link" href="{{ route('vendor.products.index') }}">Stock →</a>
    </div>
    <div id="lowStockList"><div class="loading-state">Loading...</div></div>
  </div>

  <!-- Top Performing Products -->
  <div class="section-card">
    <div class="section-card-header">
      <h2>Top Products</h2>
      <span style="font-size:12px; color:var(--text-muted);">Units Sold</span>
    </div>
    <div id="topProductsList"><div class="loading-state">Loading...</div></div>
  </div>
</div>

<!-- Recent orders -->
<div class="section-card" style="margin-bottom: 26px;">
  <div class="section-card-header">
    <h2>Recent Customer Orders</h2>
    <a class="section-link" href="{{ route('vendor.orders.index') }}">All orders →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Sub Order</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Payment</th>
          <th>Fulfillment Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="recentOrdersBody">
        <tr>
          <td colspan="7" class="loading-state">Loading recent orders...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Quick actions (5 actions) -->
<div>
  <h2 style="font-size: 17px; margin-bottom: 14px; font-weight:700;">Quick Actions</h2>
  <div class="quick-actions">
    <a href="{{ route('vendor.products.index') }}" class="quick-action-card">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Add / Manage Products</div>
        <div class="qa-sub">Create listings & update stock</div>
      </div>
    </a>

    <a href="{{ route('vendor.orders.index') }}" class="quick-action-card">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
          <path d="M3 6h18" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Process Orders</div>
        <div class="qa-sub">Accept orders within 24h SLA</div>
      </div>
    </a>

    <a href="{{ route('vendor.returns.index') }}" class="quick-action-card">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 14 4 9l5-5" />
          <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Return Claims</div>
        <div class="qa-sub">Inspect & accept or dispute</div>
      </div>
    </a>

    <a href="{{ route('vendor.payouts.index') }}" class="quick-action-card">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="2" y="5" width="20" height="14" rx="2" />
          <line x1="2" y1="10" x2="22" y2="10" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Payouts & Wallet</div>
        <div class="qa-sub">Ledger credits & bank payouts</div>
      </div>
    </a>

    <a href="{{ route('vendor.store.edit') }}" class="quick-action-card">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 21V9l9-6 9 6v12" />
          <path d="M9 21v-6h6v6" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Store Profile</div>
        <div class="qa-sub">Business & bank details</div>
      </div>
    </a>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const API_BASE = "{{ url('/api/vendor') }}";
  const ORDER_PAGE = "{{ url('/vendor/orders') }}";
  const token = localStorage.getItem('meeta_vendor_token') || localStorage.getItem('meeta_token');
  let currentRange = 'week';

  function authHeaders() {
    return {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
  }

  function formatCurrency(n) {
    if (n === null || n === undefined) return '—';
    return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 2 });
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-IN', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    });
  }

  async function loadDashboard(range = 'week') {
    currentRange = range;
    try {
      const res = await fetch(`${API_BASE}/dashboard?range=${range}`, {
        headers: authHeaders()
      });

      if (res.status === 401) {
        localStorage.removeItem('meeta_vendor_token');
        window.location.href = "{{ url('/vendor/login') }}";
        return;
      }

      if (res.status === 404) {
        showMissingApiState();
        return;
      }

      const json = await res.json();
      if (!json.success) {
        showErrorState(json.message || 'Failed to load dashboard data.');
        return;
      }

      const data = json.data;
      if (data.vendor && data.vendor.store_name) {
        document.getElementById('vendorStoreHeader').textContent = data.vendor.store_name;
      }

      renderStats(data);
      renderProductStatus(data.product_status_counts);
      renderSalesChart(data.sales);
      renderRecentOrders(data.recent_orders);
      renderOperationalInsights(data);
    } catch (e) {
      showErrorState('Something went wrong while loading the dashboard.');
      console.error(e);
    }
  }

  function renderStats(data) {
    document.getElementById('statTotalProducts').textContent = data.total_products ?? '—';
    document.getElementById('statPendingProducts').textContent = data.pending_products ?? '—';
    document.getElementById('statActiveProducts').textContent = data.active_products ?? '—';
    document.getElementById('statTotalOrders').textContent = data.total_orders ?? '—';

    const settlements = data.settlements || {};
    const settledAmount = settlements.settled_amount ?? 0;
    const walletBalance = settlements.current_wallet_balance ?? (data.summary ? data.summary.current_balance : 0);

    document.getElementById('statSettledEarnings').textContent = formatCurrency(settledAmount);
    document.getElementById('topWalletBalance').textContent = formatCurrency(walletBalance);
  }

  function renderProductStatus(counts) {
    const wrap = document.getElementById('productStatusList');
    if (!counts) {
      wrap.innerHTML = '<div class="empty-state">No product status data available.</div>';
      return;
    }
    const rows = [
      ['active', 'Active Listings'],
      ['pending', 'Under Review'],
      ['draft', 'Drafts'],
      ['rejected', 'Rejected'],
    ];
    wrap.innerHTML = rows.map(([key, label]) => `
      <div class="status-row">
        <span class="status-name"><span class="status-dot dot-${key}"></span>${label}</span>
        <span class="status-count">${counts[key] ?? 0}</span>
      </div>
    `).join('');
  }

  function renderSalesChart(sales) {
    const wrap = document.getElementById('salesChartWrap');
    if (!sales || !sales.values || !sales.values.length) {
      wrap.innerHTML = '<div class="chart-empty">No sales recorded yet for this period.</div>';
      return;
    }

    const labels = sales.labels || sales.values.map((_, i) => i + 1);
    const values = sales.values;
    const max = Math.max(...values, 1);

    const change = sales.change_percentage === null || sales.change_percentage === undefined ?
      'Period revenue' : `${sales.change_percentage >= 0 ? '+' : ''}${sales.change_percentage}% vs previous period`;
    
    wrap.innerHTML = `
      <div class="sales-summary">
        <strong>${formatCurrency(sales.total)}</strong>
        <span class="sales-change">${change}</span>
      </div>
      <div style="display:flex; align-items:flex-end; gap:8px; width:100%; height:190px; padding-top: 14px;">
        ${values.map((v, i) => {
          const heightPct = Math.max((v / max) * 100, 4);
          return `
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
              <div title="${labels[i]}: ${formatCurrency(v)}" style="width:100%; max-width:32px; height:${heightPct}%; background:var(--terracotta); border-radius:6px 6px 2px 2px; opacity:${0.55 + (v / max) * 0.45}; transition:0.2s;"></div>
              <div style="font-size:11px; color:var(--text-muted); white-space:nowrap;">${labels[i] ?? ''}</div>
            </div>
          `;
        }).join('')}
      </div>
    `;
  }

  function renderOperationalInsights(data) {
    const attention = data.attention || {};
    document.getElementById('attentionList').innerHTML = [
      ['New orders to accept', attention.new_orders || 0, (attention.new_orders > 0)],
      ['Orders to pack & ship', attention.orders_to_pack || 0, false],
      ['Pending return claims', attention.pending_returns || 0, (attention.pending_returns > 0)],
      ['Rejected products', attention.rejected_products || 0, (attention.rejected_products > 0)],
    ].map(([label, count, isAlert]) => `
      <div class="metric-line">
        <span>${label}</span>
        <strong class="${isAlert ? 'alert-count' : ''}">${count}</strong>
      </div>
    `).join('');

    const settlements = data.settlements || {};
    document.getElementById('financeList').innerHTML = [
      ['Current Wallet Balance', formatCurrency(settlements.current_wallet_balance ?? 0)],
      ['Pending Clearance', formatCurrency(settlements.pending_amount ?? 0)],
      ['Settled Lifetime', formatCurrency(settlements.settled_amount ?? 0)],
      ['Total Payouts Disbursed', formatCurrency(settlements.total_paid_out ?? 0)],
    ].map(([label, val]) => `
      <div class="metric-line">
        <span style="color:var(--text-secondary); font-size:12.5px;">${label}</span>
        <strong style="font-size:14px;">${val}</strong>
      </div>
    `).join('');

    const lowStock = data.low_stock || [];
    document.getElementById('lowStockList').innerHTML = lowStock.length ? lowStock.map(item => `
      <div class="compact-row">
        <span style="max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.sku || item.variant_name || 'Variant'}</span>
        <b style="color:${item.available_quantity <= 0 ? 'var(--danger)' : 'inherit'};">${item.available_quantity} left</b>
      </div>
    `).join('') : '<div class="empty-state" style="padding:20px 0;">All stock levels healthy.</div>';

    const products = data.top_products || [];
    document.getElementById('topProductsList').innerHTML = products.length ? products.map(item => `
      <div class="compact-row">
        <span style="max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.product_name}</span>
        <b>${item.units_sold} sold · ${formatCurrency(item.revenue)}</b>
      </div>
    `).join('') : '<div class="empty-state" style="padding:20px 0;">Sales will appear once items are ordered.</div>';
  }

  function renderRecentOrders(orders) {
    const tbody = document.getElementById('recentOrdersBody');
    if (!orders || !orders.length) {
      tbody.innerHTML = '<tr><td colspan="7" class="empty-state">No orders placed yet.</td></tr>';
      return;
    }

    tbody.innerHTML = orders.map(o => {
      const status = (o.status || '').toLowerCase();
      const label = status.replace('_', ' ');
      const payment = (o.payment_status || 'pending').toLowerCase();
      return `
        <tr>
          <td><strong style="color:var(--terracotta-dark);">${o.sub_order_number || o.order_number || ('#' + o.id)}</strong></td>
          <td>${o.customer_name || 'Customer'}</td>
          <td><strong>${formatCurrency(o.amount)}</strong></td>
          <td><span class="badge ${payment === 'paid' ? 'order-completed' : 'order-pending'}">${payment}</span></td>
          <td><span class="badge order-${status}">${label}</span></td>
          <td style="color:var(--text-muted); font-size:12px;">${formatDate(o.created_at)}</td>
          <td><a href="${ORDER_PAGE}/${o.id}" class="btn btn-outline btn-sm" style="padding:4px 10px; font-size:12px; border-radius:6px; text-decoration:none; display:inline-block;">Manage</a></td>
        </tr>
      `;
    }).join('');
  }

  function showMissingApiState() {
    document.getElementById('salesChartWrap').innerHTML =
      '<div class="chart-empty">Dashboard API not reachable.</div>';
    document.getElementById('productStatusList').innerHTML =
      '<div class="empty-state">Unable to load catalogue health.</div>';
    document.getElementById('recentOrdersBody').innerHTML =
      '<tr><td colspan="7" class="empty-state">Dashboard API not reachable.</td></tr>';
    ['statTotalProducts', 'statPendingProducts', 'statActiveProducts', 'statTotalOrders']
    .forEach(id => document.getElementById(id).textContent = '—');
  }

  function showErrorState(message) {
    document.getElementById('salesChartWrap').innerHTML = `<div class="chart-empty">${message}</div>`;
    document.getElementById('productStatusList').innerHTML = `<div class="empty-state">${message}</div>`;
    document.getElementById('recentOrdersBody').innerHTML = `<tr><td colspan="7" class="empty-state">${message}</td></tr>`;
    ['attentionList', 'financeList', 'lowStockList', 'topProductsList'].forEach(id => {
      document.getElementById(id).innerHTML = `<div class="empty-state">${message}</div>`;
    });
  }

  document.getElementById('salesRangeToggle').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-range]');
    if (!btn) return;
    document.querySelectorAll('#salesRangeToggle button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    loadDashboard(btn.dataset.range);
  });

  loadDashboard('week');
</script>
@endpush

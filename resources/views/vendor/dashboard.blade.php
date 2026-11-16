@extends('vendor.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
  .dash-header {
    margin-bottom: 26px;
  }

  .dash-header h1 {
    font-size: 27px;
    margin-bottom: 5px;
  }

  .dash-header p {
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* ---------- Stat cards ---------- */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 26px;
  }

  @media (max-width: 900px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 520px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }
  }

  .stat-card {
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .stat-icon {
    width: 46px;
    height: 46px;
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
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .stat-card .stat-value {
    font-family: 'Fraunces', serif;
    font-size: 25px;
    font-weight: 700;
    color: var(--text-primary);
  }

  /* ---------- Section layout ---------- */
  .dash-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
    align-items: start;
  }

  @media (max-width: 1000px) {
    .dash-grid {
      grid-template-columns: 1fr;
    }
  }

  .section-card {
    padding: 22px 24px;
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
  }

  .range-toggle {
    display: flex;
    background: var(--bg-card-hover);
    border-radius: 9px;
    padding: 4px;
    gap: 2px;
  }

  .range-toggle button {
    border: none;
    background: none;
    padding: 7px 14px;
    border-radius: 7px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    font-family: inherit;
  }

  .range-toggle button.active {
    background: var(--terracotta);
    color: #fff;
  }

  #salesChartWrap {
    min-height: 220px;
    display: flex;
    align-items: flex-end;
  }

  .chart-empty,
  .chart-loading {
    color: var(--text-muted);
    font-size: 13.5px;
    padding: 40px 0;
    text-align: center;
    width: 100%;
  }

  /* ---------- Product status ---------- */
  .status-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
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

  .dot-draft {
    background: var(--neutral);
  }

  .dot-pending {
    background: #d8963a;
  }

  .dot-active {
    background: var(--success);
  }

  .dot-rejected {
    background: var(--danger);
  }

  /* ---------- Recent orders table ---------- */
  .table-wrap {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 560px;
  }

  thead th {
    text-align: left;
    font-size: 11.5px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  tbody td {
    padding: 13px 14px;
    border-bottom: 1px solid var(--border-color);
    font-size: 13.5px;
  }

  tbody tr:last-child td {
    border-bottom: none;
  }

  .empty-state,
  .loading-state {
    padding: 40px 20px;
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
  }

  .badge.order-pending {
    background: #fbead1;
    color: #9a6a15;
  }

  .badge.order-processing {
    background: #e0e9f5;
    color: #2c5aa0;
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

  /* ---------- Quick actions ---------- */
  .quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 4px;
  }

  @media (max-width: 700px) {
    .quick-actions {
      grid-template-columns: 1fr;
    }
  }

  .quick-action-card {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: box-shadow 0.15s, transform 0.15s;
  }

  .quick-action-card:hover {
    box-shadow: 0 8px 22px rgba(60, 46, 33, 0.12);
    transform: translateY(-1px);
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

  .insights-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 26px;
    margin-bottom: 26px;
  }

  .metric-line,
  .compact-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 11px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
  }

  .metric-line:last-child,
  .compact-row:last-child { border-bottom: 0; }
  .metric-line strong { font-size: 17px; color: var(--terracotta-dark); }
  .compact-row span { color: var(--text-muted); }
  .compact-row b { font-size: 13px; text-align: right; }
  .alert-count { color: var(--danger); font-weight: 700; }
  .section-link { color: var(--terracotta-dark); font-size: 12px; font-weight: 600; }
  .sales-summary { display:flex; align-items:baseline; gap:10px; margin:8px 0 2px; }
  .sales-summary strong { font-size:24px; }
  .sales-change { font-size:12px; color:var(--text-muted); }

  @media (max-width: 1050px) { .insights-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="dash-header">
  <h1>Welcome back, {{ $vendorDisplayName ?? 'there' }}</h1>
  <p>Manage your store, products and orders.</p>
</div>

<!-- Stat cards -->
<div class="stats-grid" id="statsGrid">
  <div class="card stat-card">
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
  <div class="card stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" />
        <polyline points="12 6 12 12 16 14" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Pending Products</div>
      <div class="stat-value" id="statPendingProducts">—</div>
    </div>
  </div>
  <div class="card stat-card">
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
    </div>
    <div>
      <div class="stat-label">Active Products</div>
      <div class="stat-value" id="statActiveProducts">—</div>
    </div>
  </div>
  <div class="card stat-card">
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
</div>

<div class="dash-grid">
  <!-- Sales overview -->
  <div class="card section-card">
    <div class="section-card-header">
      <h2>Sales Overview</h2>
      <div class="range-toggle" id="salesRangeToggle">
        <button class="active" data-range="week">This Week</button>
        <button data-range="month">This Month</button>
      </div>
    </div>
    <div id="salesChartWrap">
      <div class="chart-loading">Loading sales data...</div>
    </div>
  </div>

  <!-- Product status -->
  <div class="card section-card">
    <div class="section-card-header">
      <h2>Product Status</h2>
    </div>
    <div class="status-list" id="productStatusList">
      <div class="loading-state">Loading...</div>
    </div>
  </div>
</div>

<div class="insights-grid">
  <div class="card section-card">
    <div class="section-card-header"><h2>Order Work Queue</h2><a class="section-link" href="{{ route('vendor.orders.index') }}">Manage orders →</a></div>
    <div id="attentionList"><div class="loading-state">Loading...</div></div>
  </div>
  <div class="card section-card">
    <div class="section-card-header"><h2>Low Stock</h2><a class="section-link" href="{{ route('vendor.products.index') }}">Manage stock →</a></div>
    <div id="lowStockList"><div class="loading-state">Loading...</div></div>
  </div>
  <div class="card section-card">
    <div class="section-card-header"><h2>Top Products</h2></div>
    <div id="topProductsList"><div class="loading-state">Loading...</div></div>
  </div>
</div>

<!-- Recent orders -->
<div class="card section-card" style="margin-bottom: 26px;">
  <div class="section-card-header">
    <h2>Recent Orders</h2>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody id="recentOrdersBody">
        <tr>
          <td colspan="5" class="loading-state">Loading recent orders...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Quick actions -->
<div>
  <h2 style="font-size: 17px; margin-bottom: 14px;">Quick Actions</h2>
  <div class="quick-actions">
    @php
    $addProductUrl = Route::has('vendor.products.create') ? route('vendor.products.create') : '#';
    $manageProductsUrl = Route::has('vendor.products.index') ? route('vendor.products.index') : '#';
    $viewOrdersUrl = Route::has('vendor.orders.index') ? route('vendor.orders.index') : '#';
    @endphp
    <a href="{{ $addProductUrl }}" class="card quick-action-card {{ $addProductUrl === '#' ? 'disabled' : '' }}">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Add Product</div>
        <div class="qa-sub">List a new item in your store</div>
      </div>
    </a>
    <a href="{{ $manageProductsUrl }}" class="card quick-action-card {{ $manageProductsUrl === '#' ? 'disabled' : '' }}">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 6h16" />
          <path d="M4 12h16" />
          <path d="M4 18h16" />
        </svg>
      </div>
      <div>
        <div class="qa-title">Manage Products</div>
        <div class="qa-sub">Edit, update or remove listings</div>
      </div>
    </a>
    <a href="{{ $viewOrdersUrl }}" class="card quick-action-card {{ $viewOrdersUrl === '#' ? 'disabled' : '' }}">
      <div class="quick-action-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
          <path d="M3 6h18" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
      </div>
      <div>
        <div class="qa-title">View Orders</div>
        <div class="qa-sub">Track and fulfill customer orders</div>
      </div>
    </a>
  </div>
</div>

@endsection

@push('scripts')
<script>
  /*
   * Expected endpoint (proposed — confirm with backend or adjust the URL/parsing below
   * once the real controller exists):
   *
   *   GET {{ url('/api/vendor/dashboard') }}?range=week|month
   *
   *   Response:
   *   {
   *     "success": true,
   *     "data": {
   *       "total_products": 24,
   *       "pending_products": 3,
   *       "active_products": 19,
   *       "total_orders": 57,
   *       "product_status_counts": { "draft": 2, "pending": 3, "active": 19, "rejected": 0 },
   *       "sales": { "range": "week", "labels": ["Mon","Tue",...], "values": [1200, 800, ...] },
   *       "recent_orders": [
   *         { "id": 101, "order_number": "ORD-1042", "customer_name": "Riya Sharma",
   *           "amount": 1499, "status": "pending", "created_at": "2026-08-18T10:20:00Z" }
   *       ]
   *     }
   *   }
   */
  const API_BASE = "{{ url('/api/vendor') }}";
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
    return '₹' + Number(n).toLocaleString('en-IN');
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

      if (res.status === 404) {
        showMissingApiState();
        return;
      }

      const json = await res.json();
      if (!json.success) {
        showErrorState(json.message || 'Failed to load dashboard data.');
        return;
      }

      renderStats(json.data);
      renderProductStatus(json.data.product_status_counts);
      renderSalesChart(json.data.sales);
      renderRecentOrders(json.data.recent_orders);
      renderOperationalInsights(json.data);
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
  }

  function renderProductStatus(counts) {
    const wrap = document.getElementById('productStatusList');
    if (!counts) {
      wrap.innerHTML = '<div class="empty-state">No product status data available.</div>';
      return;
    }
    const rows = [
      ['draft', 'Draft'],
      ['pending', 'Pending'],
      ['active', 'Active'],
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
      wrap.innerHTML = '<div class="chart-empty">No sales data yet for this period.</div>';
      return;
    }

    const labels = sales.labels || sales.values.map((_, i) => i + 1);
    const values = sales.values;
    const max = Math.max(...values, 1);

    const change = sales.change_percentage === null || sales.change_percentage === undefined ?
      'No previous-period comparison' : `${sales.change_percentage >= 0 ? '+' : ''}${sales.change_percentage}% vs previous period`;
    wrap.innerHTML = `
            <div class="sales-summary"><strong>${formatCurrency(sales.total)}</strong><span class="sales-change">${change}</span></div>
            <div style="display:flex; align-items:flex-end; gap:10px; width:100%; height:200px; padding-top: 10px;">
                ${values.map((v, i) => {
                    const heightPct = Math.max((v / max) * 100, 3);
                    return `
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
                            <div title="${formatCurrency(v)}" style="width:100%; max-width:34px; height:${heightPct}%; background:var(--terracotta); border-radius:6px 6px 2px 2px; opacity:${0.55 + (v / max) * 0.45};"></div>
                            <div style="font-size:11px; color:var(--text-muted);">${labels[i] ?? ''}</div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
  }

  function renderOperationalInsights(data) {
    const attention = data.attention || {};
    document.getElementById('attentionList').innerHTML = [
      ['New orders to accept', attention.new_orders || 0],
      ['Orders to pack', attention.orders_to_pack || 0],
      ['Ready for pickup', attention.ready_for_pickup || 0],
      ['Rejected products', attention.rejected_products || 0],
    ].map(([label, count]) => `<div class="metric-line"><span>${label}</span><strong class="${count ? 'alert-count' : ''}">${count}</strong></div>`).join('');

    const lowStock = data.low_stock || [];
    document.getElementById('lowStockList').innerHTML = lowStock.length ? lowStock.map(item => `
      <div class="compact-row"><span>${item.sku || item.variant_name || 'Variant'}</span><b>${item.available_quantity} available</b></div>
    `).join('') : '<div class="empty-state">Stock levels look healthy.</div>';

    const products = data.top_products || [];
    document.getElementById('topProductsList').innerHTML = products.length ? products.map(item => `
      <div class="compact-row"><span>${item.product_name}</span><b>${item.units_sold} sold · ${formatCurrency(item.revenue)}</b></div>
    `).join('') : '<div class="empty-state">Sales will appear here after orders arrive.</div>';
  }

  function renderRecentOrders(orders) {
    const tbody = document.getElementById('recentOrdersBody');
    if (!orders || !orders.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No orders yet.</td></tr>';
      return;
    }

    tbody.innerHTML = orders.map(o => {
      const status = (o.status || '').toLowerCase();
      const label = status ? status.charAt(0).toUpperCase() + status.slice(1) : '—';
      return `
                <tr>
                    <td>${o.order_number || o.id}</td>
                    <td>${o.customer_name || '—'}</td>
                    <td>${formatCurrency(o.amount)}</td>
                    <td><span class="badge order-${status}">${label}</span></td>
                    <td>${formatDate(o.created_at)}</td>
                </tr>
            `;
    }).join('');
  }

  function showMissingApiState() {
    document.getElementById('salesChartWrap').innerHTML =
      '<div class="chart-empty">Dashboard API not available yet.</div>';
    document.getElementById('productStatusList').innerHTML =
      '<div class="empty-state">Dashboard API not available yet.</div>';
    document.getElementById('recentOrdersBody').innerHTML =
      '<tr><td colspan="5" class="empty-state">Dashboard API not available yet.</td></tr>';
    ['statTotalProducts', 'statPendingProducts', 'statActiveProducts', 'statTotalOrders']
    .forEach(id => document.getElementById(id).textContent = '—');
  }

  function showErrorState(message) {
    document.getElementById('salesChartWrap').innerHTML = `<div class="chart-empty">${message}</div>`;
    document.getElementById('productStatusList').innerHTML = `<div class="empty-state">${message}</div>`;
    document.getElementById('recentOrdersBody').innerHTML = `<tr><td colspan="5" class="empty-state">${message}</td></tr>`;
    ['attentionList', 'lowStockList', 'topProductsList'].forEach(id => {
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

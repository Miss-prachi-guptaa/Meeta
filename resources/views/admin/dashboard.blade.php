@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .page-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .page-header h1 {
        font-family: 'Fraunces', serif;
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }
    .page-header p {
        color: var(--text-secondary);
        font-size: 14.5px;
        margin-top: 5px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 22px 24px;
        box-shadow: var(--shadow-card);
        display: flex;
        align-items: center;
        gap: 18px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(60, 46, 33, 0.09);
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.terracotta { background: var(--terracotta-soft); color: var(--terracotta-dark); }
    .stat-icon.gold { background: var(--gold-soft); color: var(--gold); }
    .stat-icon.success { background: var(--success-soft); color: var(--success); }
    .stat-icon.neutral { background: var(--neutral-soft); color: var(--neutral); }

    .stat-icon svg { width: 24px; height: 24px; stroke-width: 2; }

    .stat-content .label {
        font-size: 11.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.8px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .stat-content .value {
        font-family: 'Fraunces', serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.1;
    }

    /* Charts Layout */
    .charts-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 22px;
        margin-bottom: 28px;
    }
    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow-card);
    }
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .section-head h2 {
        font-family: 'Fraunces', serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .section-head p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-top: 3px;
    }
    .chart-wrap {
        height: 270px;
        position: relative;
    }

    /* Table styling */
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    th {
        background: #fdf8f0;
        padding: 13px 18px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }
    td {
        padding: 15px 18px;
        font-size: 13.5px;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--bg-card-hover); }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .pill.paid, .pill.completed, .pill.delivered {
        background: var(--success-soft);
        color: var(--success);
    }
    .pill.pending, .pill.processing, .pill.confirmed {
        background: var(--gold-soft);
        color: var(--gold);
    }
    .pill.failed, .pill.cancelled {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .loading-state, .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: var(--text-muted);
        font-size: 14.5px;
    }

    @media (max-width: 1100px) {
        .charts-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1>Marketplace Overview</h1>
        <p>Monitor high-level platform sales, orders, vendor onboardings, and recent activities.</p>
    </div>
    <div class="header-right">
        <button class="btn btn-outline btn-sm" onclick="loadDashboard()">🔄 Refresh Stats</button>
    </div>
</div>

<div id="initialLoader" class="section-card loading-state">
    Loading dashboard metrics…
</div>

<div id="dashboardArea" style="display: none;">
    <!-- Top 4 Metric Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon terracotta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-content">
                <div class="label">Total Revenue</div>
                <div class="value" id="statRevenue">₹0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <div class="stat-content">
                <div class="label">Total Orders</div>
                <div class="value" id="statOrders">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            </div>
            <div class="stat-content">
                <div class="label">Active Vendors</div>
                <div class="value" id="statActiveVendors">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon neutral">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-content">
                <div class="label">Pending Vendor Approvals</div>
                <div class="value" id="statPendingVendors">0</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="section-card">
            <div class="section-head">
                <div>
                    <h2>Revenue Growth</h2>
                    <p>Daily paid orders revenue (Last 7 Days)</p>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="section-card">
            <div class="section-head">
                <div>
                    <h2>Order Volume</h2>
                    <p>Orders received daily</p>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="section-card">
        <div class="section-head">
            <div>
                <h2>Recent Marketplace Orders</h2>
                <p>Latest customer transactions across all vendors</p>
            </div>
            <a href="/admin/orders" class="btn btn-outline btn-sm">View All Orders →</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Placed At</th>
                    </tr>
                </thead>
                <tbody id="recentOrdersBody"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    let revenueChartInstance = null;
    let ordersChartInstance = null;

    async function loadDashboard() {
        const token = localStorage.getItem('meeta_token');
        const loader = document.getElementById('initialLoader');
        const area = document.getElementById('dashboardArea');

        try {
            const res = await fetch('/api/admin/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401 || res.status === 419) {
                localStorage.removeItem('meeta_token');
                location.href = '/admin/login';
                return;
            }

            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Failed to load dashboard.');

            const d = json.data;
            const s = d.stats || {};

            // Update stats
            document.getElementById('statRevenue').textContent = '₹' + Number(s.total_revenue || 0).toLocaleString('en-IN');
            document.getElementById('statOrders').textContent = Number(s.total_orders || 0).toLocaleString('en-IN');
            document.getElementById('statActiveVendors').textContent = Number(s.active_vendors || 0).toLocaleString('en-IN');
            document.getElementById('statPendingVendors').textContent = Number(s.pending_vendors || 0).toLocaleString('en-IN');

            // Render Recent Orders
            const tbody = document.getElementById('recentOrdersBody');
            const orders = d.recent_orders || [];
            if (orders.length > 0) {
                tbody.innerHTML = orders.map(o => `
                    <tr>
                        <td><strong style="color: var(--terracotta-dark);">#${o.order_number || o.id}</strong></td>
                        <td>${o.shipping_full_name || o.user?.name || 'Customer'}</td>
                        <td><strong>₹${Number(o.total_amount).toLocaleString('en-IN')}</strong></td>
                        <td><span class="pill ${String(o.payment_status).toLowerCase()}">${o.payment_status}</span></td>
                        <td><span class="pill ${String(o.order_status).toLowerCase()}">${o.order_status}</span></td>
                        <td>${new Date(o.created_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No recent orders yet.</td></tr>';
            }

            loader.style.display = 'none';
            area.style.display = 'block';

            // Warm Cream & Terracotta Chart Styling
            renderCharts(d.revenue_chart || {}, d.orders_chart || {});

        } catch (e) {
            loader.innerHTML = `<span style="color: var(--danger);">⚠️ ${e.message}</span>`;
        }
    }

    function renderCharts(revData, ordData) {
        const terracotta = '#c1622d';
        const terracottaSoft = 'rgba(193, 98, 45, 0.14)';
        const gold = '#b8862b';
        const textMuted = '#a5947c';
        const borderColor = '#e8dcc9';

        // Revenue Chart (Line)
        if (revenueChartInstance) revenueChartInstance.destroy();
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        revenueChartInstance = new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: revData.labels || [],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: revData.data || [],
                    borderColor: terracotta,
                    backgroundColor: terracottaSoft,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: terracotta,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textMuted, font: { family: 'Inter' } } },
                    y: { beginAtZero: true, grid: { color: borderColor }, ticks: { color: textMuted, font: { family: 'Inter' } } }
                }
            }
        });

        // Orders Chart (Bar)
        if (ordersChartInstance) ordersChartInstance.destroy();
        const ctxOrd = document.getElementById('ordersChart').getContext('2d');
        ordersChartInstance = new Chart(ctxOrd, {
            type: 'bar',
            data: {
                labels: ordData.labels || [],
                datasets: [{
                    label: 'Orders',
                    data: ordData.data || [],
                    backgroundColor: terracotta,
                    borderRadius: 6,
                    maxBarThickness: 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textMuted, font: { family: 'Inter' } } },
                    y: { beginAtZero: true, grid: { color: borderColor }, ticks: { color: textMuted, stepSize: 1, font: { family: 'Inter' } } }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endpush

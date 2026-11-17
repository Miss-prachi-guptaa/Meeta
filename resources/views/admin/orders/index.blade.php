@extends('layouts.admin')

@section('title', 'Orders & SLA Monitoring')

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
    .page-header h1 { font-size: 28px; font-weight: 700; }
    .page-header p { color: var(--text-secondary); font-size: 14.5px; margin-top: 5px; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-soft);
    }
    .stat-card .label {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.6px;
        font-weight: 600;
    }
    .stat-card .value {
        font-family: 'Fraunces', serif;
        font-size: 26px;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 4px;
    }
    .stat-card.alert .value { color: var(--danger); }
    .stat-card.success .value { color: var(--success); }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .toolbar-left { display: flex; gap: 12px; flex-wrap: wrap; flex: 1; }
    .toolbar input, .toolbar select { min-width: 180px; }

    .table-container {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-card);
    }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th {
        background: #fdf8f0;
        padding: 13px 18px;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }
    td {
        padding: 14px 18px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--bg-card-hover); }

    .sub-orders-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 6px;
    }
    .sub-order-pill {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12.5px;
    }

    .sla-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11.5px;
    }
    .sla-breached { background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(184, 72, 60, 0.2); }
    .sla-active { background: var(--gold-soft); color: var(--gold); border: 1px solid rgba(184, 134, 43, 0.2); }
    .sla-confirmed { background: var(--success-soft); color: var(--success); }

    /* Modal / Drawer */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
    }
    .modal-backdrop.show { display: flex; }
    .modal-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 26px 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
    }
</style>
@endpush

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1>Orders & SLA Monitoring</h1>
        <p>Monitor customer master orders, vendor-allocated sub-orders, and SLA confirmation timers.</p>
    </div>
    <div class="header-right">
        <button class="btn btn-outline btn-sm" onclick="fetchOrders()">🔄 Refresh</button>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Master Orders</div>
        <div class="value" id="statTotalOrders">-</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Sub-Orders</div>
        <div class="value" id="statTotalSubOrders">-</div>
    </div>
    <div class="stat-card">
        <div class="label">Pending Vendor Acceptance</div>
        <div class="value" id="statPendingConf">-</div>
    </div>
    <div class="stat-card alert">
        <div class="label">SLA Breached (>24h Timeout)</div>
        <div class="value" id="statSlaBreached">-</div>
    </div>
    <div class="stat-card success">
        <div class="label">Paid Platform Sales</div>
        <div class="value" id="statRevenue">-</div>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-left">
        <input type="text" id="searchInput" placeholder="Search Order ID, Vendor, Customer..." onkeyup="debounceSearch()">
        <select id="statusFilter" onchange="fetchOrders()">
            <option value="">All Statuses</option>
            <option value="confirmed">Pending Confirmation</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <select id="slaFilter" onchange="fetchOrders()">
            <option value="">All SLA States</option>
            <option value="1">SLA Breached (>24h)</option>
        </select>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Master Order</th>
                <th>Customer</th>
                <th>Sub-Orders / Vendor Allocation</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading orders...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Order Detail Modal -->
<div class="modal-backdrop" id="detailModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="font-size: 20px;">Order Details</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 22px; cursor: pointer;">&times;</button>
        </div>
        <div id="modalContent">Loading...</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let searchTimeout = null;

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchOrders, 400);
    }

    async function fetchOrders() {
        const token = localStorage.getItem('meeta_token');
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const slaBreached = document.getElementById('slaFilter').value;

        let url = `/api/admin/orders?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
        if (slaBreached) url += `&sla_breached=1`;

        try {
            const res = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                renderStats(data.data.stats);
                renderTable(data.data.orders.data);
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderStats(stats) {
        document.getElementById('statTotalOrders').textContent = stats.total_orders;
        document.getElementById('statTotalSubOrders').textContent = stats.total_sub_orders;
        document.getElementById('statPendingConf').textContent = stats.pending_confirmation;
        document.getElementById('statSlaBreached').textContent = stats.sla_breached_count;
        document.getElementById('statRevenue').textContent = '₹' + Number(stats.total_revenue || 0).toLocaleString();
    }

    function renderTable(orders) {
        const tbody = document.getElementById('ordersTableBody');
        if (!orders || orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No orders found matching criteria.</td></tr>`;
            return;
        }

        tbody.innerHTML = orders.map(o => {
            const subOrdersHtml = o.sub_orders.map(sub => {
                let slaBadgeClass = 'sla-active';
                if (sub.sla?.is_breached) slaBadgeClass = 'sla-breached';
                else if (sub.confirmed_at) slaBadgeClass = 'sla-confirmed';

                return `
                    <div class="sub-order-pill">
                        <div>
                            <strong>#${sub.sub_order_number}</strong> — ${sub.vendor?.store_name || 'Vendor'} 
                            <span style="color: var(--text-muted); font-size: 11px;">(${sub.order_status})</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="sla-badge ${slaBadgeClass}">⏱ ${sub.sla?.remaining_human || 'N/A'}</span>
                            ${sub.sla?.is_breached && sub.order_status !== 'cancelled' ? `
                                <button class="btn btn-danger btn-sm" onclick="autoCancelSubOrder(${sub.id})">Auto-Cancel</button>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');

            return `
                <tr>
                    <td>
                        <strong style="color: var(--terracotta-dark);">#${o.order_number}</strong>
                    </td>
                    <td>
                        <div>${o.shipping_full_name}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">${o.shipping_phone}</div>
                    </td>
                    <td style="max-width: 360px;">
                        <div class="sub-orders-list">${subOrdersHtml}</div>
                    </td>
                    <td><strong>₹${Number(o.total_amount).toLocaleString()}</strong></td>
                    <td>
                        <span class="badge" style="background: ${o.payment_status === 'paid' ? 'var(--success-soft)' : 'var(--danger-soft)'}; color: ${o.payment_status === 'paid' ? 'var(--success)' : 'var(--danger)'};">
                            ${o.payment_status}
                        </span>
                    </td>
                    <td>${new Date(o.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-outline btn-sm" onclick="viewOrderDetail(${o.id})">Inspect</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function autoCancelSubOrder(id) {
        if (!confirm('Are you sure you want to cancel this SLA-breached sub-order? Inventory will be automatically restocked.')) return;
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(`/api/admin/sub-orders/${id}/auto-cancel`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: 'SLA 24-hour timeout auto-cancellation by administrator.' })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                fetchOrders();
            } else {
                alert(data.message || 'Action failed.');
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function viewOrderDetail(id) {
        document.getElementById('detailModal').classList.add('show');
        document.getElementById('modalContent').innerHTML = 'Loading order details...';

        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(`/api/admin/orders/${id}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                const o = data.data;
                document.getElementById('modalContent').innerHTML = `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <div>
                            <h4 style="font-size: 16px;">Master Order: #${o.order_number}</h4>
                            <div style="font-size: 13px; color: var(--text-muted);">Customer: ${o.shipping_full_name} (${o.shipping_phone})</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 18px; font-weight: 700; color: var(--terracotta-dark);">₹${Number(o.total_amount).toLocaleString()}</div>
                            <span class="badge" style="background: var(--success-soft); color: var(--success);">${o.payment_status}</span>
                        </div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 16px; font-size: 13px;">
                        <strong>Shipping Address:</strong> ${o.shipping_address_line_1}, ${o.shipping_city}, ${o.shipping_state} - ${o.shipping_postal_code}
                    </div>
                    <h5 style="font-size: 14px; margin-bottom: 10px;">Allocated Sub-Orders:</h5>
                    ${o.sub_orders.map(sub => `
                        <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fff;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <strong>Sub-Order: #${sub.sub_order_number} (${sub.vendor?.store_name})</strong>
                                <span class="badge" style="background: var(--terracotta-soft); color: var(--terracotta-dark);">${sub.order_status}</span>
                            </div>
                            <ul style="font-size: 13px; padding-left: 18px; margin: 0;">
                                ${sub.order_items.map(i => `<li>${i.product_name} (${i.variant_name || 'Standard'}) x ${i.quantity} — ₹${Number(i.total_amount).toLocaleString()}</li>`).join('')}
                            </ul>
                        </div>
                    `).join('')}
                `;
            }
        } catch (e) {
            console.error(e);
        }
    }

    function closeModal() {
        document.getElementById('detailModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', fetchOrders);
</script>
@endpush

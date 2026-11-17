@extends('layouts.admin')

@section('title', 'Payouts & Settlements')

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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
    .stat-card.success .value { color: var(--success); }
    .stat-card.warning .value { color: var(--gold); }
    .stat-card.alert .value { color: var(--danger); }

    .tabs-nav {
        display: flex;
        gap: 12px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
    }
    .tab-btn {
        background: none;
        border: none;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        font-family: inherit;
    }
    .tab-btn.active {
        color: var(--terracotta-dark);
        border-bottom-color: var(--terracotta);
    }

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
</style>
@endpush

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1>Weekly Payout Batches & Settlements</h1>
        <p>Review eligible vendor payouts, approve weekly disbursement batches, and handle bank transfer retries.</p>
    </div>
    <div class="header-right">
        <button class="btn btn-terracotta" onclick="triggerProcessBatch()">⚡ Process Weekly Payout Batch</button>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card success">
        <div class="label">Total Paid Out</div>
        <div class="value" id="statPaid">-</div>
    </div>
    <div class="stat-card warning">
        <div class="label">Eligible Pending Settlements</div>
        <div class="value" id="statEligible">-</div>
    </div>
    <div class="stat-card alert">
        <div class="label">Bank Transfer Errors (Retry Needed)</div>
        <div class="value" id="statFailed">-</div>
    </div>
</div>

<div class="tabs-nav">
    <button class="tab-btn active" onclick="switchTab('eligible')">Eligible for Payout Batch</button>
    <button class="tab-btn" onclick="switchTab('history')">Payout History & Logs</button>
</div>

<div id="eligibleSection">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Verified Bank Details</th>
                    <th>Eligible Delivered Orders</th>
                    <th>Payable Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="eligibleTableBody">
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading eligible batch...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="historySection" style="display: none;">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Payout Ref</th>
                    <th>Vendor</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Bank UTR / Ref</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                <tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading payout history...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeTab = 'eligible';

    function switchTab(tab) {
        activeTab = tab;
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        if (tab === 'eligible') {
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.getElementById('eligibleSection').style.display = 'block';
            document.getElementById('historySection').style.display = 'none';
            loadEligibleBatch();
        } else {
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.getElementById('eligibleSection').style.display = 'none';
            document.getElementById('historySection').style.display = 'block';
            loadPayoutHistory();
        }
    }

    async function loadStats() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/payouts', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                document.getElementById('statPaid').textContent = '₹' + Number(data.data.stats.total_paid || 0).toLocaleString();
                document.getElementById('statEligible').textContent = '₹' + Number(data.data.stats.eligible_settlements || 0).toLocaleString();
                document.getElementById('statFailed').textContent = data.data.stats.failed_retries;
            }
        } catch (e) { console.error(e); }
    }

    async function loadEligibleBatch() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/payouts/eligible', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                const tbody = document.getElementById('eligibleTableBody');
                const vendors = data.data.vendors;
                if (!vendors || vendors.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">No pending settlements ready for disbursement this week.</td></tr>`;
                    return;
                }
                tbody.innerHTML = vendors.map(v => `
                    <tr>
                        <td><strong>${v.store_name}</strong><br><span style="font-size: 12px; color: var(--text-muted);">${v.business_name}</span></td>
                        <td>
                            ${v.bank_account ? `
                                <div><strong>${v.bank_account.bank_name}</strong></div>
                                <div style="font-size: 12px; color: var(--text-muted);">A/C: ${v.bank_account.account_number} | IFSC: ${v.bank_account.ifsc_code}</div>
                            ` : '<span style="color: var(--danger); font-size: 12px;">⚠️ No Verified Bank</span>'}
                        </td>
                        <td>${v.eligible_orders} Orders</td>
                        <td><strong style="color: var(--terracotta-dark);">₹${Number(v.payable_amount).toLocaleString()}</strong></td>
                        <td>
                            <span class="badge" style="background: var(--gold-soft); color: var(--gold);">Ready for Batch</span>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    async function loadPayoutHistory() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/payouts', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                const tbody = document.getElementById('historyTableBody');
                const payouts = data.data.payouts.data;
                if (!payouts || payouts.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">No historical payouts found.</td></tr>`;
                    return;
                }
                tbody.innerHTML = payouts.map(p => `
                    <tr>
                        <td><strong>#${p.payout_number}</strong></td>
                        <td>${p.vendor?.store_name || 'Vendor'}</td>
                        <td><strong>₹${Number(p.amount).toLocaleString()}</strong></td>
                        <td>${p.payment_method}</td>
                        <td><code>${p.transaction_reference || 'N/A'}</code></td>
                        <td>
                            <span class="badge" style="background: ${p.status === 'completed' ? 'var(--success-soft)' : 'var(--danger-soft)'}; color: ${p.status === 'completed' ? 'var(--success)' : 'var(--danger)'};">
                                ${p.status}
                            </span>
                        </td>
                        <td>${new Date(p.created_at).toLocaleDateString()}</td>
                        <td>
                            ${p.status === 'failed' ? `
                                <button class="btn btn-outline btn-sm" onclick="retryPayout(${p.id})">Retry</button>
                            ` : '<span style="color: var(--text-muted); font-size: 12px;">Completed</span>'}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    async function triggerProcessBatch() {
        if (!confirm('Are you sure you want to trigger this Weekly Payout Batch for all eligible vendors?')) return;
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/payouts/batch/process', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ payment_method: 'bank_transfer' })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                loadStats();
                loadEligibleBatch();
            } else {
                alert(data.message || 'Processing failed.');
            }
        } catch (e) { console.error(e); }
    }

    async function retryPayout(id) {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(`/api/admin/payouts/${id}/retry`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                loadStats();
                loadPayoutHistory();
            }
        } catch (e) { console.error(e); }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();
        loadEligibleBatch();
    });
</script>
@endpush

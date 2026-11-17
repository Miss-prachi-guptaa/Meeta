@extends('vendor.layouts.app')

@section('title', 'Payouts & Earnings')

@push('styles')
<style>
    .page-head {
        margin-bottom: 24px;
    }
    .page-head h1 {
        font-family: 'Fraunces', serif;
        font-size: 30px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .page-head p {
        color: var(--text-secondary);
        font-size: 14.5px;
        margin-top: 4px;
    }

    .finance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .fin-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px 22px;
        box-shadow: var(--shadow-soft);
    }
    .fin-card .label {
        font-size: 11.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.6px;
        font-weight: 600;
    }
    .fin-card .val {
        font-family: 'Fraunces', serif;
        font-size: 26px;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 6px;
    }
    .fin-card.earned .val { color: var(--terracotta-dark); }
    .fin-card.paid .val { color: var(--success); }
    .fin-card.pending .val { color: var(--gold); }

    .tabs-bar {
        display: flex;
        gap: 14px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
    }
    .tab-item {
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
    .tab-item.active {
        color: var(--terracotta-dark);
        border-bottom-color: var(--terracotta);
    }

    .table-card {
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
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }
    td {
        padding: 14px 18px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--border-color);
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--bg-card-hover); }

    .pill {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .pill.completed { background: var(--success-soft); color: var(--success); }
    .pill.pending { background: var(--gold-soft); color: var(--gold); }
    .pill.credit { color: var(--success); font-weight: 700; }
    .pill.debit { color: var(--danger); font-weight: 700; }
</style>
@endpush

@section('content')
<div class="page-head">
    <h1>Payouts & Earnings</h1>
    <p>Track your settled store revenue, platform commission deductions, and bank disbursements.</p>
</div>

<div class="finance-grid">
    <div class="fin-card earned">
        <div class="label">Total Settled Earnings</div>
        <div class="val" id="valSettled">₹0</div>
    </div>
    <div class="fin-card pending">
        <div class="label">Pending Clearance (Return Window)</div>
        <div class="val" id="valPending">₹0</div>
    </div>
    <div class="fin-card paid">
        <div class="label">Disbursed to Bank</div>
        <div class="val" id="valPaid">₹0</div>
    </div>
</div>

<div class="tabs-bar">
    <button class="tab-item active" onclick="switchFinTab('payouts')">Bank Payouts History</button>
    <button class="tab-item" onclick="switchFinTab('ledger')">Detailed Ledger Transactions</button>
</div>

<div id="payoutsTab">
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Payout Reference</th>
                    <th>Amount Disbursed</th>
                    <th>Payment Method</th>
                    <th>Bank UTR Reference</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="payoutsBody">
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading payouts...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="ledgerTab" style="display: none;">
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance After</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="ledgerBody">
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading ledger...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    async function loadFinanceData() {
        const token = localStorage.getItem('meeta_token');
        try {
            // Overview stats
            const resOverview = await fetch('/api/vendor/finance/overview', { headers: { 'Authorization': `Bearer ${token}` } });
            const dataOverview = await resOverview.json();
            if (dataOverview.success) {
                const d = dataOverview.data;
                document.getElementById('valSettled').textContent = '₹' + Number(d.settled_earnings).toLocaleString('en-IN');
                document.getElementById('valPending').textContent = '₹' + Number(d.pending_clearance).toLocaleString('en-IN');
                document.getElementById('valPaid').textContent = '₹' + Number(d.total_payouts).toLocaleString('en-IN');
            }

            // Payouts
            const resPayouts = await fetch('/api/vendor/finance/payouts', { headers: { 'Authorization': `Bearer ${token}` } });
            const dataPayouts = await resPayouts.json();
            if (dataPayouts.success) {
                const tbody = document.getElementById('payoutsBody');
                const list = dataPayouts.data.data;
                if (!list || list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No bank payouts disbursed yet.</td></tr>';
                } else {
                    tbody.innerHTML = list.map(p => `
                        <tr>
                            <td><strong>#${p.payout_number}</strong></td>
                            <td><strong style="color: var(--terracotta-dark);">₹${Number(p.amount).toLocaleString('en-IN')}</strong></td>
                            <td>${p.payment_method}</td>
                            <td><code>${p.transaction_reference || 'Processing'}</code></td>
                            <td><span class="pill ${p.status}">${p.status}</span></td>
                            <td>${new Date(p.created_at).toLocaleDateString('en-IN')}</td>
                        </tr>
                    `).join('');
                }
            }

            // Ledger
            const resLedger = await fetch('/api/vendor/finance/ledger', { headers: { 'Authorization': `Bearer ${token}` } });
            const dataLedger = await resLedger.json();
            if (dataLedger.success) {
                const tbody = document.getElementById('ledgerBody');
                const list = dataLedger.data.data;
                if (!list || list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">No ledger entries recorded yet.</td></tr>';
                } else {
                    tbody.innerHTML = list.map(l => `
                        <tr>
                            <td>${new Date(l.created_at).toLocaleDateString('en-IN')}</td>
                            <td><span class="pill ${l.transaction_type}">${l.transaction_type.toUpperCase()}</span></td>
                            <td><strong>${l.transaction_type === 'credit' ? '+' : '-'}₹${Number(l.amount).toLocaleString('en-IN')}</strong></td>
                            <td>₹${Number(l.balance_after).toLocaleString('en-IN')}</td>
                            <td style="color: var(--text-secondary);">${l.remarks || '—'}</td>
                        </tr>
                    `).join('');
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function switchFinTab(tab) {
        document.querySelectorAll('.tab-item').forEach(b => b.classList.remove('active'));
        if (tab === 'payouts') {
            document.querySelectorAll('.tab-item')[0].classList.add('active');
            document.getElementById('payoutsTab').style.display = 'block';
            document.getElementById('ledgerTab').style.display = 'none';
        } else {
            document.querySelectorAll('.tab-item')[1].classList.add('active');
            document.getElementById('payoutsTab').style.display = 'none';
            document.getElementById('ledgerTab').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', loadFinanceData);
</script>
@endpush

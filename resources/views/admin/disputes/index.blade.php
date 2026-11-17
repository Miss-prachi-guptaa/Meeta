@extends('layouts.admin')

@section('title', 'Disputes & Returns')

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
        max-width: 600px;
        padding: 26px;
    }
</style>
@endpush

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1>Dispute & Return Resolutions</h1>
        <p>Review contested return cases and decide in favor of Customer (Refund + Ledger Reversal) or Vendor.</p>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Return #</th>
                <th>Order</th>
                <th>Customer</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="disputeTableBody">
            <tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading disputes...</td></tr>
        </tbody>
    </table>
</div>

<!-- Dispute Decision Modal -->
<div class="modal-backdrop" id="decisionModal">
    <div class="modal-box">
        <h3 style="font-size: 18px; margin-bottom: 16px;" id="decisionTitle">Resolve Return Case</h3>
        <div id="decisionBody" style="margin-bottom: 20px; font-size: 13.5px;"></div>
        
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 6px;">Admin Resolution Notes</label>
            <textarea id="adminNotes" rows="3" style="width: 100%;" placeholder="State reason for resolution decision..."></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-danger" onclick="submitDecision('vendor')">Reject Claim (Favor Vendor)</button>
                <button class="btn btn-success" onclick="submitDecision('customer')">Approve Refund (Favor Customer)</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeDisputeId = null;

    async function loadDisputes() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/disputes', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                const tbody = document.getElementById('disputeTableBody');
                const list = data.data.data;
                if (!list || list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No active disputes or returns requiring intervention.</td></tr>`;
                    return;
                }
                tbody.innerHTML = list.map(d => `
                    <tr>
                        <td><strong>#${d.return_number}</strong></td>
                        <td>#${d.order?.order_number || 'Order'}</td>
                        <td>${d.user?.name || 'Customer'}</td>
                        <td>${d.reason || 'Return requested'}</td>
                        <td>
                            <span class="badge" style="background: ${d.status === 'approved' ? 'var(--success-soft)' : 'var(--gold-soft)'}; color: ${d.status === 'approved' ? 'var(--success)' : 'var(--gold)'};">
                                ${d.status}
                            </span>
                        </td>
                        <td>${new Date(d.created_at).toLocaleDateString()}</td>
                        <td>
                            ${d.status === 'pending' || d.status === 'requested' ? `
                                <button class="btn btn-outline btn-sm" onclick="openDecisionModal(${d.id}, '${d.return_number}', '${d.reason}')">Resolve</button>
                            ` : '<span style="color: var(--text-muted); font-size: 12px;">Resolved</span>'}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    function openDecisionModal(id, number, reason) {
        activeDisputeId = id;
        document.getElementById('decisionTitle').textContent = `Resolve Case: #${number}`;
        document.getElementById('decisionBody').innerHTML = `
            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px;">
                <strong>Customer Claim:</strong> ${reason}
            </div>
        `;
        document.getElementById('adminNotes').value = '';
        document.getElementById('decisionModal').classList.add('show');
    }

    async function submitDecision(favor) {
        const notes = document.getElementById('adminNotes').value.trim();
        if (!notes) {
            alert('Please provide resolution notes.');
            return;
        }

        const endpoint = favor === 'customer' 
            ? `/api/admin/disputes/${activeDisputeId}/resolve-customer`
            : `/api/admin/disputes/${activeDisputeId}/resolve-vendor`;

        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ admin_note: notes })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                closeModal();
                loadDisputes();
            } else {
                alert(data.message || 'Action failed.');
            }
        } catch (e) { console.error(e); }
    }

    function closeModal() {
        document.getElementById('decisionModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', loadDisputes);
</script>
@endpush

@extends('vendor.layouts.app')

@section('title', 'Returns & Claims')

@push('styles')
<style>
    .page-head { margin-bottom: 24px; }
    .page-head h1 {
        font-family: 'Fraunces', serif;
        font-size: 30px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .page-head p { color: var(--text-secondary); font-size: 14.5px; margin-top: 4px; }

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
        vertical-align: middle;
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
    .pill.pending, .pill.requested { background: var(--gold-soft); color: var(--gold); }
    .pill.approved { background: var(--success-soft); color: var(--success); }
    .pill.disputed { background: var(--danger-soft); color: var(--danger); }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        font-family: inherit;
    }
    .btn-outline { background: #fff; border-color: var(--border-color-strong); color: var(--text-primary); }
    .btn-success { background: var(--success); color: #fff; }
    .btn-danger { background: var(--danger); color: #fff; }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-backdrop.show { display: flex; }
    .modal-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 550px;
        padding: 24px;
    }
</style>
@endpush

@section('content')
<div class="page-head">
    <h1>Customer Returns & Claims</h1>
    <p>Review return requests for your delivered items and approve pickup or contest via dispute.</p>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Return #</th>
                <th>Order</th>
                <th>Customer</th>
                <th>Item Details</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="returnsBody">
            <tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading returns...</td></tr>
        </tbody>
    </table>
</div>

<!-- Return Response Modal -->
<div class="modal-backdrop" id="responseModal">
    <div class="modal-box">
        <h3 style="font-size: 18px; margin-bottom: 14px;" id="modalTitle">Respond to Return</h3>
        <div id="modalDetails" style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 16px; font-size: 13px;"></div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 6px;">Vendor Response / Reason</label>
            <textarea id="vendorNotes" rows="3" style="width: 100%; border: 1px solid var(--border-color-strong); border-radius: 8px; padding: 10px;" placeholder="Provide acceptance notes or justification for raising dispute..."></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-danger" onclick="submitResponse('dispute')">Raise Dispute</button>
                <button class="btn btn-success" onclick="submitResponse('accept')">Accept Return</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeReturnId = null;

    async function loadReturns() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/vendor/returns', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                const tbody = document.getElementById('returnsBody');
                const list = data.data.data;
                if (!list || list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No customer return claims for your store.</td></tr>';
                    return;
                }
                tbody.innerHTML = list.map(r => `
                    <tr>
                        <td><strong>#${r.return_number}</strong></td>
                        <td>#${r.order?.order_number || 'Order'}</td>
                        <td>${r.user?.name || 'Customer'}</td>
                        <td>
                            ${r.items.map(i => `<div>${i.order_item?.product?.name || 'Product'} (Qty: ${i.quantity})</div>`).join('')}
                        </td>
                        <td>${r.reason}</td>
                        <td><span class="pill ${r.status}">${r.status}</span></td>
                        <td>
                            ${r.status === 'pending' || r.status === 'requested' ? `
                                <button class="btn btn-outline" onclick="openResponseModal(${r.id}, '${r.return_number}', '${r.reason}')">Respond</button>
                            ` : `<span style="font-size: 12px; color: var(--text-muted);">Processed</span>`}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    function openResponseModal(id, number, reason) {
        activeReturnId = id;
        document.getElementById('modalTitle').textContent = `Respond to Return: #${number}`;
        document.getElementById('modalDetails').innerHTML = `<strong>Customer Claim:</strong> ${reason}`;
        document.getElementById('vendorNotes').value = '';
        document.getElementById('responseModal').classList.add('show');
    }

    async function submitResponse(action) {
        const notes = document.getElementById('vendorNotes').value.trim();
        if (!notes) {
            alert('Please provide response notes.');
            return;
        }

        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(`/api/vendor/returns/${activeReturnId}/respond`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, notes })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                closeModal();
                loadReturns();
            } else {
                alert(data.message || 'Action failed.');
            }
        } catch (e) { console.error(e); }
    }

    function closeModal() {
        document.getElementById('responseModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', loadReturns);
</script>
@endpush

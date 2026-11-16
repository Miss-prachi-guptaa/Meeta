@extends('layouts.admin')

@section('title', 'Vendors')

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

    .page-header h1 { font-size: 30px; font-weight: 700; }
    .page-header p { color: var(--text-secondary); font-size: 14.5px; margin-top: 5px; }

    .total-count-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow-soft);
        padding: 14px 26px;
        text-align: center;
        min-width: 150px;
    }

    .total-count-box .count-label {
        font-size: 12px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    .total-count-box .count-value {
        font-family: 'Fraunces', serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--terracotta-dark);
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        gap: 14px;
        margin-bottom: 22px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 260px;
        max-width: 460px;
    }

    .search-box svg {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 17px;
        height: 17px;
        color: var(--text-muted);
    }

    .search-box input {
        width: 100%;
        padding: 13px 16px 13px 40px;
        font-size: 14px;
        border-radius: 11px;
    }

    .status-filter, .date-filter { min-width: 170px; }
    .date-filter { color: var(--text-secondary); }

    .filter-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 4px;
        display: block;
    }

    .filter-group { display: flex; flex-direction: column; }

    /* Table */
    .table-wrap { overflow-x: auto; }

    table { width: 100%; border-collapse: collapse; min-width: 900px; }

    thead th {
        text-align: left;
        font-size: 12px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 17px 20px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
        background: var(--bg-card-hover);
    }

    thead tr th:first-child { border-radius: var(--radius) 0 0 0; }
    thead tr th:last-child { border-radius: 0 var(--radius) 0 0; }

    tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        vertical-align: middle;
    }

    tbody tr:hover { background: var(--bg-card-hover); }
    tbody tr:last-child td { border-bottom: none; }

    .vendor-cell { display: flex; align-items: center; gap: 12px; }

    .vendor-logo {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--terracotta-soft);
        color: var(--terracotta-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .vendor-name { font-weight: 600; color: var(--text-primary); }
    .vendor-sub { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }

    .contact-line { font-size: 13.5px; }
    .contact-line + .contact-line { color: var(--text-secondary); margin-top: 2px; }

    .badge.status-approved { background: var(--success-soft); color: var(--success); }
    .badge.status-pending { background: var(--gold-soft); color: var(--gold); }
    .badge.status-draft { background: var(--neutral-soft); color: var(--neutral); }
    .badge.status-rejected { background: var(--danger-soft); color: var(--danger); }
    .badge.status-suspended { background: var(--danger-soft); color: var(--danger); }

    .rejection-note {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        color: var(--danger);
        margin-top: 4px;
        cursor: help;
    }

    .actions-cell { display: flex; gap: 7px; flex-wrap: wrap; }

    .empty-state, .loading-state {
        padding: 70px 20px;
        text-align: center;
        color: var(--text-muted);
        font-size: 14px;
    }

    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 17px 20px;
        font-size: 13px;
        color: var(--text-secondary);
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination { display: flex; gap: 6px; }
    .pagination button {
        min-width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--border-color-strong);
        background: #fff;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 13px;
        font-family: inherit;
    }
    .pagination button:hover:not(:disabled) { background: var(--bg-card-hover); }
    .pagination button.active { background: var(--terracotta); color: #fff; border-color: var(--terracotta); }
    .pagination button:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Modal (shared) */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(60, 46, 33, 0.35);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 200;
        padding: 20px;
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fffdf9;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        width: 100%;
        max-width: 460px;
        padding: 28px;
        box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18);
    }

    .modal-box h3 { font-size: 19px; margin-bottom: 6px; }
    .modal-box p { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 16px; }
    .modal-box textarea { width: 100%; min-height: 90px; resize: vertical; margin-bottom: 18px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; }
    .modal-error { color: var(--danger); font-size: 12.5px; margin-top: -10px; margin-bottom: 14px; display: none; }

    /* Vendor detail panel */
    .detail-overlay {
        position: fixed;
        inset: 0;
        background: rgba(60, 46, 33, 0.35);
        display: none;
        justify-content: flex-end;
        z-index: 210;
    }
    .detail-overlay.open { display: flex; }

    .detail-panel {
        background: #fffdf9;
        width: 100%;
        max-width: 560px;
        height: 100%;
        overflow-y: auto;
        padding: 32px;
        box-shadow: -8px 0 30px rgba(60, 46, 33, 0.15);
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-header h2 { font-size: 22px; }
    .detail-header .close-btn {
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color-strong);
        border-radius: 8px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    .detail-section { margin-bottom: 26px; }
    .detail-section h4 {
        font-size: 12px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: var(--terracotta-dark);
        margin-bottom: 12px;
        font-weight: 700;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 18px;
    }

    .detail-item .label { font-size: 12px; color: var(--text-muted); margin-bottom: 3px; }
    .detail-item .value { font-size: 14px; color: var(--text-primary); font-weight: 500; }

    .doc-list { display: flex; flex-direction: column; gap: 8px; }
    .doc-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-card-hover);
        font-size: 13.5px;
    }
    .doc-item .doc-type { font-weight: 600; text-transform: capitalize; }
    .doc-item .doc-status { font-size: 11.5px; }

    .rejection-box {
        background: var(--danger-soft);
        border: 1px solid rgba(184, 72, 60, 0.25);
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 13.5px;
        color: var(--danger);
    }

    .detail-loading { text-align: center; padding: 60px 0; color: var(--text-muted); }

    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #fff;
        border: 1px solid var(--border-color-strong);
        border-left: 4px solid var(--success);
        padding: 14px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        z-index: 300;
        box-shadow: 0 8px 24px rgba(60, 46, 33, 0.18);
        transform: translateY(20px);
        opacity: 0;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.error { border-left-color: var(--danger); }
    .activity-label{font-size:11px;color:var(--terracotta);font-weight:600;margin-top:3px}.activity-date{white-space:nowrap}

    @media (max-width: 600px) {
        .detail-panel { max-width: 100%; padding: 22px; }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h1>Vendors</h1>
        <p>Review, approve and manage every artisan seller on MEETA.</p>
    </div>
    <div class="total-count-box">
        <div class="count-label">Total Vendors</div>
        <div class="count-value" id="totalVendorCount">—</div>
    </div>
</div>

<div class="toolbar">
    <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchInput" placeholder="Search vendors by store, owner or email...">
    </div>

    <div class="filter-group">
        <label class="filter-label">Status</label>
        <select id="statusFilter" class="status-filter">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <div class="filter-group">
        <label class="filter-label">Activity after</label>
        <input type="date" id="dateFilter" class="date-filter">
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Owner</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Review Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="vendorsTableBody">
                <tr><td colspan="6" class="loading-state">Loading vendors...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span id="tableSummary"></span>
        <div class="pagination" id="paginationControls"></div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <h3>Reject Vendor</h3>
        <p>This reason will be shared with the vendor.</p>
        <textarea id="rejectReason" placeholder="e.g. GST document is invalid."></textarea>
        <div class="modal-error" id="rejectError">Rejection reason is required.</div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeRejectModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectBtn">Reject Vendor</button>
        </div>
    </div>
</div>

<!-- Vendor Detail Panel -->
<div class="detail-overlay" id="detailOverlay">
    <div class="detail-panel">
        <div class="detail-header">
            <h2 id="detailStoreName">Vendor Details</h2>
            <button class="close-btn" onclick="closeDetailPanel()">✕</button>
        </div>
        <div id="detailContent">
            <div class="detail-loading">Loading vendor details...</div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

@endsection

@push('scripts')
<script>
    const API_BASE = "{{ url('/api/admin') }}";
    const STORAGE_BASE = "{{ url('/storage') }}";
    const token = localStorage.getItem('meeta_token');

    let allVendors = [];
    let currentPage = 1;
    const perPage = 10;
    let rejectTargetId = null;

    function authHeaders() {
        return {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function reviewActivity(vendor) {
        if (vendor.status === 'pending' && vendor.submitted_at) {
            return `<div class="activity-date">${formatDate(vendor.submitted_at)}</div><div class="activity-label">Submitted for review</div>`;
        }
        return `<div class="activity-date">${formatDate(vendor.created_at)}</div><div class="activity-label">Joined</div>`;
    }

    function vendorDisplayName(vendor) {
        const u = vendor.user || {};
        return u.display_name || [u.first_name, u.last_name].filter(Boolean).join(' ') || '—';
    }

    function initials(name) {
        if (!name || name === '—') return '?';
        return name.trim().split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase();
    }

    function statusBadge(status) {
        return `<span class="badge status-${status}">${status}</span>`;
    }

    function actionButtons(vendor) {
        const id = vendor.id;
        let buttons = `<button class="btn btn-outline btn-sm" onclick="openDetailPanel(${id})">View</button>`;

        if (vendor.status === 'pending' || vendor.status === 'draft') {
            buttons += `<button class="btn btn-success btn-sm" onclick="approveVendor(${id})">Approve</button>`;
            buttons += `<button class="btn btn-danger btn-sm" onclick="openRejectModal(${id})">Reject</button>`;
        }

        if (vendor.status === 'approved') {
            buttons += `<button class="btn btn-danger btn-sm" onclick="suspendVendor(${id})">Suspend</button>`;
        }

        // Rejected and Suspended vendors: View only (per spec)
        return `<div class="actions-cell">${buttons}</div>`;
    }

    function renderRows(vendors) {
        const tbody = document.getElementById('vendorsTableBody');

        if (!vendors.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No vendors match these filters.</td></tr>`;
            return;
        }

        tbody.innerHTML = vendors.map(vendor => {
            const u = vendor.user || {};
            const name = vendorDisplayName(vendor);
            const logoHtml = vendor.logo
                ? `<img class="vendor-logo" src="${STORAGE_BASE}/${vendor.logo}" alt="">`
                : `<div class="vendor-logo">${initials(vendor.store_name)}</div>`;

            let rejectionHtml = '';
            if (vendor.status === 'rejected' && vendor.rejection_reason) {
                rejectionHtml = `<div class="rejection-note" title="${vendor.rejection_reason.replace(/"/g, '&quot;')}">ⓘ Reason: ${vendor.rejection_reason.length > 40 ? vendor.rejection_reason.slice(0, 40) + '…' : vendor.rejection_reason}</div>`;
            }

            return `
                <tr>
                    <td>
                        <div class="vendor-cell">
                            ${logoHtml}
                            <div>
                                <div class="vendor-name">${vendor.store_name || '—'}</div>
                                <div class="vendor-sub">${vendor.business_name || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td>${name}</td>
                    <td>
                        <div class="contact-line">${u.email || vendor.support_email || '—'}</div>
                        <div class="contact-line">${u.phone || vendor.support_phone || '—'}</div>
                    </td>
                    <td>
                        ${statusBadge(vendor.status)}
                        ${rejectionHtml}
                    </td>
                    <td>${reviewActivity(vendor)}</td>
                    <td>${actionButtons(vendor)}</td>
                </tr>
            `;
        }).join('');
    }

    function applyFiltersAndRender() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const dateAfter = document.getElementById('dateFilter').value;

        let filtered = allVendors.filter(v => {
            if (status && v.status !== status) return false;

            if (dateAfter) {
                const joined = new Date(v.status === 'pending' && v.submitted_at ? v.submitted_at : v.created_at);
                const boundary = new Date(dateAfter);
                if (joined < boundary) return false;
            }

            if (search) {
                const u = v.user || {};
                const haystack = [
                    v.store_name, v.business_name, u.email, v.support_email,
                    [u.first_name, u.last_name].filter(Boolean).join(' ')
                ].filter(Boolean).join(' ').toLowerCase();
                if (!haystack.includes(search)) return false;
            }

            return true;
        });

        document.getElementById('totalVendorCount').textContent = allVendors.length;

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        renderRows(pageItems);
        renderPagination(filtered.length, totalPages, start, pageItems.length);
    }

    function renderPagination(totalFiltered, totalPages, start, shownCount) {
        const wrap = document.getElementById('paginationControls');
        const summary = document.getElementById('tableSummary');

        summary.textContent = totalFiltered === 0
            ? 'No vendors to show'
            : `Showing ${start + 1}-${start + shownCount} of ${totalFiltered} vendors`;

        let html = `<button ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})">‹</button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 7 && i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
                if (i === 2 || i === totalPages - 1) html += `<button disabled>…</button>`;
                continue;
            }
            html += `<button class="${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
        }

        html += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})">›</button>`;
        wrap.innerHTML = html;
    }

    function goToPage(page) {
        if (page < 1) return;
        currentPage = page;
        applyFiltersAndRender();
    }

    async function loadVendors() {
        const tbody = document.getElementById('vendorsTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="loading-state">Loading vendors...</td></tr>`;

        try {
            const res = await fetch(`${API_BASE}/vendors?per_page=1000`, { headers: authHeaders() });
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="6" class="empty-state">${json.message || 'Failed to load vendors.'}</td></tr>`;
                return;
            }

            allVendors = json.data.data || [];
            applyFiltersAndRender();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">Something went wrong. Please try again.</td></tr>`;
            console.error(e);
        }
    }

    async function approveVendor(id) {
        if (!confirm('Approve this vendor?')) return;
        try {
            const res = await fetch(`${API_BASE}/vendors/${id}/approve`, { method: 'POST', headers: authHeaders() });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Vendor approved successfully.');
                loadVendors();
            } else {
                showToast(json.message || 'Failed to approve vendor.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    async function suspendVendor(id) {
        if (!confirm('Suspend this vendor? They will lose store access.')) return;
        try {
            const res = await fetch(`${API_BASE}/vendors/${id}/suspend`, { method: 'POST', headers: authHeaders() });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Vendor suspended successfully.');
                loadVendors();
            } else {
                showToast(json.message || 'Failed to suspend vendor.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    function openRejectModal(id) {
        rejectTargetId = id;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectError').style.display = 'none';
        document.getElementById('rejectModal').classList.add('open');
    }

    function closeRejectModal() {
        rejectTargetId = null;
        document.getElementById('rejectModal').classList.remove('open');
    }

    document.getElementById('confirmRejectBtn').addEventListener('click', async () => {
        const reason = document.getElementById('rejectReason').value.trim();
        if (!reason) {
            document.getElementById('rejectError').style.display = 'block';
            return;
        }

        try {
            const res = await fetch(`${API_BASE}/vendors/${rejectTargetId}/reject`, {
                method: 'POST',
                headers: authHeaders(),
                body: JSON.stringify({ rejection_reason: reason }),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Vendor rejected successfully.');
                closeRejectModal();
                loadVendors();
            } else {
                showToast(json.message || 'Failed to reject vendor.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Vendor Detail Panel ----------
    async function openDetailPanel(id) {
        document.getElementById('detailOverlay').classList.add('open');
        document.getElementById('detailStoreName').textContent = 'Vendor Details';
        document.getElementById('detailContent').innerHTML = '<div class="detail-loading">Loading vendor details...</div>';

        try {
            const res = await fetch(`${API_BASE}/vendors/${id}`, { headers: authHeaders() });
            const json = await res.json();

            if (!json.success) {
                document.getElementById('detailContent').innerHTML = `<div class="detail-loading">${json.message || 'Failed to load vendor.'}</div>`;
                return;
            }

            renderDetailPanel(json.data);
        } catch (e) {
            document.getElementById('detailContent').innerHTML = '<div class="detail-loading">Something went wrong.</div>';
        }
    }

    function closeDetailPanel() {
        document.getElementById('detailOverlay').classList.remove('open');
    }

    function renderDetailPanel(vendor) {
        const u = vendor.user || {};
        const address = (vendor.addresses && vendor.addresses[0]) || null;
        const documents = vendor.documents || [];

        document.getElementById('detailStoreName').textContent = vendor.store_name || 'Vendor Details';

        let rejectionSection = '';
        if (vendor.status === 'rejected' && vendor.rejection_reason) {
            rejectionSection = `
                <div class="detail-section">
                    <h4>Rejection Reason</h4>
                    <div class="rejection-box">${vendor.rejection_reason}</div>
                </div>`;
        }

        let approvalSection = '';
        if (vendor.status === 'approved' && vendor.approved_at) {
            approvalSection = `
                <div class="detail-section">
                    <h4>Approval Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="label">Approved On</div>
                            <div class="value">${formatDate(vendor.approved_at)}</div>
                        </div>
                    </div>
                </div>`;
        }

        let addressSection = '';
        if (address) {
            addressSection = `
                <div class="detail-section">
                    <h4>Business Address</h4>
                    <div class="detail-grid">
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <div class="label">Address</div>
                            <div class="value">${[address.address_line_1, address.address_line_2, address.landmark].filter(Boolean).join(', ')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">City</div>
                            <div class="value">${address.city || '—'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">State</div>
                            <div class="value">${address.state || '—'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Postal Code</div>
                            <div class="value">${address.postal_code || '—'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="label">Country</div>
                            <div class="value">${address.country || '—'}</div>
                        </div>
                    </div>
                </div>`;
        }

        let documentsSection = '';
        if (documents.length) {
            documentsSection = `
                <div class="detail-section">
                    <h4>Documents</h4>
                    <div class="doc-list">
                        ${documents.map(doc => `
                            <div class="doc-item">
                                <span class="doc-type">${doc.document_type.replace(/_/g, ' ')}</span>
                                <a href="${STORAGE_BASE}/${doc.file_path}" target="_blank" class="btn btn-outline btn-sm">View File</a>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
        }

        document.getElementById('detailContent').innerHTML = `
            <div class="detail-section">
                <h4>Store / Business Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Store Name</div>
                        <div class="value">${vendor.store_name || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Business Name</div>
                        <div class="value">${vendor.business_name || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Status</div>
                        <div class="value">${statusBadge(vendor.status)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Joined</div>
                        <div class="value">${formatDate(vendor.created_at)}</div>
                    </div>
                    ${vendor.submitted_at ? `<div class="detail-item"><div class="label">Latest Submission</div><div class="value">${formatDate(vendor.submitted_at)}</div></div>` : ''}
                </div>
            </div>

            <div class="detail-section">
                <h4>Owner Details</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Name</div>
                        <div class="value">${vendorDisplayName(vendor)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Account Status</div>
                        <div class="value">${u.status || '—'}</div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4>Contact Details</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Email</div>
                        <div class="value">${u.email || vendor.support_email || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Phone</div>
                        <div class="value">${u.phone || vendor.support_phone || '—'}</div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4>GST / PAN</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">GST Number</div>
                        <div class="value">${vendor.gst_number || 'Not provided'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">PAN Number</div>
                        <div class="value">${vendor.pan_number || 'Not provided'}</div>
                    </div>
                </div>
            </div>

            ${addressSection}
            ${approvalSection}
            ${rejectionSection}
            ${documentsSection}
        `;
    }

    // Filters
    let searchDebounce = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentPage = 1;
            applyFiltersAndRender();
        }, 350);
    });

    document.getElementById('statusFilter').addEventListener('change', () => {
        currentPage = 1;
        applyFiltersAndRender();
    });

    document.getElementById('dateFilter').addEventListener('change', () => {
        currentPage = 1;
        applyFiltersAndRender();
    });

    // Initial load
    loadVendors();
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Attributes')

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
    .header-right { display: flex; align-items: center; gap: 16px; }

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
        font-size: 12px; letter-spacing: 0.6px; text-transform: uppercase;
        color: var(--text-muted); margin-bottom: 4px;
    }
    .total-count-box .count-value {
        font-family: 'Fraunces', serif; font-size: 28px; font-weight: 700; color: var(--terracotta-dark);
    }

    .toolbar {
        display: flex; gap: 14px; margin-bottom: 22px;
        flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
    }
    .toolbar-filters { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; }

    .search-box { position: relative; flex: 1; min-width: 240px; max-width: 380px; }
    .search-box svg {
        position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
        width: 17px; height: 17px; color: var(--text-muted);
    }
    .search-box input { width: 100%; padding: 13px 16px 13px 40px; font-size: 14px; border-radius: 11px; }

    .filter-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; display: block; }
    .filter-group { display: flex; flex-direction: column; }
    .status-filter, .sort-filter { min-width: 160px; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 780px; }

    thead th {
        text-align: left; font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase;
        color: var(--text-muted); padding: 17px 20px; border-bottom: 1px solid var(--border-color);
        white-space: nowrap; background: var(--bg-card-hover);
    }
    thead tr th:first-child { border-radius: var(--radius) 0 0 0; }
    thead tr th:last-child { border-radius: 0 var(--radius) 0 0; }

    tbody td {
        padding: 15px 20px; border-bottom: 1px solid var(--border-color);
        font-size: 14px; vertical-align: middle;
    }
    tbody tr:hover { background: var(--bg-card-hover); }
    tbody tr:last-child td { border-bottom: none; }

    .attr-name { font-weight: 600; color: var(--text-primary); }
    .attr-slug { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }

    .values-count-btn {
        background: var(--terracotta-soft); color: var(--terracotta-dark);
        border: 1px solid var(--border-color); border-radius: 8px;
        padding: 7px 14px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
        white-space: nowrap;
    }
    .values-count-btn:hover { background: var(--terracotta); color: #fff; }

    .badge.status-active { background: var(--success-soft); color: var(--success); }
    .badge.status-inactive { background: var(--neutral-soft); color: var(--neutral); }

    .actions-cell { display: flex; gap: 7px; flex-wrap: wrap; }

    .empty-state, .loading-state { padding: 70px 20px; text-align: center; color: var(--text-muted); font-size: 14px; }

    .table-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 17px 20px; font-size: 13px; color: var(--text-secondary); flex-wrap: wrap; gap: 10px;
    }
    .pagination { display: flex; gap: 6px; }
    .pagination button {
        min-width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-color-strong);
        background: #fff; color: var(--text-secondary); cursor: pointer; font-size: 13px; font-family: inherit;
    }
    .pagination button:hover:not(:disabled) { background: var(--bg-card-hover); }
    .pagination button.active { background: var(--terracotta); color: #fff; border-color: var(--terracotta); }
    .pagination button:disabled { opacity: 0.4; cursor: not-allowed; }

    .modal-overlay {
        position: fixed; inset: 0; background: rgba(60, 46, 33, 0.35);
        display: none; align-items: center; justify-content: center; z-index: 200; padding: 20px; overflow-y: auto;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fffdf9; border: 1px solid var(--border-color); border-radius: var(--radius);
        width: 100%; max-width: 500px; padding: 28px; box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18); margin: auto;
    }
    .modal-box.modal-wide { max-width: 720px; }
    .modal-box h3 { font-size: 19px; margin-bottom: 6px; }
    .modal-box p.modal-sub { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 20px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-group label .required { color: var(--danger); }
    .form-group input, .form-group textarea { width: 100%; }
    .form-error { color: var(--danger); font-size: 12px; margin-top: 5px; display: none; }

    .toggle-row {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg-card-hover); border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 16px;
    }
    .switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .switch-slider { position: absolute; cursor: pointer; inset: 0; background: var(--neutral); border-radius: 20px; transition: 0.2s; }
    .switch-slider:before {
        content: ""; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px;
        background: #fff; border-radius: 50%; transition: 0.2s;
    }
    .switch input:checked + .switch-slider { background: var(--success); }
    .switch input:checked + .switch-slider:before { transform: translateX(18px); }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; margin-bottom: 8px; }
    .detail-item .label { font-size: 12px; color: var(--text-muted); margin-bottom: 3px; }
    .detail-item .value { font-size: 14px; color: var(--text-primary); font-weight: 500; }
    .detail-item.full { grid-column: 1 / -1; }

    /* Manage Values modal */
    .values-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 4px; flex-wrap: wrap; gap: 10px;
    }
    .values-modal-header .attr-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .values-modal-header .attr-title { font-size: 19px; font-weight: 700; color: var(--text-primary); }

    .values-toolbar {
        display: flex; gap: 12px; margin: 18px 0 14px; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
    }
    .values-toolbar .search-box { max-width: none; flex: 1; min-width: 180px; }
    .values-toolbar .filter-group { min-width: 130px; }

    .values-table-wrap { max-height: 340px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 10px; }
    .values-table-wrap table { min-width: 0; }
    .values-table-wrap thead th { position: sticky; top: 0; z-index: 1; }

    .toast {
        position: fixed; bottom: 24px; right: 24px; background: #fff; border: 1px solid var(--border-color-strong);
        border-left: 4px solid var(--success); padding: 14px 20px; border-radius: 10px; font-size: 13.5px;
        z-index: 300; box-shadow: 0 8px 24px rgba(60, 46, 33, 0.18);
        transform: translateY(20px); opacity: 0; transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.error { border-left-color: var(--danger); }

    @media (max-width: 700px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h1>Attributes</h1>
        <p>Manage product attributes (e.g. Color, Material) and their values.</p>
    </div>
    <div class="header-right">
        <div class="total-count-box">
            <div class="count-label">Total Attributes</div>
            <div class="count-value" id="totalAttrCount">—</div>
        </div>
        <button class="btn btn-terracotta" onclick="openFormModal()">+ Add Attribute</button>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-filters">
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Search by attribute name...">
        </div>

        <div class="filter-group">
            <label class="filter-label">Status</label>
            <select id="statusFilter" class="status-filter">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Sort By</label>
            <select id="sortFilter" class="sort-filter">
                <option value="name_asc">Name (A–Z)</option>
                <option value="name_desc">Name (Z–A)</option>
                <option value="created_desc">Newest First</option>
                <option value="created_asc">Oldest First</option>
            </select>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Values</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="attrsTableBody">
                <tr><td colspan="5" class="loading-state">Loading attributes...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span id="tableSummary"></span>
        <div class="pagination" id="paginationControls"></div>
    </div>
</div>

<!-- Add/Edit Attribute Modal -->
<div class="modal-overlay" id="formModal">
    <div class="modal-box">
        <h3 id="formModalTitle">Add Attribute</h3>
        <p class="modal-sub">Fill in the details below.</p>

        <form id="attrForm">
            <div class="form-group">
                <label>Attribute Name <span class="required">*</span></label>
                <input type="text" id="formName" placeholder="e.g. Color">
                <div class="form-error" id="formNameError">Attribute name is required.</div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <div class="toggle-row">
                    <span id="formStatusLabel" style="font-size:13.5px;">Active</span>
                    <label class="switch">
                        <input type="checkbox" id="formIsActive" checked>
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </div>
        </form>

        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeFormModal()">Cancel</button>
            <button class="btn btn-terracotta" id="saveAttrBtn">Save Attribute</button>
        </div>
    </div>
</div>

<!-- View Attribute Modal -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box" style="max-width: 460px;">
        <h3 id="viewModalTitle">Attribute Details</h3>
        <div id="viewModalContent" style="margin-top: 14px;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<!-- Delete Attribute Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width: 420px;">
        <h3>Delete Attribute</h3>
        <p class="modal-sub" id="deleteModalText">Are you sure you want to delete this attribute? This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<!-- Manage Values Modal -->
<div class="modal-overlay" id="valuesModal">
    <div class="modal-box modal-wide">
        <div class="values-modal-header">
            <div>
                <div class="attr-label">Manage Values for</div>
                <div class="attr-title" id="valuesAttrName">—</div>
            </div>
            <button class="btn btn-terracotta btn-sm" onclick="openValueFormModal()">+ Add Value</button>
        </div>

        <div class="values-toolbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="valueSearchInput" placeholder="Search values...">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select id="valueStatusFilter">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="values-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Value</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="valuesTableBody">
                    <tr><td colspan="4" class="loading-state">Loading values...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeValuesModal()">Close</button>
        </div>
    </div>
</div>

<!-- Add/Edit Value Modal -->
<div class="modal-overlay" id="valueFormModal">
    <div class="modal-box">
        <h3 id="valueFormModalTitle">Add Value</h3>
        <p class="modal-sub">Fill in the details below.</p>

        <form id="valueForm">
            <div class="form-group">
                <label>Value <span class="required">*</span></label>
                <input type="text" id="valueFormValue" placeholder="e.g. Red">
                <div class="form-error" id="valueFormValueError">Value is required.</div>
            </div>

            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" id="valueFormSortOrder" placeholder="0" value="0">
            </div>

            <div class="form-group">
                <label>Status</label>
                <div class="toggle-row">
                    <span id="valueFormStatusLabel" style="font-size:13.5px;">Active</span>
                    <label class="switch">
                        <input type="checkbox" id="valueFormIsActive" checked>
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </div>
        </form>

        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeValueFormModal()">Cancel</button>
            <button class="btn btn-terracotta" id="saveValueBtn">Save Value</button>
        </div>
    </div>
</div>

<!-- Delete Value Confirm Modal -->
<div class="modal-overlay" id="deleteValueModal">
    <div class="modal-box" style="max-width: 420px;">
        <h3>Delete Value</h3>
        <p class="modal-sub" id="deleteValueModalText">Are you sure you want to delete this value? This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteValueModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteValueBtn">Delete</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

@endsection

@push('scripts')
<script>
    const API_BASE = "{{ url('/api/admin') }}";
    const token = localStorage.getItem('meeta_token');

    let allAttrs = [];
    let currentPage = 1;
    const perPage = 10;
    let editingAttrId = null;
    let deleteTargetId = null;

    // Values modal state
    let activeAttribute = null;      // the attribute currently open in the Manage Values modal
    let allValues = [];              // values for the active attribute
    let editingValueId = null;
    let deleteValueTargetId = null;

    function authHeaders(withJson = true) {
        const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
        if (withJson) headers['Content-Type'] = 'application/json';
        return headers;
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function isActiveStatus(item) {
        return item.status === 'active';
    }

    function statusBadge(item) {
        return isActiveStatus(item)
            ? `<span class="badge status-active">Active</span>`
            : `<span class="badge status-inactive">Inactive</span>`;
    }

    // =====================================================================
    // ATTRIBUTES
    // =====================================================================

    async function loadAttrs() {
        const tbody = document.getElementById('attrsTableBody');
        tbody.innerHTML = `<tr><td colspan="5" class="loading-state">Loading attributes...</td></tr>`;

        try {
            const res = await fetch(`${API_BASE}/attributes?per_page=1000`, { headers: authHeaders() });
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="5" class="empty-state">${json.message || 'Failed to load attributes.'}</td></tr>`;
                return;
            }

            allAttrs = json.data.data || json.data || [];
            document.getElementById('totalAttrCount').textContent = allAttrs.length;
            render();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="5" class="empty-state">Something went wrong. Please try again.</td></tr>`;
            console.error(e);
        }
    }

    function getFilteredSorted() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const sort = document.getElementById('sortFilter').value;

        let filtered = allAttrs.filter(a => {
            if (search && !a.name.toLowerCase().includes(search)) return false;
            if (status && a.status !== status) return false;
            return true;
        });

        filtered.sort((a, b) => {
            if (sort === 'name_asc') return a.name.localeCompare(b.name);
            if (sort === 'name_desc') return b.name.localeCompare(a.name);
            if (sort === 'created_desc') return new Date(b.created_at) - new Date(a.created_at);
            if (sort === 'created_asc') return new Date(a.created_at) - new Date(b.created_at);
            return 0;
        });

        return filtered;
    }

    function render() {
        const tbody = document.getElementById('attrsTableBody');
        const filtered = getFilteredSorted();

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (!pageItems.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="empty-state">No attributes match these filters.</td></tr>`;
        } else {
            tbody.innerHTML = pageItems.map(attr => `
                <tr>
                    <td><div class="attr-name">${attr.name}</div></td>
                    <td><div class="attr-slug">${attr.slug || '—'}</div></td>
                    <td>${statusBadge(attr)}</td>
                    <td>
                        <button class="values-count-btn" onclick="openValuesModal(${attr.id})">
                            Manage Values ${attr.values_count !== undefined ? `(${attr.values_count})` : ''}
                        </button>
                    </td>
                    <td>${actionButtons(attr)}</td>
                </tr>
            `).join('');
        }

        document.getElementById('tableSummary').textContent = filtered.length === 0
            ? 'No attributes to show'
            : `Showing ${start + 1}-${start + pageItems.length} of ${filtered.length} attributes`;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const wrap = document.getElementById('paginationControls');
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
        render();
    }

    function actionButtons(attr) {
        const active = isActiveStatus(attr);
        return `
            <div class="actions-cell">
                <button class="btn btn-outline btn-sm" onclick="openViewModal(${attr.id})">View</button>
                <button class="btn btn-outline btn-sm" onclick="openFormModal(${attr.id})">Edit</button>
                <button class="btn ${active ? 'btn-danger' : 'btn-success'} btn-sm" onclick="toggleStatus(${attr.id}, '${active ? 'inactive' : 'active'}')">
                    ${active ? 'Deactivate' : 'Activate'}
                </button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${attr.id})">Delete</button>
            </div>
        `;
    }

    async function toggleStatus(id, newStatus) {
        try {
            const res = await fetch(`${API_BASE}/attributes/${id}/status`, {
                method: 'PATCH',
                headers: authHeaders(),
                body: JSON.stringify({ status: newStatus }),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Status updated successfully.');
                await loadAttrs();
            } else {
                showToast(json.message || 'Failed to update status.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    function findAttrById(id) {
        return allAttrs.find(a => a.id === id);
    }

    // ---------- View modal ----------
    async function openViewModal(id) {
        const attr = findAttrById(id);
        if (!attr) return;

        document.getElementById('viewModalTitle').textContent = attr.name;
        document.getElementById('viewModalContent').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="label">Status</div>
                    <div class="value">${statusBadge(attr)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Created</div>
                    <div class="value">${formatDate(attr.created_at)}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Slug</div>
                    <div class="value">${attr.slug || '—'}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Values</div>
                    <div class="value" id="viewModalValues">Loading…</div>
                </div>
            </div>
        `;
        document.getElementById('viewModal').classList.add('open');

        // Fetch actual values for this attribute (list API doesn't reliably return a count).
        try {
            const res = await fetch(`${API_BASE}/attribute-values?attribute_id=${id}&per_page=1000`, { headers: authHeaders() });
            const json = await res.json();
            const valuesEl = document.getElementById('viewModalValues');
            if (!valuesEl) return; // modal closed before response arrived

            if (!json.success) {
                valuesEl.textContent = '—';
                return;
            }

            let values = json.data.data || json.data || [];
            values = values.filter(v => v.attribute_id === id);

            if (!values.length) {
                valuesEl.textContent = 'No values added yet.';
            } else {
                valuesEl.innerHTML = `${values.length} value${values.length > 1 ? 's' : ''}: ` +
                    values.map(v => v.value).join(', ');
            }
        } catch (e) {
            const valuesEl = document.getElementById('viewModalValues');
            if (valuesEl) valuesEl.textContent = '—';
        }
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('open');
    }

    // ---------- Add/Edit modal ----------
    function openFormModal(id = null) {
        editingAttrId = id;
        document.getElementById('attrForm').reset();
        document.getElementById('formNameError').style.display = 'none';
        document.getElementById('formIsActive').checked = true;
        document.getElementById('formStatusLabel').textContent = 'Active';

        if (id) {
            const attr = findAttrById(id);
            document.getElementById('formModalTitle').textContent = 'Edit Attribute';
            if (attr) {
                document.getElementById('formName').value = attr.name || '';
                const active = isActiveStatus(attr);
                document.getElementById('formIsActive').checked = active;
                document.getElementById('formStatusLabel').textContent = active ? 'Active' : 'Inactive';
            }
        } else {
            document.getElementById('formModalTitle').textContent = 'Add Attribute';
        }

        document.getElementById('formModal').classList.add('open');
    }

    function closeFormModal() {
        editingAttrId = null;
        document.getElementById('formModal').classList.remove('open');
    }

    document.getElementById('formIsActive').addEventListener('change', (e) => {
        document.getElementById('formStatusLabel').textContent = e.target.checked ? 'Active' : 'Inactive';
    });

    document.getElementById('saveAttrBtn').addEventListener('click', async () => {
        const name = document.getElementById('formName').value.trim();
        if (!name) {
            document.getElementById('formNameError').style.display = 'block';
            return;
        }
        document.getElementById('formNameError').style.display = 'none';

        const payload = {
            name: name,
            status: document.getElementById('formIsActive').checked ? 'active' : 'inactive',
        };

        const isEdit = !!editingAttrId;
        const url = isEdit ? `${API_BASE}/attributes/${editingAttrId}` : `${API_BASE}/attributes`;

        try {
            const res = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: authHeaders(),
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || (isEdit ? 'Attribute updated successfully.' : 'Attribute created successfully.'));
                closeFormModal();
                await loadAttrs();
            } else {
                // Covers backend's 422 "Attribute already exists." and validation errors.
                showToast(json.message || 'Failed to save attribute.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Delete modal ----------
    function openDeleteModal(id) {
        deleteTargetId = id;
        const attr = findAttrById(id);
        document.getElementById('deleteModalText').textContent = attr
            ? `Are you sure you want to delete "${attr.name}"? This cannot be undone.`
            : 'Are you sure you want to delete this attribute?';
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.remove('open');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        try {
            const res = await fetch(`${API_BASE}/attributes/${deleteTargetId}`, {
                method: 'DELETE',
                headers: authHeaders(),
            });
            const json = await res.json();

            if (json.success) {
                showToast(json.message || 'Attribute deleted successfully.');
                closeDeleteModal();
                await loadAttrs();
            } else {
                // Covers backend's 422 "Cannot delete attribute with values" and 404 "not found".
                showToast(json.message || 'Failed to delete attribute.', true);
                closeDeleteModal();
            }
        } catch (e) {
            showToast('Something went wrong.', true);
            closeDeleteModal();
        }
    });

    // ---------- Filters ----------
    let searchDebounce = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => { currentPage = 1; render(); }, 350);
    });
    document.getElementById('statusFilter').addEventListener('change', () => { currentPage = 1; render(); });
    document.getElementById('sortFilter').addEventListener('change', () => { currentPage = 1; render(); });

    // =====================================================================
    // ATTRIBUTE VALUES
    // =====================================================================

    async function openValuesModal(attributeId) {
        activeAttribute = findAttrById(attributeId);
        if (!activeAttribute) return;

        document.getElementById('valuesAttrName').textContent = activeAttribute.name;
        document.getElementById('valueSearchInput').value = '';
        document.getElementById('valueStatusFilter').value = '';
        document.getElementById('valuesModal').classList.add('open');

        await loadValues();
    }

    function closeValuesModal() {
        document.getElementById('valuesModal').classList.remove('open');
        activeAttribute = null;
        allValues = [];
    }

    async function loadValues() {
        if (!activeAttribute) return;
        const tbody = document.getElementById('valuesTableBody');
        tbody.innerHTML = `<tr><td colspan="4" class="loading-state">Loading values...</td></tr>`;

        try {
            const res = await fetch(`${API_BASE}/attribute-values?attribute_id=${activeAttribute.id}&per_page=1000`, { headers: authHeaders() });
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="4" class="empty-state">${json.message || 'Failed to load values.'}</td></tr>`;
                return;
            }

            let values = json.data.data || json.data || [];
            // Safety net in case the API returns values for all attributes.
            values = values.filter(v => v.attribute_id === activeAttribute.id);
            allValues = values;
            renderValues();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="4" class="empty-state">Something went wrong. Please try again.</td></tr>`;
            console.error(e);
        }
    }

    function getFilteredSortedValues() {
        const search = document.getElementById('valueSearchInput').value.trim().toLowerCase();
        const status = document.getElementById('valueStatusFilter').value;

        let filtered = allValues.filter(v => {
            if (search && !v.value.toLowerCase().includes(search)) return false;
            if (status && v.status !== status) return false;
            return true;
        });

        filtered.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        return filtered;
    }

    function renderValues() {
        const tbody = document.getElementById('valuesTableBody');
        const filtered = getFilteredSortedValues();

        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="empty-state">No values match these filters.</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(v => `
            <tr>
                <td><div class="attr-name">${v.value}</div></td>
                <td>${v.sort_order ?? 0}</td>
                <td>${statusBadge(v)}</td>
                <td>${valueActionButtons(v)}</td>
            </tr>
        `).join('');
    }

    function valueActionButtons(v) {
        const active = isActiveStatus(v);
        return `
            <div class="actions-cell">
                <button class="btn btn-outline btn-sm" onclick="openValueFormModal(${v.id})">Edit</button>
                <button class="btn ${active ? 'btn-danger' : 'btn-success'} btn-sm" onclick="toggleValueStatus(${v.id}, '${active ? 'inactive' : 'active'}')">
                    ${active ? 'Deactivate' : 'Activate'}
                </button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteValueModal(${v.id})">Delete</button>
            </div>
        `;
    }

    async function toggleValueStatus(id, newStatus) {
        try {
            const res = await fetch(`${API_BASE}/attribute-values/${id}/status`, {
                method: 'PATCH',
                headers: authHeaders(),
                body: JSON.stringify({ status: newStatus }),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Status updated successfully.');
                await loadValues();
            } else {
                showToast(json.message || 'Failed to update status.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    function findValueById(id) {
        return allValues.find(v => v.id === id);
    }

    // ---------- Add/Edit Value modal ----------
    function openValueFormModal(id = null) {
        editingValueId = id;
        document.getElementById('valueForm').reset();
        document.getElementById('valueFormValueError').style.display = 'none';
        document.getElementById('valueFormIsActive').checked = true;
        document.getElementById('valueFormStatusLabel').textContent = 'Active';
        document.getElementById('valueFormSortOrder').value = 0;

        if (id) {
            const val = findValueById(id);
            document.getElementById('valueFormModalTitle').textContent = 'Edit Value';
            if (val) {
                document.getElementById('valueFormValue').value = val.value || '';
                document.getElementById('valueFormSortOrder').value = val.sort_order ?? 0;
                const active = isActiveStatus(val);
                document.getElementById('valueFormIsActive').checked = active;
                document.getElementById('valueFormStatusLabel').textContent = active ? 'Active' : 'Inactive';
            }
        } else {
            document.getElementById('valueFormModalTitle').textContent = 'Add Value';
        }

        document.getElementById('valueFormModal').classList.add('open');
    }

    function closeValueFormModal() {
        editingValueId = null;
        document.getElementById('valueFormModal').classList.remove('open');
    }

    document.getElementById('valueFormIsActive').addEventListener('change', (e) => {
        document.getElementById('valueFormStatusLabel').textContent = e.target.checked ? 'Active' : 'Inactive';
    });

    document.getElementById('saveValueBtn').addEventListener('click', async () => {
        if (!activeAttribute) return;

        const value = document.getElementById('valueFormValue').value.trim();
        if (!value) {
            document.getElementById('valueFormValueError').style.display = 'block';
            return;
        }
        document.getElementById('valueFormValueError').style.display = 'none';

        const isEdit = !!editingValueId;
        const payload = {
            value: value,
            sort_order: parseInt(document.getElementById('valueFormSortOrder').value, 10) || 0,
            status: document.getElementById('valueFormIsActive').checked ? 'active' : 'inactive',
        };
        if (!isEdit) payload.attribute_id = activeAttribute.id;

        const url = isEdit ? `${API_BASE}/attribute-values/${editingValueId}` : `${API_BASE}/attribute-values`;

        try {
            const res = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: authHeaders(),
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || (isEdit ? 'Value updated successfully.' : 'Value created successfully.'));
                closeValueFormModal();
                await loadValues();
                await loadAttrs(); // refresh values_count on the attributes table
            } else {
                showToast(json.message || 'Failed to save value.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Delete Value modal ----------
    function openDeleteValueModal(id) {
        deleteValueTargetId = id;
        const val = findValueById(id);
        document.getElementById('deleteValueModalText').textContent = val
            ? `Are you sure you want to delete "${val.value}"? This cannot be undone.`
            : 'Are you sure you want to delete this value?';
        document.getElementById('deleteValueModal').classList.add('open');
    }

    function closeDeleteValueModal() {
        deleteValueTargetId = null;
        document.getElementById('deleteValueModal').classList.remove('open');
    }

    document.getElementById('confirmDeleteValueBtn').addEventListener('click', async () => {
        try {
            const res = await fetch(`${API_BASE}/attribute-values/${deleteValueTargetId}`, {
                method: 'DELETE',
                headers: authHeaders(),
            });
            const json = await res.json();

            if (json.success) {
                showToast(json.message || 'Value deleted successfully.');
                closeDeleteValueModal();
                await loadValues();
                await loadAttrs(); // refresh values_count on the attributes table
            } else {
                showToast(json.message || 'Failed to delete value.', true);
                closeDeleteValueModal();
            }
        } catch (e) {
            showToast('Something went wrong.', true);
            closeDeleteValueModal();
        }
    });

    // ---------- Value filters ----------
    let valueSearchDebounce = null;
    document.getElementById('valueSearchInput').addEventListener('input', () => {
        clearTimeout(valueSearchDebounce);
        valueSearchDebounce = setTimeout(() => renderValues(), 300);
    });
    document.getElementById('valueStatusFilter').addEventListener('change', () => renderValues());

    // Initial load
    loadAttrs();
</script>
@endpush
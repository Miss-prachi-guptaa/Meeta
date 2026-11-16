@extends('layouts.admin')

@section('title', 'Brands')

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
    table { width: 100%; border-collapse: collapse; min-width: 860px; }

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

    .brand-cell { display: flex; align-items: center; gap: 12px; }
    .brand-logo {
        width: 42px; height: 42px; border-radius: 10px;
        background: var(--terracotta-soft); color: var(--terracotta-dark);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px; flex-shrink: 0;
        object-fit: cover; border: 1px solid var(--border-color);
    }
    .brand-name { font-weight: 600; color: var(--text-primary); }
    .brand-desc { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; max-width: 300px; }

    .website-link { color: var(--terracotta-dark); font-size: 13.5px; font-weight: 500; }
    .website-link:hover { text-decoration: underline; }

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
    .modal-box h3 { font-size: 19px; margin-bottom: 6px; }
    .modal-box p.modal-sub { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 20px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-group label .required { color: var(--danger); }
    .form-group input, .form-group textarea { width: 100%; }
    .form-group textarea { min-height: 70px; resize: vertical; }
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

    .image-upload-box {
        border: 1.5px dashed var(--border-color-strong); border-radius: 10px; padding: 16px;
        display: flex; align-items: center; gap: 14px; background: var(--bg-card-hover);
    }
    .image-preview {
        width: 56px; height: 56px; border-radius: 8px; object-fit: cover;
        border: 1px solid var(--border-color); background: #fff; flex-shrink: 0;
    }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; margin-bottom: 8px; }
    .detail-item .label { font-size: 12px; color: var(--text-muted); margin-bottom: 3px; }
    .detail-item .value { font-size: 14px; color: var(--text-primary); font-weight: 500; }
    .detail-item.full { grid-column: 1 / -1; }

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
        <h1>Brands</h1>
        <p>Manage the artisan and product brands listed on MEETA.</p>
    </div>
    <div class="header-right">
        <div class="total-count-box">
            <div class="count-label">Total Brands</div>
            <div class="count-value" id="totalBrandCount">—</div>
        </div>
        <button class="btn btn-terracotta" onclick="openFormModal()">+ Add Brand</button>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-filters">
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Search by brand name...">
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
                    <th>Logo</th>
                    <th>Brand</th>
                    <th>Website</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="brandsTableBody">
                <tr><td colspan="6" class="loading-state">Loading brands...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span id="tableSummary"></span>
        <div class="pagination" id="paginationControls"></div>
    </div>
</div>

<!-- Add/Edit Brand Modal -->
<div class="modal-overlay" id="formModal">
    <div class="modal-box">
        <h3 id="formModalTitle">Add Brand</h3>
        <p class="modal-sub">Fill in the details below.</p>

        <form id="brandForm">
            <div class="form-group">
                <label>Brand Name <span class="required">*</span></label>
                <input type="text" id="formName" placeholder="e.g. Jhansi Handloom Co.">
                <div class="form-error" id="formNameError">Brand name is required.</div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea id="formDescription" placeholder="Short description of this brand..."></textarea>
            </div>

            <div class="form-group">
                <label>Website</label>
                <input type="url" id="formWebsite" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label>Logo</label>
                <div class="image-upload-box">
                    <img id="formLogoPreview" class="image-preview" style="display:none;" src="">
                    <input type="file" id="formLogo" accept="image/*">
                </div>
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
            <button class="btn btn-terracotta" id="saveBrandBtn">Save Brand</button>
        </div>
    </div>
</div>

<!-- View Brand Modal -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box" style="max-width: 460px;">
        <h3 id="viewModalTitle">Brand Details</h3>
        <div id="viewModalContent" style="margin-top: 14px;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width: 420px;">
        <h3>Delete Brand</h3>
        <p class="modal-sub" id="deleteModalText">Are you sure you want to delete this brand? This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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

    let allBrands = [];
    let currentPage = 1;
    const perPage = 10;
    let editingBrandId = null;
    let deleteTargetId = null;

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

    function initials(name) {
        if (!name) return '?';
        return name.trim().split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase();
    }

    function isActiveStatus(brand) {
        return brand.status === 'active';
    }

    function statusBadge(brand) {
        return isActiveStatus(brand)
            ? `<span class="badge status-active">Active</span>`
            : `<span class="badge status-inactive">Inactive</span>`;
    }

    function brandLogo(brand) {
        return brand.logo
            ? `<img class="brand-logo" src="${STORAGE_BASE}/${brand.logo}" alt="">`
            : `<div class="brand-logo">${initials(brand.name)}</div>`;
    }

    // ---------- Load ----------
    async function loadBrands() {
        const tbody = document.getElementById('brandsTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="loading-state">Loading brands...</td></tr>`;

        try {
            const res = await fetch(`${API_BASE}/brands?per_page=1000`, { headers: authHeaders() });
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="6" class="empty-state">${json.message || 'Failed to load brands.'}</td></tr>`;
                return;
            }

            allBrands = json.data.data || json.data || [];
            document.getElementById('totalBrandCount').textContent = allBrands.length;
            render();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">Something went wrong. Please try again.</td></tr>`;
            console.error(e);
        }
    }

    function getFilteredSorted() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const sort = document.getElementById('sortFilter').value;

        let filtered = allBrands.filter(b => {
            if (search && !b.name.toLowerCase().includes(search)) return false;
            if (status && b.status !== status) return false;
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
        const tbody = document.getElementById('brandsTableBody');
        const filtered = getFilteredSorted();

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (!pageItems.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No brands match these filters.</td></tr>`;
        } else {
            tbody.innerHTML = pageItems.map(brand => `
                <tr>
                    <td>${brandLogo(brand)}</td>
                    <td>
                        <div class="brand-name">${brand.name}</div>
                        ${brand.description ? `<div class="brand-desc">${brand.description}</div>` : ''}
                    </td>
                    <td>${brand.website ? `<a href="${brand.website}" target="_blank" class="website-link">${brand.website.replace(/^https?:\/\//, '')}</a>` : '—'}</td>
                    <td>${statusBadge(brand)}</td>
                    <td>${formatDate(brand.created_at)}</td>
                    <td>${actionButtons(brand)}</td>
                </tr>
            `).join('');
        }

        document.getElementById('tableSummary').textContent = filtered.length === 0
            ? 'No brands to show'
            : `Showing ${start + 1}-${start + pageItems.length} of ${filtered.length} brands`;

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

    function actionButtons(brand) {
        const active = isActiveStatus(brand);
        return `
            <div class="actions-cell">
                <button class="btn btn-outline btn-sm" onclick="openViewModal(${brand.id})">View</button>
                <button class="btn btn-outline btn-sm" onclick="openFormModal(${brand.id})">Edit</button>
                <button class="btn ${active ? 'btn-danger' : 'btn-success'} btn-sm" onclick="toggleStatus(${brand.id}, '${active ? 'inactive' : 'active'}')">
                    ${active ? 'Deactivate' : 'Activate'}
                </button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${brand.id})">Delete</button>
            </div>
        `;
    }

    async function toggleStatus(id, newStatus) {
        try {
            const res = await fetch(`${API_BASE}/brands/${id}/status`, {
                method: 'PATCH',
                headers: authHeaders(),
                body: JSON.stringify({ status: newStatus }),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Status updated successfully.');
                await loadBrands();
            } else {
                showToast(json.message || 'Failed to update status.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    function findBrandById(id) {
        return allBrands.find(b => b.id === id);
    }

    // ---------- View modal ----------
    function openViewModal(id) {
        const brand = findBrandById(id);
        if (!brand) return;

        document.getElementById('viewModalTitle').textContent = brand.name;
        document.getElementById('viewModalContent').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="label">Status</div>
                    <div class="value">${statusBadge(brand)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Created</div>
                    <div class="value">${formatDate(brand.created_at)}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Website</div>
                    <div class="value">${brand.website ? `<a href="${brand.website}" target="_blank" class="website-link">${brand.website}</a>` : 'Not provided'}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Description</div>
                    <div class="value">${brand.description || 'No description added.'}</div>
                </div>
            </div>
        `;
        document.getElementById('viewModal').classList.add('open');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('open');
    }

    // ---------- Add/Edit modal ----------
    function openFormModal(id = null) {
        editingBrandId = id;
        document.getElementById('brandForm').reset();
        document.getElementById('formNameError').style.display = 'none';
        document.getElementById('formLogoPreview').style.display = 'none';
        document.getElementById('formIsActive').checked = true;
        document.getElementById('formStatusLabel').textContent = 'Active';

        if (id) {
            const brand = findBrandById(id);
            document.getElementById('formModalTitle').textContent = 'Edit Brand';
            if (brand) {
                document.getElementById('formName').value = brand.name || '';
                document.getElementById('formDescription').value = brand.description || '';
                document.getElementById('formWebsite').value = brand.website || '';
                const active = isActiveStatus(brand);
                document.getElementById('formIsActive').checked = active;
                document.getElementById('formStatusLabel').textContent = active ? 'Active' : 'Inactive';
                if (brand.logo) {
                    const preview = document.getElementById('formLogoPreview');
                    preview.src = `${STORAGE_BASE}/${brand.logo}`;
                    preview.style.display = 'block';
                }
            }
        } else {
            document.getElementById('formModalTitle').textContent = 'Add Brand';
        }

        document.getElementById('formModal').classList.add('open');
    }

    function closeFormModal() {
        editingBrandId = null;
        document.getElementById('formModal').classList.remove('open');
    }

    document.getElementById('formIsActive').addEventListener('change', (e) => {
        document.getElementById('formStatusLabel').textContent = e.target.checked ? 'Active' : 'Inactive';
    });

    document.getElementById('formLogo').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const preview = document.getElementById('formLogoPreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });

    document.getElementById('saveBrandBtn').addEventListener('click', async () => {
        const name = document.getElementById('formName').value.trim();
        if (!name) {
            document.getElementById('formNameError').style.display = 'block';
            return;
        }
        document.getElementById('formNameError').style.display = 'none';

        const formData = new FormData();
        formData.append('name', name);
        formData.append('description', document.getElementById('formDescription').value.trim());
        formData.append('website', document.getElementById('formWebsite').value.trim());
        formData.append('status', document.getElementById('formIsActive').checked ? 'active' : 'inactive');

        const logoFile = document.getElementById('formLogo').files[0];
        if (logoFile) formData.append('logo', logoFile);

        const isEdit = !!editingBrandId;
        // Multipart + PUT doesn't work reliably in PHP, so Laravel's
        // method-spoofing convention is used for edits (route stays PUT).
        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `${API_BASE}/brands/${editingBrandId}` : `${API_BASE}/brands`;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: authHeaders(false),
                body: formData,
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || (isEdit ? 'Brand updated successfully.' : 'Brand created successfully.'));
                closeFormModal();
                await loadBrands();
            } else {
                showToast(json.message || 'Failed to save brand.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Delete modal ----------
    function openDeleteModal(id) {
        deleteTargetId = id;
        const brand = findBrandById(id);
        document.getElementById('deleteModalText').textContent = brand
            ? `Are you sure you want to delete "${brand.name}"? This cannot be undone.`
            : 'Are you sure you want to delete this brand?';
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.remove('open');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        try {
            const res = await fetch(`${API_BASE}/brands/${deleteTargetId}`, {
                method: 'DELETE',
                headers: authHeaders(),
            });
            const json = await res.json();

            if (json.success) {
                showToast(json.message || 'Brand deleted successfully.');
                closeDeleteModal();
                await loadBrands();
            } else {
                // Covers backend's 422 "Cannot delete brand with products" and similar guard messages.
                showToast(json.message || 'Failed to delete brand.', true);
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

    // Initial load
    loadBrands();
</script>
@endpush
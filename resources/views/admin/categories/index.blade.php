@extends('layouts.admin')

@section('title', 'Categories')

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
        align-items: flex-end;
        justify-content: space-between;
    }

    .toolbar-filters { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 240px;
        max-width: 380px;
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

    .search-box input { width: 100%; padding: 13px 16px 13px 40px; font-size: 14px; border-radius: 11px; }

    .filter-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; display: block; }
    .filter-group { display: flex; flex-direction: column; }
    .status-filter, .parent-filter { min-width: 170px; }

    .view-toggle {
        display: flex;
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color-strong);
        border-radius: 10px;
        padding: 3px;
        gap: 3px;
    }

    .view-toggle button {
        border: none;
        background: transparent;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        font-family: inherit;
    }

    .view-toggle button.active { background: var(--terracotta); color: #fff; }

    /* Table */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 880px; }

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
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        vertical-align: middle;
    }
    tbody tr:hover { background: var(--bg-card-hover); }
    tbody tr:last-child td { border-bottom: none; }

    .category-cell { display: flex; align-items: center; gap: 12px; }

    .category-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--terracotta-soft);
        color: var(--terracotta-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .category-name { font-weight: 600; color: var(--text-primary); }
    .category-desc { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; max-width: 320px; }

    .tree-toggle {
        border: none;
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color-strong);
        border-radius: 6px;
        width: 22px;
        height: 22px;
        font-size: 12px;
        cursor: pointer;
        flex-shrink: 0;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tree-spacer { width: 22px; flex-shrink: 0; }

    .badge.status-active { background: var(--success-soft); color: var(--success); }
    .badge.status-inactive { background: var(--neutral-soft); color: var(--neutral); }

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

    /* Modal (Add/Edit + Delete confirm) */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(60, 46, 33, 0.35);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 200;
        padding: 20px;
        overflow-y: auto;
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fffdf9;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        width: 100%;
        max-width: 520px;
        padding: 28px;
        box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18);
        margin: auto;
    }

    .modal-box h3 { font-size: 19px; margin-bottom: 6px; }
    .modal-box p.modal-sub { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 20px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }

    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .form-group label .required { color: var(--danger); }
    .form-group input, .form-group textarea, .form-group select { width: 100%; }
    .form-group textarea { min-height: 80px; resize: vertical; }
    .form-error { color: var(--danger); font-size: 12px; margin-top: 5px; display: none; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-card-hover);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 16px;
    }

    .switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .switch-slider {
        position: absolute; cursor: pointer; inset: 0;
        background: var(--neutral); border-radius: 20px;
        transition: 0.2s;
    }
    .switch-slider:before {
        content: ""; position: absolute; width: 18px; height: 18px;
        left: 3px; bottom: 3px; background: #fff; border-radius: 50%;
        transition: 0.2s;
    }
    .switch input:checked + .switch-slider { background: var(--success); }
    .switch input:checked + .switch-slider:before { transform: translateX(18px); }

    .image-upload-box {
        border: 1.5px dashed var(--border-color-strong);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--bg-card-hover);
    }

    .image-preview {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--border-color);
        background: #fff;
        flex-shrink: 0;
    }

    /* View detail modal */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 18px;
        margin-bottom: 8px;
    }
    .detail-item .label { font-size: 12px; color: var(--text-muted); margin-bottom: 3px; }
    .detail-item .value { font-size: 14px; color: var(--text-primary); font-weight: 500; }
    .detail-item.full { grid-column: 1 / -1; }

    .children-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .children-chip {
        background: var(--terracotta-soft);
        color: var(--terracotta-dark);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 11px;
        border-radius: 20px;
    }

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

    @media (max-width: 700px) {
        .form-row { grid-template-columns: 1fr; }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h1>Categories</h1>
        <p>Organize your marketplace catalogue into categories and subcategories.</p>
    </div>
    <div class="header-right">
        <div class="total-count-box">
            <div class="count-label">Total Categories</div>
            <div class="count-value" id="totalCategoryCount">—</div>
        </div>
        <button class="btn btn-terracotta" onclick="openFormModal()">+ Add Category</button>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-filters">
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Search by category name...">
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
            <label class="filter-label">Parent Category</label>
            <select id="parentFilter" class="parent-filter">
                <option value="">All</option>
                <option value="__top__">Top-level only</option>
            </select>
        </div>
    </div>

    <div class="view-toggle">
        <button id="listViewBtn" class="active" onclick="switchView('list')">List</button>
        <button id="treeViewBtn" onclick="switchView('tree')">Tree</button>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Parent</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTableBody">
                <tr><td colspan="6" class="loading-state">Loading categories...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer" id="tableFooter">
        <span id="tableSummary"></span>
        <div class="pagination" id="paginationControls"></div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal-overlay" id="formModal">
    <div class="modal-box">
        <h3 id="formModalTitle">Add Category</h3>
        <p class="modal-sub">Fill in the details below.</p>

        <form id="categoryForm">
            <div class="form-group">
                <label>Category Name <span class="required">*</span></label>
                <input type="text" id="formName" placeholder="e.g. Handmade Paintings">
                <div class="form-error" id="formNameError">Category name is required.</div>
            </div>

            <div class="form-group">
                <label>Parent Category</label>
                <select id="formParent">
                    <option value="">None (Top-level category)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea id="formDescription" placeholder="Short description of this category..."></textarea>
            </div>

            <div class="form-group">
                <label>Image</label>
                <div class="image-upload-box">
                    <img id="formImagePreview" class="image-preview" style="display:none;" src="">
                    <input type="file" id="formImage" accept="image/*">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" id="formSortOrder" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="toggle-row" style="padding: 10px 14px;">
                        <span id="formStatusLabel" style="font-size:13.5px;">Active</span>
                        <label class="switch">
                            <input type="checkbox" id="formIsActive" checked>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeFormModal()">Cancel</button>
            <button class="btn btn-terracotta" id="saveCategoryBtn">Save Category</button>
        </div>
    </div>
</div>

<!-- View Category Modal -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box" style="max-width: 480px;">
        <h3 id="viewModalTitle">Category Details</h3>
        <div id="viewModalContent" style="margin-top: 14px;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width: 420px;">
        <h3>Delete Category</h3>
        <p class="modal-sub" id="deleteModalText">Are you sure you want to delete this category? This cannot be undone.</p>
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

    let allCategories = [];   // flat list, from /categories
    let categoryTree = [];    // nested list, from /categories/tree
    let currentView = 'list';
    let currentPage = 1;
    const perPage = 10;
    let editingCategoryId = null;
    let deleteTargetId = null;
    let collapsedNodes = new Set();

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
        setTimeout(() => toast.classList.remove('show'), 3000);
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

    function resolveImageSrc(path) {
        if (!path) return '';
        if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
        return `${STORAGE_BASE}/${path}`;
    }

    function categoryImageError(image, name) {
        const fallback = document.createElement('div');
        fallback.className = 'category-icon';
        fallback.textContent = initials(name);
        image.replaceWith(fallback);
    }

    function categoryIcon(cat) {
        return cat.image
            ? `<img class="category-icon" src="${resolveImageSrc(cat.image)}" alt="${cat.name}" onerror="categoryImageError(this, this.alt)">`
            : `<div class="category-icon">${initials(cat.name)}</div>`;
    }

    function statusBadge(isActive) {
        return isActive
            ? `<span class="badge status-active">Active</span>`
            : `<span class="badge status-inactive">Inactive</span>`;
    }

    // ---------- Load data ----------
    async function loadAll() {
        await Promise.all([loadFlatCategories(), loadCategoryTree()]);
        populateParentFilter();
        render();
    }

    async function loadFlatCategories() {
        try {
            const res = await fetch(`${API_BASE}/categories?per_page=1000`, { headers: authHeaders() });
            const json = await res.json();
            if (json.success) {
                allCategories = (json.data.data || json.data || []);
                document.getElementById('totalCategoryCount').textContent = allCategories.length;
            } else {
                showToast(json.message || 'Failed to load categories.', true);
            }
        } catch (e) {
            console.error(e);
            showToast('Something went wrong loading categories.', true);
        }
    }

    async function loadCategoryTree() {
        try {
            const res = await fetch(`${API_BASE}/categories/tree`, { headers: authHeaders() });
            const json = await res.json();
            if (json.success) {
                categoryTree = json.data || [];
            }
        } catch (e) {
            console.error(e);
        }
    }

    function populateParentFilter() {
        const select = document.getElementById('parentFilter');
        const current = select.value;
        select.innerHTML = `<option value="">All</option><option value="__top__">Top-level only</option>`;
        allCategories
            .filter(c => !c.parent_id)
            .forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        select.value = current;
    }

    function render() {
        if (currentView === 'list') {
            document.getElementById('tableFooter').style.display = 'flex';
            renderListView();
        } else {
            document.getElementById('tableFooter').style.display = 'none';
            renderTreeView();
        }
    }

    function switchView(view) {
        currentView = view;
        document.getElementById('listViewBtn').classList.toggle('active', view === 'list');
        document.getElementById('treeViewBtn').classList.toggle('active', view === 'tree');
        render();
    }

    // ---------- List view ----------
    function getFilteredFlat() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const parent = document.getElementById('parentFilter').value;

        return allCategories.filter(c => {
            if (search && !c.name.toLowerCase().includes(search)) return false;
            if (status === 'active' && !c.is_active) return false;
            if (status === 'inactive' && c.is_active) return false;
            if (parent === '__top__' && c.parent_id) return false;
            if (parent && parent !== '__top__' && String(c.parent_id) !== parent) return false;
            return true;
        });
    }

    function renderListView() {
        const tbody = document.getElementById('categoriesTableBody');
        const filtered = getFilteredFlat();

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (!pageItems.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No categories match these filters.</td></tr>`;
        } else {
            tbody.innerHTML = pageItems.map(cat => `
                <tr>
                    <td>
                        <div class="category-cell">
                            ${categoryIcon(cat)}
                            <div>
                                <div class="category-name">${cat.name}</div>
                                ${cat.description ? `<div class="category-desc">${cat.description}</div>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${cat.parent_name || '—'}</td>
                    <td>${cat.sort_order ?? '—'}</td>
                    <td>${statusBadge(cat.is_active)}</td>
                    <td>${formatDate(cat.created_at)}</td>
                    <td>${actionButtons(cat)}</td>
                </tr>
            `).join('');
        }

        document.getElementById('tableSummary').textContent = filtered.length === 0
            ? 'No categories to show'
            : `Showing ${start + 1}-${start + pageItems.length} of ${filtered.length} categories`;

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
        renderListView();
    }

    // ---------- Tree view ----------
    function renderTreeView() {
        const tbody = document.getElementById('categoriesTableBody');

        if (!categoryTree.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No categories yet.</td></tr>`;
            return;
        }

        const rows = [];
        flattenTreeForRender(categoryTree, 0, [], rows);
        tbody.innerHTML = rows.join('');
    }

    function flattenTreeForRender(nodes, depth, ancestors, rows) {
        nodes.forEach(node => {
            const hidden = ancestors.some(a => collapsedNodes.has(a));
            const hasChildren = node.children && node.children.length > 0;
            const isCollapsed = collapsedNodes.has(node.id);

            const toggleHtml = hasChildren
                ? `<button class="tree-toggle" onclick="toggleNode(${node.id})">${isCollapsed ? '▸' : '▾'}</button>`
                : `<span class="tree-spacer"></span>`;

            rows.push(`
                <tr style="${hidden ? 'display:none;' : ''}" data-node-id="${node.id}">
                    <td>
                        <div class="category-cell" style="padding-left: ${depth * 24}px;">
                            ${toggleHtml}
                            ${categoryIcon(node)}
                            <div>
                                <div class="category-name">${node.name}</div>
                                ${node.description ? `<div class="category-desc">${node.description}</div>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${node.parent_name || '—'}</td>
                    <td>${node.sort_order ?? '—'}</td>
                    <td>${statusBadge(node.is_active)}</td>
                    <td>${formatDate(node.created_at)}</td>
                    <td>${actionButtons(node)}</td>
                </tr>
            `);

            if (hasChildren) {
                flattenTreeForRender(node.children, depth + 1, [...ancestors, node.id], rows);
            }
        });
    }

    function toggleNode(id) {
        if (collapsedNodes.has(id)) collapsedNodes.delete(id);
        else collapsedNodes.add(id);
        renderTreeView();
    }

    // ---------- Actions ----------
    function actionButtons(cat) {
        return `
            <div class="actions-cell">
                <button class="btn btn-outline btn-sm" onclick="openViewModal(${cat.id})">View</button>
                <button class="btn btn-outline btn-sm" onclick="openFormModal(${cat.id})">Edit</button>
                <button class="btn ${cat.is_active ? 'btn-danger' : 'btn-success'} btn-sm" onclick="toggleStatus(${cat.id}, ${cat.is_active ? 'false' : 'true'})">
                    ${cat.is_active ? 'Deactivate' : 'Activate'}
                </button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${cat.id})">Delete</button>
            </div>
        `;
    }

    async function toggleStatus(id, newStatus) {
        try {
            const res = await fetch(`${API_BASE}/categories/${id}/status`, {
                method: 'PATCH',
                headers: authHeaders(),
                body: JSON.stringify({ is_active: newStatus }),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Status updated successfully.');
                await loadAll();
            } else {
                showToast(json.message || 'Failed to update status.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    }

    // ---------- View modal ----------
    function findCategoryById(id) {
        return allCategories.find(c => c.id === id);
    }

    function openViewModal(id) {
        const cat = findCategoryById(id);
        if (!cat) return;

        document.getElementById('viewModalTitle').textContent = cat.name;

        const childrenNames = allCategories
            .filter(c => c.parent_id === cat.id)
            .map(c => `<span class="children-chip">${c.name}</span>`)
            .join('');

        document.getElementById('viewModalContent').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="label">Slug</div>
                    <div class="value">${cat.slug || '—'}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Parent</div>
                    <div class="value">${cat.parent_name || 'Top-level'}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Sort Order</div>
                    <div class="value">${cat.sort_order ?? '—'}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Status</div>
                    <div class="value">${statusBadge(cat.is_active)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Created</div>
                    <div class="value">${formatDate(cat.created_at)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Updated</div>
                    <div class="value">${formatDate(cat.updated_at)}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Description</div>
                    <div class="value">${cat.description || 'No description added.'}</div>
                </div>
                ${childrenNames ? `
                <div class="detail-item full">
                    <div class="label">Subcategories</div>
                    <div class="children-list">${childrenNames}</div>
                </div>` : ''}
            </div>
        `;

        document.getElementById('viewModal').classList.add('open');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('open');
    }

    // ---------- Add/Edit modal ----------
    function collectDescendantIds(categoryId) {
        const ids = new Set();
        const walk = (parentId) => {
            allCategories.filter(c => c.parent_id === parentId).forEach(c => {
                ids.add(c.id);
                walk(c.id);
            });
        };
        walk(categoryId);
        return ids;
    }

    function populateParentDropdown(excludeId = null) {
        const select = document.getElementById('formParent');
        const excludeIds = excludeId ? new Set([excludeId, ...collectDescendantIds(excludeId)]) : new Set();

        function buildOptions(nodes, depth) {
            let html = '';
            nodes.forEach(node => {
                if (!excludeIds.has(node.id)) {
                    html += `<option value="${node.id}">${'— '.repeat(depth)}${node.name}</option>`;
                }
                if (node.children && node.children.length) {
                    html += buildOptions(node.children, depth + 1);
                }
            });
            return html;
        }

        select.innerHTML = `<option value="">None (Top-level category)</option>` + buildOptions(categoryTree, 0);
    }

    function openFormModal(id = null) {
        editingCategoryId = id;
        document.getElementById('categoryForm').reset();
        document.getElementById('formNameError').style.display = 'none';
        document.getElementById('formImagePreview').style.display = 'none';
        document.getElementById('formIsActive').checked = true;
        document.getElementById('formStatusLabel').textContent = 'Active';

        populateParentDropdown(id);

        if (id) {
            const cat = findCategoryById(id);
            document.getElementById('formModalTitle').textContent = 'Edit Category';
            if (cat) {
                document.getElementById('formName').value = cat.name || '';
                document.getElementById('formParent').value = cat.parent_id || '';
                document.getElementById('formDescription').value = cat.description || '';
                document.getElementById('formSortOrder').value = cat.sort_order ?? 0;
                document.getElementById('formIsActive').checked = !!cat.is_active;
                document.getElementById('formStatusLabel').textContent = cat.is_active ? 'Active' : 'Inactive';
                if (cat.image) {
                    const preview = document.getElementById('formImagePreview');
                    preview.src = resolveImageSrc(cat.image);
                    preview.style.display = 'block';
                }
            }
        } else {
            document.getElementById('formModalTitle').textContent = 'Add Category';
        }

        document.getElementById('formModal').classList.add('open');
    }

    function closeFormModal() {
        editingCategoryId = null;
        document.getElementById('formModal').classList.remove('open');
    }

    document.getElementById('formIsActive').addEventListener('change', (e) => {
        document.getElementById('formStatusLabel').textContent = e.target.checked ? 'Active' : 'Inactive';
    });

    document.getElementById('formImage').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const preview = document.getElementById('formImagePreview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });

    document.getElementById('saveCategoryBtn').addEventListener('click', async () => {
        const name = document.getElementById('formName').value.trim();
        if (!name) {
            document.getElementById('formNameError').style.display = 'block';
            return;
        }
        document.getElementById('formNameError').style.display = 'none';

        const formData = new FormData();
        formData.append('name', name);
        formData.append('parent_id', document.getElementById('formParent').value || '');
        formData.append('description', document.getElementById('formDescription').value.trim());
        formData.append('sort_order', document.getElementById('formSortOrder').value || 0);
        formData.append('is_active', document.getElementById('formIsActive').checked ? 1 : 0);

        const imageFile = document.getElementById('formImage').files[0];
        if (imageFile) formData.append('image', imageFile);

        const isEdit = !!editingCategoryId;
        // Multipart requests can't use a real PUT reliably in PHP, so Laravel's
        // method-spoofing convention is used for edits (route stays PUT).
        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `${API_BASE}/categories/${editingCategoryId}` : `${API_BASE}/categories`;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: authHeaders(false),
                body: formData,
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || (isEdit ? 'Category updated successfully.' : 'Category created successfully.'));
                closeFormModal();
                await loadAll();
            } else {
                showToast(json.message || 'Failed to save category.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Delete modal ----------
    function openDeleteModal(id) {
        deleteTargetId = id;
        const cat = findCategoryById(id);
        document.getElementById('deleteModalText').textContent = cat
            ? `Are you sure you want to delete "${cat.name}"? This cannot be undone.`
            : 'Are you sure you want to delete this category?';
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.remove('open');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        try {
            const res = await fetch(`${API_BASE}/categories/${deleteTargetId}`, {
                method: 'DELETE',
                headers: authHeaders(),
            });
            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Category deleted successfully.');
                closeDeleteModal();
                await loadAll();
            } else {
                showToast(json.message || 'Failed to delete category.', true);
            }
        } catch (e) {
            showToast('Something went wrong.', true);
        }
    });

    // ---------- Filters ----------
    let searchDebounce = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => { currentPage = 1; renderListView(); }, 350);
    });
    document.getElementById('statusFilter').addEventListener('change', () => { currentPage = 1; renderListView(); });
    document.getElementById('parentFilter').addEventListener('change', () => { currentPage = 1; renderListView(); });

    // Initial load
    loadAll();
</script>
@endpush

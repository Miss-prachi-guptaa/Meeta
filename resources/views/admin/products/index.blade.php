@extends('layouts.admin')

@section('title', 'Product Approvals')

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
        font-size: 30px;
        font-weight: 700;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 14.5px;
        margin-top: 5px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

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

    .toolbar {
        display: flex;
        gap: 14px;
        margin-bottom: 22px;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
    }

    .toolbar-filters {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

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

    .search-box input {
        width: 100%;
        padding: 13px 16px 13px 40px;
        font-size: 14px;
        border-radius: 11px;
    }

    .filter-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 4px;
        display: block;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .status-filter,
    .sort-filter {
        min-width: 170px;
    }

    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

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

    thead tr th:first-child {
        border-radius: var(--radius) 0 0 0;
    }

    thead tr th:last-child {
        border-radius: 0 var(--radius) 0 0;
    }

    tbody td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: var(--bg-card-hover);
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-thumb {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--terracotta-soft);
        color: var(--terracotta-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .product-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .product-slug {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .badge.status-pending {
        background: #fbead1;
        color: #9a6a15;
    }

    .badge.status-approved {
        background: var(--success-soft);
        color: var(--success);
    }

    .badge.status-active {
        background: var(--success-soft);
        color: var(--success);
    }

    .badge.status-rejected {
        background: #fadada;
        color: var(--danger);
    }

    .badge.status-inactive {
        background: var(--neutral-soft);
        color: var(--neutral);
    }

    .actions-cell {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .empty-state,
    .loading-state {
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

    .pagination {
        display: flex;
        gap: 6px;
    }

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

    .pagination button:hover:not(:disabled) {
        background: var(--bg-card-hover);
    }

    .pagination button.active {
        background: var(--terracotta);
        color: #fff;
        border-color: var(--terracotta);
    }

    .pagination button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

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

    .modal-overlay.open {
        display: flex;
    }

    .modal-box {
        background: #fffdf9;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        width: 100%;
        max-width: 500px;
        padding: 28px;
        box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18);
        margin: auto;
    }

    .modal-box.modal-wide {
        max-width: 780px;
    }

    .modal-box h3 {
        font-size: 19px;
        margin-bottom: 6px;
    }

    .modal-box p.modal-sub {
        font-size: 13.5px;
        color: var(--text-secondary);
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .form-group label .required {
        color: var(--danger);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
    }

    .form-group textarea {
        min-height: 90px;
        resize: vertical;
    }

    .form-error {
        color: var(--danger);
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }

    /* View modal */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 18px;
        margin-bottom: 8px;
    }

    .detail-item .label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 3px;
    }

    .detail-item .value {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .detail-item.full {
        grid-column: 1 / -1;
    }

    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--terracotta-dark);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 22px 0 10px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .section-title:first-child {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    .image-gallery {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .image-gallery img {
        width: 84px;
        height: 84px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--bg-card-hover);
    }

    .desc-block {
        font-size: 13.5px;
        color: var(--text-secondary);
        line-height: 1.55;
        white-space: pre-line;
    }

    .kv-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .kv-row {
        display: flex;
        gap: 10px;
        font-size: 13px;
    }

    .kv-row .k {
        min-width: 140px;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .kv-row .v {
        color: var(--text-primary);
        font-weight: 500;
        word-break: break-word;
    }

    .variant-card {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 14px 16px;
        background: var(--bg-card-hover);
        margin-bottom: 12px;
    }

    .variant-card:last-child {
        margin-bottom: 0;
    }

    .variant-card .variant-heading {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .attr-chip {
        display: inline-block;
        background: var(--terracotta-soft);
        color: var(--terracotta-dark);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        margin: 2px 4px 2px 0;
    }

    .rejection-box {
        background: #fadada;
        border: 1px solid #f0b8b8;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13.5px;
        color: #7a2222;
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

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.error {
        border-left-color: var(--danger);
    }

    @media (max-width: 700px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h1>Product Approvals</h1>
        <p>Review products submitted by vendors and approve or reject them before they go live.</p>
    </div>
    <div class="header-right">
        <div class="total-count-box">
            <div class="count-label">Pending Products</div>
            <div class="count-value" id="totalPendingCount">—</div>
        </div>
    </div>
</div>

<div class="toolbar">
    <div class="toolbar-filters">
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" id="searchInput" placeholder="Search by product name...">
        </div>

        <div class="filter-group">
            <label class="filter-label">Category</label>
            <select id="categoryFilter" class="status-filter">
                <option value="">All Categories</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Vendor</label>
            <select id="vendorFilter" class="status-filter">
                <option value="">All Vendors</option>
            </select>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Vendor</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <tr>
                    <td colspan="7" class="loading-state">Loading products...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span id="tableSummary"></span>
        <div class="pagination" id="paginationControls"></div>
    </div>
</div>

<!-- View Product Modal -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box modal-wide">
        <h3 id="viewModalTitle">Product Details</h3>
        <p class="modal-sub" id="viewModalSub"></p>
        <div id="viewModalContent">
            <div class="loading-state">Loading product details...</div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
            <button class="btn btn-danger" id="viewRejectBtn" style="display:none;">Reject</button>
            <button class="btn btn-terracotta" id="viewApproveBtn" style="display:none;">Approve</button>
        </div>
    </div>
</div>

<!-- Approve Confirm Modal -->
<div class="modal-overlay" id="approveModal">
    <div class="modal-box" style="max-width: 420px;">
        <h3>Approve Product</h3>
        <p class="modal-sub" id="approveModalText">Approve this product?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeApproveModal()">Cancel</button>
            <button class="btn btn-terracotta" id="confirmApproveBtn">Approve</button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal-box" style="max-width: 460px;">
        <h3>Reject Product</h3>
        <p class="modal-sub" id="rejectModalText">Please provide a reason for rejecting this product.</p>

        <div class="form-group">
            <label>Rejection Reason <span class="required">*</span></label>
            <textarea id="rejectReasonInput" placeholder="e.g. Images are blurry."></textarea>
            <div class="form-error" id="rejectReasonError">Rejection reason is required.</div>
        </div>

        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeRejectModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmRejectBtn">Reject</button>
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

    let allProducts = [];
    let currentPage = 1;
    const perPage = 10;

    let viewTargetId = null; // product currently open in View modal
    let approveTargetId = null;
    let rejectTargetId = null;

    function authHeaders(withJson = true) {
        const headers = {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        };
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
        return d.toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function initials(name) {
        if (!name) return '?';
        return name.trim().split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase();
    }

    function statusBadge(product) {
        const status = (product.status || '').toLowerCase();
        const label = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown';
        const cls = ['pending', 'approved', 'rejected', 'active', 'inactive'].includes(status) ? status : 'pending';
        return `<span class="badge status-${cls}">${label}</span>`;
    }

    function firstImage(product) {
        if (!product.images || !product.images.length) return null;
        const img = product.images[0];
        if (typeof img === 'string') return img;
        return img.image_url || img.url || img.path || img.image || null;
    }

    function resolveImageSrc(path) {
        if (!path) return null;
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        // Public catalogue assets already contain an absolute site path.
        if (path.startsWith('/')) return path;
        return `${STORAGE_BASE}/${path}`;
    }

    function productThumb(product) {
        const path = firstImage(product);
        const src = resolveImageSrc(path);
        return src ?
            `<img class="product-thumb" src="${src}" alt="">` :
            `<div class="product-thumb">${initials(product.name)}</div>`;
    }

    // =====================================================================
    // LOAD + RENDER
    // =====================================================================

    async function loadProducts() {
        const tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="loading-state">Loading products...</td></tr>`;

        try {
            const res = await fetch(`${API_BASE}/products/pending`, {
                headers: authHeaders()
            });
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="7" class="empty-state">${json.message || 'Failed to load products.'}</td></tr>`;
                return;
            }

            allProducts = json.data.data || json.data || [];
            document.getElementById('totalPendingCount').textContent = allProducts.length;
            populateFilterOptions();
            render();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state">Something went wrong. Please try again.</td></tr>`;
            console.error(e);
        }
    }

    function populateFilterOptions() {
        const categorySel = document.getElementById('categoryFilter');
        const vendorSel = document.getElementById('vendorFilter');
        const prevCategory = categorySel.value;
        const prevVendor = vendorSel.value;

        const categories = new Map();
        const vendors = new Map();
        allProducts.forEach(p => {
            if (p.category && p.category.id != null) categories.set(p.category.id, p.category.name);
            if (p.vendor && p.vendor.id != null) vendors.set(p.vendor.id, p.vendor.business_name);
        });

        categorySel.innerHTML = '<option value="">All Categories</option>' + [...categories.entries()].map(([id, name]) => `<option value="${id}">${name}</option>`).join('');
        vendorSel.innerHTML = '<option value="">All Vendors</option>' + [...vendors.entries()].map(([id, name]) => `<option value="${id}">${name}</option>`).join('');

        categorySel.value = prevCategory;
        vendorSel.value = prevVendor;
    }

    function getFiltered() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const categoryId = document.getElementById('categoryFilter').value;
        const vendorId = document.getElementById('vendorFilter').value;

        return allProducts.filter(p => {
            if (search && !p.name.toLowerCase().includes(search)) return false;
            if (categoryId && String(p.category?.id) !== String(categoryId)) return false;
            if (vendorId && String(p.vendor?.id) !== String(vendorId)) return false;
            return true;
        });
    }

    function render() {
        const tbody = document.getElementById('productsTableBody');
        const filtered = getFiltered();

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (!pageItems.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state">No products match these filters.</td></tr>`;
        } else {
            tbody.innerHTML = pageItems.map(p => `
                <tr>
                    <td>
                        <div class="product-cell">
                            ${productThumb(p)}
                            <div>
                                <div class="product-name">${p.name}</div>
                                ${p.slug ? `<div class="product-slug">${p.slug}</div>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${p.vendor?.business_name || '—'}</td>
                    <td>${p.category?.name || '—'}</td>
                    <td>${p.brand?.name || '—'}</td>
                    <td>${statusBadge(p)}</td>
                    <td>${formatDate(p.created_at)}</td>
                    <td>${actionButtons(p)}</td>
                </tr>
            `).join('');
        }

        document.getElementById('tableSummary').textContent = filtered.length === 0 ?
            'No products to show' :
            `Showing ${start + 1}-${start + pageItems.length} of ${filtered.length} products`;

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

    function actionButtons(p) {
        const isPending = (p.status || '').toLowerCase() === 'pending';
        return `
            <div class="actions-cell">
                <button class="btn btn-outline btn-sm" onclick="openViewModal(${p.id})">View</button>
                ${isPending ? `
                    <button class="btn btn-terracotta btn-sm" onclick="openApproveModal(${p.id})">Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="openRejectModal(${p.id})">Reject</button>
                ` : ''}
            </div>
        `;
    }

    function findProductById(id) {
        return allProducts.find(p => p.id === id);
    }

    // =====================================================================
    // VIEW MODAL (fetches full product details)
    // =====================================================================

    function prettifyKey(key) {
        return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    // Renders any object's own keys/values generically (used for inventory
    // and other blocks whose exact shape can vary by product).
    function renderKvList(obj, excludeKeys = []) {
        if (!obj || typeof obj !== 'object') return '<div class="desc-block">—</div>';
        const entries = Object.entries(obj).filter(([k]) => !excludeKeys.includes(k));
        if (!entries.length) return '<div class="desc-block">—</div>';

        return `<div class="kv-list">${entries.map(([k, v]) => {
            let val = v;
            if (val === null || val === undefined || val === '') val = '—';
            else if (typeof val === 'object') val = JSON.stringify(val);
            return `<div class="kv-row"><span class="k">${prettifyKey(k)}</span><span class="v">${val}</span></div>`;
        }).join('')}</div>`;
    }

    function renderImages(product) {
        if (!product.images || !product.images.length) {
            return '<div class="desc-block">No images uploaded.</div>';
        }
        const srcs = product.images.map(img => {
            const path = typeof img === 'string' ? img : (img.image_url || img.url || img.path || img.image);
            return resolveImageSrc(path);
        }).filter(Boolean);

        if (!srcs.length) return '<div class="desc-block">No images uploaded.</div>';
        return `<div class="image-gallery">${srcs.map(s => `<img src="${s}" alt="">`).join('')}</div>`;
    }

    function renderVariants(product) {
        if (!product.variants || !product.variants.length) {
            return '<div class="desc-block">No variants added.</div>';
        }

        return product.variants.map((variant, idx) => {
            const heading = variant.sku || variant.name || `Variant ${idx + 1}`;
            const excludeKeys = ['id', 'attributes', 'attribute_values', 'variant_attributes'];
            const attrs = variant.attributes || variant.attribute_values || variant.variant_attributes;

            let attrsHtml = '';
            if (Array.isArray(attrs) && attrs.length) {
                attrsHtml = `<div style="margin-top:8px;">${attrs.map(a => {
                    const label = a.attribute?.name || a.name || '';
                    const val = a.value || a.attribute_value?.value || a;
                    return `<span class="attr-chip">${label ? label + ': ' : ''}${typeof val === 'object' ? (val.value || JSON.stringify(val)) : val}</span>`;
                }).join('')}</div>`;
            }

            return `
                <div class="variant-card">
                    <div class="variant-heading">${heading}</div>
                    ${renderKvList(variant, excludeKeys)}
                    ${attrsHtml}
                </div>
            `;
        }).join('');
    }

    function renderApprovalInfo(product) {
        const status = (product.status || '').toLowerCase();
        let html = `
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="label">Status</div>
                    <div class="value">${statusBadge(product)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Submitted</div>
                    <div class="value">${formatDate(product.created_at)}</div>
                </div>
        `;

        if (product.approved_at) {
            html += `
                <div class="detail-item">
                    <div class="label">Approved / Reviewed On</div>
                    <div class="value">${formatDate(product.approved_at)}</div>
                </div>
            `;
        }
        if (product.approved_by) {
            const reviewer = typeof product.approved_by === 'object' ?
                (product.approved_by.name || product.approved_by.email || product.approved_by.id) :
                product.approved_by;
            html += `
                <div class="detail-item">
                    <div class="label">Reviewed By</div>
                    <div class="value">${reviewer}</div>
                </div>
            `;
        }
        html += `</div>`;

        if (status === 'rejected' && product.rejection_reason) {
            html += `
                <div class="rejection-box" style="margin-top: 10px;">
                    <strong>Rejection Reason:</strong> ${product.rejection_reason}
                </div>
            `;
        }

        return html;
    }

    async function openViewModal(id) {
        viewTargetId = id;
        document.getElementById('viewModalTitle').textContent = 'Product Details';
        document.getElementById('viewModalSub').textContent = '';
        document.getElementById('viewModalContent').innerHTML = '<div class="loading-state">Loading product details...</div>';
        document.getElementById('viewApproveBtn').style.display = 'none';
        document.getElementById('viewRejectBtn').style.display = 'none';
        document.getElementById('viewModal').classList.add('open');

        try {
            const res = await fetch(`${API_BASE}/products/${id}`, {
                headers: authHeaders()
            });
            const json = await res.json();

            if (!json.success) {
                document.getElementById('viewModalContent').innerHTML =
                    `<div class="empty-state">${json.message || 'Failed to load product.'}</div>`;
                return;
            }

            const product = json.data;
            document.getElementById('viewModalTitle').textContent = product.name;
            document.getElementById('viewModalSub').textContent =
                `${product.vendor?.business_name || 'Unknown vendor'} · ${product.category?.name || 'Uncategorized'}`;

            document.getElementById('viewModalContent').innerHTML = `
                <div class="section-title">Images</div>
                ${renderImages(product)}

                <div class="section-title">Overview</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Vendor</div>
                        <div class="value">${product.vendor?.business_name || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Category</div>
                        <div class="value">${product.category?.name || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Brand</div>
                        <div class="value">${product.brand?.name || '—'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Slug</div>
                        <div class="value">${product.slug || '—'}</div>
                    </div>
                </div>

                <div class="section-title">Description</div>
                <div class="detail-item full" style="margin-bottom:12px;">
                    <div class="label">Short Description</div>
                    <div class="desc-block">${product.short_description || '—'}</div>
                </div>
                <div class="detail-item full">
                    <div class="label">Full Description</div>
                    <div class="desc-block">${product.description || '—'}</div>
                </div>

                ${(product.meta_title || product.meta_description) ? `
                    <div class="section-title">SEO Meta</div>
                    <div class="kv-list">
                        <div class="kv-row"><span class="k">Meta Title</span><span class="v">${product.meta_title || '—'}</span></div>
                        <div class="kv-row"><span class="k">Meta Description</span><span class="v">${product.meta_description || '—'}</span></div>
                    </div>
                ` : ''}

                <div class="section-title">Variants</div>
                ${renderVariants(product)}

                ${product.inventory ? `
                    <div class="section-title">Inventory</div>
                    ${renderKvList(product.inventory)}
                ` : ''}

                <div class="section-title">Approval</div>
                ${renderApprovalInfo(product)}
            `;

            const isPending = (product.status || '').toLowerCase() === 'pending';
            document.getElementById('viewApproveBtn').style.display = isPending ? 'inline-flex' : 'none';
            document.getElementById('viewRejectBtn').style.display = isPending ? 'inline-flex' : 'none';
        } catch (e) {
            document.getElementById('viewModalContent').innerHTML =
                '<div class="empty-state">Something went wrong. Please try again.</div>';
            console.error(e);
        }
    }

    function closeViewModal() {
        viewTargetId = null;
        document.getElementById('viewModal').classList.remove('open');
    }

    document.getElementById('viewApproveBtn').addEventListener('click', () => {
        if (viewTargetId == null) return;
        openApproveModal(viewTargetId);
    });
    document.getElementById('viewRejectBtn').addEventListener('click', () => {
        if (viewTargetId == null) return;
        openRejectModal(viewTargetId);
    });

    // =====================================================================
    // APPROVE
    // =====================================================================

    function openApproveModal(id) {
        approveTargetId = id;
        const product = findProductById(id);
        document.getElementById('approveModalText').textContent = product ?
            `Approve "${product.name}"?` :
            'Approve this product?';
        document.getElementById('approveModal').classList.add('open');
    }

    function closeApproveModal() {
        approveTargetId = null;
        document.getElementById('approveModal').classList.remove('open');
    }

    document.getElementById('confirmApproveBtn').addEventListener('click', async () => {
        if (approveTargetId == null) return;
        try {
            const res = await fetch(`${API_BASE}/products/${approveTargetId}/approve`, {
                method: 'POST',
                headers: authHeaders(),
            });
            const json = await res.json();

            if (json.success) {
                showToast(json.message || 'Product approved successfully.');
                closeApproveModal();
                closeViewModal();
                await loadProducts();
            } else {
                // Covers backend's 422 "Only pending products can be approved" and 404.
                showToast(json.message || 'Failed to approve product.', true);
                closeApproveModal();
            }
        } catch (e) {
            showToast('Something went wrong.', true);
            closeApproveModal();
        }
    });

    // =====================================================================
    // REJECT
    // =====================================================================

    function openRejectModal(id) {
        rejectTargetId = id;
        document.getElementById('rejectReasonInput').value = '';
        document.getElementById('rejectReasonError').style.display = 'none';
        const product = findProductById(id);
        document.getElementById('rejectModalText').textContent = product ?
            `Please provide a reason for rejecting "${product.name}".` :
            'Please provide a reason for rejecting this product.';
        document.getElementById('rejectModal').classList.add('open');
    }

    function closeRejectModal() {
        rejectTargetId = null;
        document.getElementById('rejectModal').classList.remove('open');
    }

    document.getElementById('confirmRejectBtn').addEventListener('click', async () => {
        if (rejectTargetId == null) return;

        const reason = document.getElementById('rejectReasonInput').value.trim();
        if (!reason) {
            document.getElementById('rejectReasonError').style.display = 'block';
            return;
        }
        document.getElementById('rejectReasonError').style.display = 'none';

        try {
            const res = await fetch(`${API_BASE}/products/${rejectTargetId}/reject`, {
                method: 'POST',
                headers: authHeaders(),
                body: JSON.stringify({
                    rejection_reason: reason
                }),
            });
            const json = await res.json();


            if (json.success) {
                showToast(json.message || 'Product rejected successfully.');
                closeRejectModal();
                closeViewModal();
                await loadProducts();
            } else {
                // Covers backend's 422 "Rejection reason is required." / "Only pending products can be rejected" and 404.
                if (json.message && json.message.toLowerCase().includes('reason')) {
                    document.getElementById('rejectReasonError').textContent = json.message;
                    document.getElementById('rejectReasonError').style.display = 'block';
                } else {
                    showToast(json.message || 'Failed to reject product.', true);
                    closeRejectModal();
                }
            }
        } catch (e) {
            showToast('Something went wrong.', true);
            closeRejectModal();
        }
    });

    // =====================================================================
    // FILTERS
    // =====================================================================

    let searchDebounce = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentPage = 1;
            render();
        }, 350);
    });
    document.getElementById('categoryFilter').addEventListener('change', () => {
        currentPage = 1;
        render();
    });
    document.getElementById('vendorFilter').addEventListener('change', () => {
        currentPage = 1;
        render();
    });

    // Initial load
    loadProducts();
</script>
@endpush

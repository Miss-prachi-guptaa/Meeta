@extends('vendor.layouts.app')

@section('title', 'Products')

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
    font-size: 27px;
    margin-bottom: 5px;
  }

  .page-header p {
    color: var(--text-secondary);
    font-size: 14.5px;
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
    min-width: 140px;
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
    font-size: 26px;
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
    min-width: 220px;
    max-width: 360px;
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
    padding: 12px 16px 12px 40px;
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

  .filter-group select {
    min-width: 160px;
  }

  .table-wrap {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 820px;
  }

  thead th {
    text-align: left;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 16px 20px;
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

  .prod-name {
    font-weight: 600;
    color: var(--text-primary);
  }

  .product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 280px;
  }

  .product-thumb {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    background: var(--bg-card-hover);
    object-fit: cover;
  }

  .product-thumb-fallback {
    display: grid;
    place-items: center;
    color: var(--accent);
    font-weight: 700;
    font-size: 18px;
  }

  .prod-slug {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
  }

  .badge.status-draft {
    background: var(--neutral-soft);
    color: var(--neutral);
  }

  .badge.status-pending {
    background: #fbead1;
    color: #9a6a15;
  }

  .badge.status-active {
    background: var(--success-soft);
    color: var(--success);
  }

  .badge.status-rejected {
    background: var(--danger-soft);
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
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
    font-size: 14px;
  }

  .table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
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
    max-width: 540px;
    padding: 28px;
    box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18);
    margin: auto;
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

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }

  @media (max-width: 560px) {
    .form-row {
      grid-template-columns: 1fr;
    }
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
  .form-group textarea,
  .form-group select {
    width: 100%;
  }

  .form-group textarea {
    min-height: 70px;
    resize: vertical;
  }

  .form-group select:disabled {
    background: var(--bg-card-hover);
    cursor: not-allowed;
    color: var(--text-muted);
  }

  .form-error {
    color: var(--danger);
    font-size: 12px;
    margin-top: 5px;
    display: none;
  }

  .form-hint {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 5px;
  }

  .field-load-error {
    color: var(--danger);
    font-size: 12px;
    margin-top: 5px;
    display: none;
    align-items: center;
    gap: 6px;
  }

  .field-load-error .retry-link {
    color: var(--terracotta-dark);
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
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
</style>
@endpush

@section('content')

<div class="page-header-row">
  <div class="page-header">
    <h1>Products</h1>
    <p>Create and manage your product listings.</p>
  </div>
  <div class="header-right">
    <div class="total-count-box">
      <div class="count-label">Total Products</div>
      <div class="count-value" id="totalProductCount">—</div>
    </div>
    <button class="btn btn-terracotta" onclick="openAddModal()">+ Add Product</button>
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
      <label class="filter-label">Status</label>
      <select id="statusFilter">
        <option value="">All</option>
        <option value="draft">Draft</option>
        <option value="pending">Pending</option>
        <option value="active">Active</option>
        <option value="rejected">Rejected</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div class="filter-group">
      <label class="filter-label">Category</label>
      <select id="categoryFilter">
        <option value="">All Categories</option>
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
          <th>Category</th>
          <th>Brand</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="productsTableBody">
        <tr>
          <td colspan="6" class="loading-state">Loading products...</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="table-footer">
    <span id="tableSummary"></span>
    <div class="pagination" id="paginationControls"></div>
  </div>
</div>

<!-- Add/Edit Product Modal (basic info only) -->
<div class="modal-overlay" id="formModal">
  <div class="modal-box">
    <h3 id="formModalTitle">Add Product</h3>
    <p class="modal-sub">Basic details — you can add images, variants and inventory after saving.</p>

    <form id="productForm">
      <div class="form-row">
        <div class="form-group">
          <label>Category <span class="required">*</span></label>
          <select id="formCategory">
            <option value="">Loading categories...</option>
          </select>
          <div class="field-load-error" id="formCategoryError">
            <span>Failed to load categories.</span>
            <span class="retry-link" onclick="loadCategoryOptions()">Retry</span>
          </div>
        </div>
        <div class="form-group">
          <label>Brand</label>
          <select id="formBrand">
            <option value="">Loading brands...</option>
          </select>
          <div class="field-load-error" id="formBrandError">
            <span>Failed to load brands.</span>
            <span class="retry-link" onclick="loadBrandOptions()">Retry</span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Product Name <span class="required">*</span></label>
        <input type="text" id="formName" placeholder="e.g. Handwoven Jute Basket">
        <div class="form-error" id="formNameError">Product name is required.</div>
      </div>

      <div class="form-group">
        <label>Short Description</label>
        <input type="text" id="formShortDescription" placeholder="One-line summary shown in listings">
      </div>

      <div class="form-group">
        <label>Full Description</label>
        <textarea id="formDescription" placeholder="Detailed product description..."></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Meta Title</label>
          <input type="text" id="formMetaTitle" placeholder="SEO title">
        </div>
        <div class="form-group">
          <label>Meta Description</label>
          <input type="text" id="formMetaDescription" placeholder="SEO description">
        </div>
      </div>
    </form>
    <div class="form-error" id="formGeneralError" style="display:none;"></div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeFormModal()">Cancel</button>
      <button class="btn btn-terracotta" id="saveProductBtn">Save & Continue</button>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box" style="max-width: 420px;">
    <h3>Delete Product</h3>
    <p class="modal-sub" id="deleteModalText">Are you sure you want to delete this product? This cannot be undone.</p>
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
  const API_BASE = "{{ url('/api/vendor') }}";
  const MANAGE_BASE = "{{ url('/vendor/products') }}";
  const STORAGE_BASE = "{{ url('/storage') }}";
  const token = localStorage.getItem('meeta_vendor_token') || localStorage.getItem('meeta_token');

  let allProducts = [];
  let currentPage = 1;
  const perPage = 10;
  let editingProductId = null;
  let deleteTargetId = null;

  // Active category/brand lists (only is_active/status==="active" entries), used for the Add/Edit form dropdowns.
  let activeCategories = [];
  let activeBrands = [];
  let categoriesLoaded = false;
  let brandsLoaded = false;

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
    setTimeout(() => toast.classList.remove('show'), 3800);
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

  function statusBadge(p) {
    const status = (p.status || 'draft').toLowerCase();
    const label = status.charAt(0).toUpperCase() + status.slice(1);
    return `<span class="badge status-${status}">${label}</span>`;
  }

  // =====================================================================
  // Category / Brand dropdowns
  // Confirmed APIs:
  //   GET /api/vendor/categories -> use only is_active === true
  //   GET /api/vendor/brands     -> use only status === "active"
  // No manual ID fallback. On failure, show an inline error + retry link.
  // =====================================================================
  async function loadCategoryOptions() {
    const sel = document.getElementById('formCategory');
    const errBox = document.getElementById('formCategoryError');
    errBox.style.display = 'none';
    sel.disabled = true;
    sel.innerHTML = '<option value="">Loading categories...</option>';
    categoriesLoaded = false;

    try {
      const res = await fetch(`${API_BASE}/categories`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load categories.');

      const raw = json.data.data || json.data || [];
      activeCategories = raw.filter(c => c.is_active === true);

      sel.innerHTML = '<option value="">Select category</option>' +
        activeCategories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
      sel.disabled = false;
      categoriesLoaded = true;

      populateCategoryFilter();
    } catch (e) {
      sel.innerHTML = '<option value="">Select category</option>';
      sel.disabled = true;
      errBox.style.display = 'flex';
    }
  }

  async function loadBrandOptions() {
    const sel = document.getElementById('formBrand');
    const errBox = document.getElementById('formBrandError');
    errBox.style.display = 'none';
    sel.disabled = true;
    sel.innerHTML = '<option value="">Loading brands...</option>';
    brandsLoaded = false;

    try {
      const res = await fetch(`${API_BASE}/brands`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load brands.');

      const raw = json.data.data || json.data || [];
      activeBrands = raw.filter(b => b.status === 'active');

      sel.innerHTML = '<option value="">Select brand</option>' +
        activeBrands.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
      sel.disabled = false;
      brandsLoaded = true;
    } catch (e) {
      sel.innerHTML = '<option value="">Select brand</option>';
      sel.disabled = true;
      errBox.style.display = 'flex';
    }
  }

  function populateCategoryFilter() {
    const sel = document.getElementById('categoryFilter');
    sel.innerHTML = '<option value="">All Categories</option>' +
      activeCategories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
  }

  function getCategoryValue() {
    return document.getElementById('formCategory').value;
  }

  function getBrandValue() {
    return document.getElementById('formBrand').value;
  }

  // ---------- Load + render products ----------
  async function loadProducts() {
    const tbody = document.getElementById('productsTableBody');
    tbody.innerHTML = `<tr><td colspan="6" class="loading-state">Loading products...</td></tr>`;

    try {
      const res = await fetch(`${API_BASE}/products`, {
        headers: authHeaders()
      });
      const json = await res.json();

      if (!json.success) {
        tbody.innerHTML = `<tr><td colspan="6" class="empty-state">${json.message || 'Failed to load products.'}</td></tr>`;
        return;
      }

      allProducts = json.data.data || json.data || [];
      document.getElementById('totalProductCount').textContent = allProducts.length;
      render();
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="6" class="empty-state">Something went wrong. Please try again.</td></tr>`;
      console.error(e);
    }
  }

  function getFiltered() {
    const search = document.getElementById('searchInput').value.trim().toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const categoryId = document.getElementById('categoryFilter').value;

    return allProducts.filter(p => {
      if (search && !p.name.toLowerCase().includes(search)) return false;
      if (status && (p.status || '').toLowerCase() !== status) return false;
      if (categoryId && String(p.category?.id) !== String(categoryId)) return false;
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
      tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No products match these filters.</td></tr>`;
    } else {
      tbody.innerHTML = pageItems.map(p => `
                <tr>
                    <td>
                        <div class="product-cell">
                            ${productThumbnail(p)}
                            <div>
                                <div class="prod-name">${p.name}</div>
                                ${p.slug ? `<div class="prod-slug">${p.slug}</div>` : ''}
                            </div>
                        </div>
                    </td>
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

  function resolveImageSrc(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
    return `${STORAGE_BASE}/${path}`;
  }

  function productThumbnail(product) {
    const path = product.primary_image?.image_url;
    if (path) {
      const alt = product.primary_image.alt_text || product.name || 'Product image';
      return `<img class="product-thumb" src="${resolveImageSrc(path)}" alt="${alt}" loading="lazy">`;
    }

    const initial = (product.name || 'P').trim().charAt(0).toUpperCase();
    return `<div class="product-thumb product-thumb-fallback" aria-label="No product image">${initial}</div>`;
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
    const status = (p.status || '').toLowerCase();
    const canSubmit = status === 'draft' || status === 'rejected';
    return `
            <div class="actions-cell">
                <a class="btn btn-outline btn-sm" href="${MANAGE_BASE}/${p.id}/manage">Manage</a>
                <button class="btn btn-outline btn-sm" onclick="openEditModal(${p.id})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${p.id})">Delete</button>
            </div>
        `;
  }

  function findProductById(id) {
    return allProducts.find(p => p.id === id);
  }

  // ---------- Add/Edit modal ----------
  function resetFormModal() {
    document.getElementById('productForm').reset();
    document.getElementById('formNameError').style.display = 'none';
    document.getElementById('formGeneralError').style.display = 'none';
  }

  function openAddModal() {
    editingProductId = null;
    resetFormModal();
    document.getElementById('formModalTitle').textContent = 'Add Product';
    document.getElementById('saveProductBtn').textContent = 'Save & Continue';
    document.getElementById('formModal').classList.add('open');
  }

  function openEditModal(id) {
    editingProductId = id;
    resetFormModal();
    const p = findProductById(id);
    document.getElementById('formModalTitle').textContent = 'Edit Product';
    document.getElementById('saveProductBtn').textContent = 'Save Changes';
    if (p) {
      if (categoriesLoaded) document.getElementById('formCategory').value = p.category?.id || '';
      if (brandsLoaded) document.getElementById('formBrand').value = p.brand?.id || '';

      document.getElementById('formName').value = p.name || '';
      document.getElementById('formShortDescription').value = p.short_description || '';
      document.getElementById('formDescription').value = p.description || '';
      document.getElementById('formMetaTitle').value = p.meta_title || '';
      document.getElementById('formMetaDescription').value = p.meta_description || '';
    }
    document.getElementById('formModal').classList.add('open');
  }

  function closeFormModal() {
    editingProductId = null;
    document.getElementById('formModal').classList.remove('open');
  }

  document.getElementById('saveProductBtn').addEventListener('click', async () => {
    const name = document.getElementById('formName').value.trim();
    if (!name) {
      document.getElementById('formNameError').style.display = 'block';
      return;
    }
    document.getElementById('formNameError').style.display = 'none';
    document.getElementById('formGeneralError').style.display = 'none';

    if (!categoriesLoaded) {
      document.getElementById('formGeneralError').textContent = 'Categories failed to load. Please retry before saving.';
      document.getElementById('formGeneralError').style.display = 'block';
      return;
    }

    const payload = {
      category_id: getCategoryValue() || null,
      brand_id: getBrandValue() || null,
      name: name,
      short_description: document.getElementById('formShortDescription').value.trim(),
      description: document.getElementById('formDescription').value.trim(),
      meta_title: document.getElementById('formMetaTitle').value.trim(),
      meta_description: document.getElementById('formMetaDescription').value.trim(),
    };

    const isEdit = !!editingProductId;
    const url = isEdit ? `${API_BASE}/products/${editingProductId}` : `${API_BASE}/products`;

    try {
      const res = await fetch(url, {
        method: isEdit ? 'PUT' : 'POST',
        headers: authHeaders(),
        body: JSON.stringify(payload),
      });
      const json = await res.json();

      if (json.success) {
        if (isEdit) {
          showToast(json.message || 'Product updated successfully.');
          closeFormModal();
          await loadProducts();
        } else {
          showToast(json.message || 'Product created successfully.');
          window.location.href = `${MANAGE_BASE}/${json.data.id}/manage`;
        }
      } else {
        const errMsg = json.message || 'Failed to save product.';
        document.getElementById('formGeneralError').textContent = errMsg;
        document.getElementById('formGeneralError').style.display = 'block';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  // ---------- Delete modal ----------
  function openDeleteModal(id) {
    deleteTargetId = id;
    const p = findProductById(id);
    document.getElementById('deleteModalText').textContent = p ?
      `Are you sure you want to delete "${p.name}"? This cannot be undone.` :
      'Are you sure you want to delete this product?';
    document.getElementById('deleteModal').classList.add('open');
  }

  function closeDeleteModal() {
    deleteTargetId = null;
    document.getElementById('deleteModal').classList.remove('open');
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    try {
      const res = await fetch(`${API_BASE}/products/${deleteTargetId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Product deleted successfully.');
        closeDeleteModal();
        await loadProducts();
      } else {
        showToast(json.message || 'Failed to delete product.', true);
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
    searchDebounce = setTimeout(() => {
      currentPage = 1;
      render();
    }, 300);
  });
  document.getElementById('statusFilter').addEventListener('change', () => {
    currentPage = 1;
    render();
  });
  document.getElementById('categoryFilter').addEventListener('change', () => {
    currentPage = 1;
    render();
  });

  // Init
  loadCategoryOptions();
  loadBrandOptions();
  loadProducts();
</script>
@endpush

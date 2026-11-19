@extends('vendor.layouts.app')

@section('title', 'Manage Product')

@push('styles')
<style>
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    color: var(--text-secondary);
    margin-bottom: 16px;
  }

  .back-link:hover {
    color: var(--terracotta-dark);
  }

  .back-link svg {
    width: 15px;
    height: 15px;
  }

  .manage-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
    flex-wrap: wrap;
  }

  .manage-header h1 {
    font-size: 25px;
    margin-bottom: 6px;
  }

  .manage-header-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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

  .rejection-box {
    background: var(--danger-soft);
    border: 1px solid #f0b8b8;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13.5px;
    color: #7a2222;
    margin-bottom: 20px;
  }

  .section-card {
    padding: 22px 24px;
    margin-bottom: 20px;
  }

  .section-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
    flex-wrap: wrap;
    gap: 10px;
  }

  .section-card-header h2 {
    font-size: 17px;
    font-family: 'Fraunces', serif;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }

  @media (max-width: 640px) {
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

  /* Images */
  .image-grid {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }

  .image-card {
    width: 140px;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    background: var(--bg-card-hover);
    position: relative;
  }

  .image-card img {
    width: 100%;
    height: 110px;
    object-fit: cover;
    display: block;
  }

  .image-card .image-body {
    padding: 8px 10px;
  }

  .image-card .primary-pill {
    position: absolute;
    top: 6px;
    left: 6px;
    background: var(--terracotta);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
  }

  .image-card .image-actions {
    display: flex;
    gap: 5px;
    margin-top: 6px;
  }

  .image-card .image-actions button {
    flex: 1;
    font-size: 11px;
    padding: 5px;
    border-radius: 6px;
    border: 1px solid var(--border-color-strong);
    background: #fff;
    cursor: pointer;
    font-family: inherit;
  }

  .image-card .image-actions button.danger {
    color: var(--danger);
    border-color: #f0b8b8;
  }

  .upload-box {
    border: 1.5px dashed var(--border-color-strong);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    background: var(--bg-card-hover);
  }

  .upload-box input[type="file"] {
    margin: 10px auto 0;
    display: block;
  }

  /* Variants */
  .table-wrap {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
  }

  thead th {
    text-align: left;
    font-size: 11.5px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 13px 16px;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  tbody td {
    padding: 13px 16px;
    border-bottom: 1px solid var(--border-color);
    font-size: 13.5px;
    vertical-align: middle;
  }

  tbody tr:last-child td {
    border-bottom: none;
  }

  .actions-cell {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .empty-state,
  .loading-state {
    padding: 34px 16px;
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
  }

  .attr-chip {
    display: inline-block;
    background: var(--terracotta-soft);
    color: var(--terracotta-dark);
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 20px;
    margin: 2px 3px 2px 0;
  }

  /* Modals */
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
    max-width: 520px;
    padding: 28px;
    box-shadow: 0 12px 32px rgba(60, 46, 33, 0.18);
    margin: auto;
  }

  .modal-box.modal-wide {
    max-width: 640px;
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

  .toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-card-hover);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 12px 16px;
  }

  .switch {
    position: relative;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .switch-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: var(--neutral);
    border-radius: 20px;
    transition: 0.2s;
  }

  .switch-slider:before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: 0.2s;
  }

  .switch input:checked+.switch-slider {
    background: var(--success);
  }

  .switch input:checked+.switch-slider:before {
    transform: translateX(18px);
  }

  .kv-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 14px;
  }

  .kv-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: var(--bg-card-hover);
    border-radius: 8px;
    font-size: 13px;
  }

  .kv-row .k {
    color: var(--text-muted);
  }

  .kv-row .v {
    font-weight: 600;
    color: var(--text-primary);
  }

  .attr-value-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    margin-bottom: 8px;
  }

  .attr-value-row .attr-label {
    font-size: 13.5px;
  }

  .attr-value-row .attr-label b {
    color: var(--terracotta-dark);
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

  .submit-errors {
    background: var(--danger-soft);
    border: 1px solid #f0b8b8;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 16px;
    display: none;
  }

  .submit-errors ul {
    margin: 6px 0 0 18px;
    font-size: 13px;
    color: #7a2222;
  }
</style>
@endpush

@section('content')

<a href="{{ url('/vendor/products') }}" class="back-link">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <line x1="19" y1="12" x2="5" y2="12" />
    <polyline points="12 19 5 12 12 5" />
  </svg>
  Back to Products
</a>

<div id="page-data" data-product-id="{{ (int) $productId }}" style="display:none;"></div>

<div class="manage-header">
  <div>
    <h1 id="productTitle">Loading...</h1>
    <div class="manage-header-meta">
      <span id="productStatusBadge"></span>
      <span style="font-size:13px; color: var(--text-muted);" id="productSlugText"></span>
    </div>
  </div>
  <button class="btn btn-terracotta" id="submitApprovalBtn" style="display:none;">Submit for Approval</button>
</div>

<div class="rejection-box" id="rejectionBox" style="display:none;"></div>

<!-- Basic Info -->
<div class="card section-card">
  <div class="section-card-header">
    <h2>Basic Information</h2>
  </div>
  <form id="infoForm">
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
      <input type="text" id="formName">
    </div>
    <div class="form-group">
      <label>Short Description</label>
      <input type="text" id="formShortDescription">
    </div>
    <div class="form-group">
      <label>Full Description</label>
      <textarea id="formDescription"></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Meta Title</label>
        <input type="text" id="formMetaTitle">
      </div>
      <div class="form-group">
        <label>Meta Description</label>
        <input type="text" id="formMetaDescription">
      </div>
    </div>
  </form>
  <div class="modal-actions" style="margin-top: 4px;">
    <button class="btn btn-terracotta" id="saveInfoBtn">Save Changes</button>
  </div>
</div>

<!-- Images -->
<div class="card section-card">
  <div class="section-card-header">
    <h2>Product Images</h2>
  </div>
  <div class="image-grid" id="imageGrid">
    <div class="loading-state">Loading images...</div>
  </div>
  <div class="upload-box">
    <div style="font-size:13.5px; color: var(--text-secondary);">Upload one or more images (JPG/PNG)</div>
    <input type="file" id="imageFileInput" accept="image/*" multiple>
    <input type="text" id="imageAltInput" placeholder="Alt text (optional, applied to all)" style="margin-top:10px; max-width:320px;">
    <div style="margin-top:12px;">
      <button class="btn btn-terracotta btn-sm" id="uploadImagesBtn">Upload</button>
    </div>
  </div>
</div>

<!-- Variants -->
<div class="card section-card">
  <div class="section-card-header">
    <h2>Variants</h2>
    <button class="btn btn-terracotta btn-sm" onclick="openVariantFormModal()">+ Add Variant</button>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:50px;">Photo</th>
          <th>SKU</th>
          <th>Barcode</th>
          <th>Price</th>
          <th>Sale Price</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="variantsTableBody">
        <tr>
          <td colspan="7" class="loading-state">Loading variants...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Add/Edit Variant Modal -->
<div class="modal-overlay" id="variantFormModal">
  <div class="modal-box">
    <h3 id="variantFormTitle">Add Variant</h3>
    <p class="modal-sub">SKU is generated automatically by the system.</p>
    <form id="variantForm">
      <div class="form-row">
        <div class="form-group"><label>Barcode</label><input type="text" id="variantBarcode" placeholder="e.g. 8901234567890"></div>
        <div class="form-group"><label>Price <span class="required">*</span></label><input type="number" id="variantPrice" step="0.01"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Sale Price</label><input type="number" id="variantSalePrice" step="0.01"></div>
        <div class="form-group"><label>Weight (kg)</label><input type="number" id="variantWeight" step="0.01"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Length (cm)</label><input type="number" id="variantLength" step="0.01"></div>
        <div class="form-group"><label>Width (cm)</label><input type="number" id="variantWidth" step="0.01"></div>
      </div>
      <div class="form-group"><label>Height (cm)</label><input type="number" id="variantHeight" step="0.01"></div>
      <div class="form-group" id="variantActiveGroup" style="display:none;">
        <label>Status</label>
        <div class="toggle-row">
          <span id="variantActiveLabel" style="font-size:13.5px;">Active</span>
          <label class="switch"><input type="checkbox" id="variantIsActive" checked><span class="switch-slider"></span></label>
        </div>
      </div>
    </form>
    <div class="form-error" id="variantFormError" style="display:none;"></div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeVariantFormModal()">Cancel</button>
      <button class="btn btn-terracotta" id="saveVariantBtn">Save Variant</button>
    </div>
  </div>
</div>

<!-- Delete Variant Modal -->
<div class="modal-overlay" id="deleteVariantModal">
  <div class="modal-box" style="max-width: 420px;">
    <h3>Delete Variant</h3>
    <p class="modal-sub" id="deleteVariantText">Are you sure you want to delete this variant?</p>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeDeleteVariantModal()">Cancel</button>
      <button class="btn btn-danger" id="confirmDeleteVariantBtn">Delete</button>
    </div>
  </div>
</div>

<!-- Variant Attributes Modal -->
<div class="modal-overlay" id="attributesModal">
  <div class="modal-box modal-wide">
    <h3>Variant Attributes</h3>
    <p class="modal-sub" id="attributesModalSub">Assign attribute values (e.g. Color: Red) to this variant.</p>

    <div id="attributesList" class="kv-list" style="margin-bottom: 18px;">
      <div class="loading-state">Loading...</div>
    </div>

    <div class="form-row" id="attrSelectRow">
      <div class="form-group">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
          <label style="margin-bottom:0;">Attribute</label>
          <button type="button" onclick="toggleNewAttributeBox()" style="background:none; border:none; color:var(--terracotta); font-size:11.5px; font-weight:600; cursor:pointer;">+ Create New</button>
        </div>
        <select id="attrAttributeSelect">
          <option value="">Loading attributes...</option>
        </select>
        <div class="field-load-error" id="attrAttributeError">
          <span>Failed to load attributes.</span>
          <span class="retry-link" onclick="loadAttributeOptions()">Retry</span>
        </div>
      </div>
      <div class="form-group">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
          <label style="margin-bottom:0;">Value</label>
          <button type="button" id="createNewValLink" onclick="toggleNewValueBox()" style="background:none; border:none; color:var(--terracotta); font-size:11.5px; font-weight:600; cursor:pointer; display:none;">+ Create New</button>
        </div>
        <select id="attrValueSelect" disabled>
          <option value="">Select attribute first</option>
        </select>
        <div class="field-load-error" id="attrValueError">
          <span>Failed to load attribute values.</span>
          <span class="retry-link" onclick="loadAttributeValueOptions()">Retry</span>
        </div>
      </div>
    </div>

    <!-- Inline create new attribute box -->
    <div id="newAttributeBox" style="display:none; background:var(--bg-card-hover); padding:12px; border-radius:8px; margin-bottom:14px; border:1px solid var(--border-color);">
      <div style="font-weight:600; font-size:12.5px; margin-bottom:6px;">Create Custom Attribute (e.g. Fabric, Sleeve Length, Weight)</div>
      <div style="display:flex; gap:8px;">
        <input type="text" id="newAttributeNameInput" placeholder="Attribute name..." style="flex:1;">
        <button type="button" class="btn btn-terracotta btn-sm" onclick="saveNewAttribute()">Save</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="toggleNewAttributeBox()">Cancel</button>
      </div>
    </div>

    <!-- Inline create new attribute value box -->
    <div id="newValueBox" style="display:none; background:var(--bg-card-hover); padding:12px; border-radius:8px; margin-bottom:14px; border:1px solid var(--border-color);">
      <div style="font-weight:600; font-size:12.5px; margin-bottom:6px;">Create Custom Value (e.g. Pure Cotton, 3/4th Sleeve)</div>
      <div style="display:flex; gap:8px;">
        <input type="text" id="newValueNameInput" placeholder="Option value..." style="flex:1;">
        <button type="button" class="btn btn-terracotta btn-sm" onclick="saveNewValue()">Save</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="toggleNewValueBox()">Cancel</button>
      </div>
    </div>
    <div class="form-error" id="attrFormError" style="display:none;"></div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeAttributesModal()">Close</button>
      <button class="btn btn-terracotta" id="addAttrValueBtn">Add</button>
    </div>
  </div>
</div>

<!-- Variant Inventory Modal -->
<div class="modal-overlay" id="inventoryModal">
  <div class="modal-box">
    <h3>Inventory</h3>
    <p class="modal-sub" id="inventoryModalSub">Manage stock for this variant.</p>

    <div id="inventoryReadonly" class="kv-list" style="display:none;"></div>

    <form id="inventoryForm">
      <div class="form-row">
        <div class="form-group"><label>Quantity <span class="required">*</span></label><input type="number" id="invQuantity"></div>
        <div class="form-group"><label>Reserved Quantity</label><input type="number" id="invReserved" value="0"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Minimum Stock</label><input type="number" id="invMin"></div>
        <div class="form-group"><label>Maximum Stock</label><input type="number" id="invMax"></div>
      </div>
    </form>
    <div class="form-error" id="inventoryFormError" style="display:none;"></div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeInventoryModal()">Close</button>
      <button class="btn btn-terracotta" id="saveInventoryBtn">Save</button>
    </div>
  </div>
</div>

<!-- Submit for Approval Modal -->
<div class="modal-overlay" id="submitModal">
  <div class="modal-box" style="max-width: 460px;">
    <h3>Submit for Approval</h3>
    <p class="modal-sub">Once submitted, an admin will review this product. Make sure images, variants and inventory are complete.</p>
    <div class="submit-errors" id="submitErrorsBox"></div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeSubmitModal()">Cancel</button>
      <button class="btn btn-terracotta" id="confirmSubmitBtn">Submit</button>
    </div>
  </div>
</div>


<!-- Variant Photos Modal -->
<div class="modal-overlay" id="variantPhotosModal">
  <div class="modal-box modal-wide">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
      <h3>Variant Photos</h3>
      <span class="badge status-active" id="varPhotoSkuBadge" style="font-size:12px;"></span>
    </div>
    <p class="modal-sub">Upload or link specific photos for this variant (e.g. when customer chooses this color/style).</p>

    <!-- Upload for this variant -->
    <div style="background:var(--bg-card-hover); padding:14px; border-radius:10px; border:1.5px dashed var(--border-color-strong); margin-bottom:16px;">
      <div style="font-size:13px; font-weight:600; margin-bottom:6px;">Upload New Photos for this Variant:</div>
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="file" id="varImageFileInput" accept="image/*" multiple style="flex:1; min-width:200px;">
        <input type="text" id="varImageAltInput" placeholder="Alt text..." style="max-width:180px;">
        <button class="btn btn-terracotta btn-sm" id="uploadVarImagesBtn">Upload</button>
      </div>
    </div>

    <!-- Assigned images list -->
    <div style="font-weight:600; font-size:13.5px; margin-bottom:10px;">Photos assigned to this variant:</div>
    <div id="varImagesGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:12px; min-height:90px; margin-bottom:18px;">
      <div class="loading-state">Loading...</div>
    </div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeVariantPhotosModal()">Done</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

@endsection

@push('scripts')
<script>
  const PRODUCT_ID = parseInt(document.getElementById('page-data').dataset.productId, 10);
  const API_BASE = "{{ url('/api/vendor') }}";
  const STORAGE_BASE = "{{ url('/storage') }}";
  const token = localStorage.getItem('meeta_vendor_token') || localStorage.getItem('meeta_token');

  let product = null;
  let images = [];
  let variants = [];
  let activeVariantId = null; // variant open in Attributes/Inventory modal
  let editingVariantId = null;
  let deleteVariantTargetId = null;

  // Active category/brand lists (only is_active/status==="active" entries).
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

  function resolveImageSrc(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    // Public catalogue assets already contain an absolute site path.
    if (path.startsWith('/')) return path;
    return `${STORAGE_BASE}/${path}`;
  }

  function statusBadgeHtml(status) {
    status = (status || 'draft').toLowerCase();
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

      if (product) fillCategoryBrandFields();
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

      if (product) fillCategoryBrandFields();
    } catch (e) {
      sel.innerHTML = '<option value="">Select brand</option>';
      sel.disabled = true;
      errBox.style.display = 'flex';
    }
  }

  function getCategoryValue() {
    return document.getElementById('formCategory').value;
  }

  function getBrandValue() {
    return document.getElementById('formBrand').value;
  }

  function fillCategoryBrandFields() {
    if (categoriesLoaded) document.getElementById('formCategory').value = product.category?.id || '';
    if (brandsLoaded) document.getElementById('formBrand').value = product.brand?.id || '';
  }

  // =====================================================================
  // Load product + basic info
  // =====================================================================
  async function loadProduct() {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!json.success) {
        document.getElementById('productTitle').textContent = 'Product not found';
        showToast(json.message || 'Failed to load product.', true);
        return;
      }
      product = json.data;
      renderProductHeader();
      fillInfoForm();
    } catch (e) {
      document.getElementById('productTitle').textContent = 'Something went wrong';
      console.error(e);
    }
  }

  function renderProductHeader() {
    document.getElementById('productTitle').textContent = product.name || 'Untitled Product';
    document.getElementById('productStatusBadge').innerHTML = statusBadgeHtml(product.status);
    document.getElementById('productSlugText').textContent = product.slug ? `/${product.slug}` : '';

    const status = (product.status || '').toLowerCase();
    const submitBtn = document.getElementById('submitApprovalBtn');
    submitBtn.style.display = (status === 'draft' || status === 'rejected') ? 'inline-flex' : 'none';

    const rejectionBox = document.getElementById('rejectionBox');
    if (status === 'rejected' && product.rejection_reason) {
      rejectionBox.style.display = 'block';
      rejectionBox.innerHTML = `<strong>Rejected:</strong> ${product.rejection_reason}`;
    } else {
      rejectionBox.style.display = 'none';
    }
  }

  function fillInfoForm() {
    fillCategoryBrandFields();

    document.getElementById('formName').value = product.name || '';
    document.getElementById('formShortDescription').value = product.short_description || '';
    document.getElementById('formDescription').value = product.description || '';
    document.getElementById('formMetaTitle').value = product.meta_title || '';
    document.getElementById('formMetaDescription').value = product.meta_description || '';
  }

  document.getElementById('saveInfoBtn').addEventListener('click', async () => {
    const name = document.getElementById('formName').value.trim();
    if (!name) {
      showToast('Product name is required.', true);
      return;
    }

    if (!categoriesLoaded) {
      showToast('Categories failed to load. Please retry before saving.', true);
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

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Product updated successfully.');
        product = json.data;
        renderProductHeader();
      } else {
        showToast(json.message || 'Failed to update product.', true);
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  // =====================================================================
  // Images
  // =====================================================================
  async function loadImages() {
    const grid = document.getElementById('imageGrid');
    grid.innerHTML = '<div class="loading-state">Loading images...</div>';
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!json.success) {
        grid.innerHTML = `<div class="empty-state">${json.message || 'Failed to load images.'}</div>`;
        return;
      }
      images = json.data.data || json.data || [];
      renderImages();
    } catch (e) {
      grid.innerHTML = '<div class="empty-state">Something went wrong.</div>';
    }
  }

  function renderImages() {
    const grid = document.getElementById('imageGrid');
    if (!images.length) {
      grid.innerHTML = '<div class="empty-state">No images uploaded yet.</div>';
      return;
    }

    grid.innerHTML = images.map(img => `
            <div class="image-card">
                ${img.is_primary ? '<span class="primary-pill">Primary</span>' : ''}
                <img src="${resolveImageSrc(img.image_url)}" alt="${img.alt_text || ''}">
                <div class="image-body">
                    <div class="image-actions">
                        ${!img.is_primary ? `<button onclick="setPrimaryImage(${img.id})">Set Primary</button>` : ''}
                        <button class="danger" onclick="deleteImage(${img.id})">Delete</button>
                    </div>
                </div>
            </div>
        `).join('');
  }

  async function setPrimaryImage(imageId) {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({
          is_primary: true
        }),
      });
      const json = await res.json();
      if (json.success) {
        showToast('Primary image updated.');
        await loadImages();

  // ==================== CUSTOM ATTRIBUTES & VALUES ====================
  function toggleNewAttributeBox() {
    const box = document.getElementById('newAttributeBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newAttributeNameInput').focus();
  }

  function toggleNewValueBox() {
    const box = document.getElementById('newValueBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newValueNameInput').focus();
  }

  async function saveNewAttribute() {
    const input = document.getElementById('newAttributeNameInput');
    const name = input.value.trim();
    if (!name) { showToast('Please enter an attribute name.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attributes`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ name })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute.');

      showToast('Custom attribute created successfully!');
      input.value = '';
      toggleNewAttributeBox();

      await loadAttributeOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrAttributeSelect').value = json.data.id;
        populateValueDropdown();
      }
    } catch (e) {
      showToast(e.message || 'Error creating attribute.', true);
    }
  }

  async function saveNewValue() {
    const attrId = document.getElementById('attrAttributeSelect').value;
    if (!attrId) { showToast('Please select an attribute first.', true); return; }

    const input = document.getElementById('newValueNameInput');
    const value = input.value.trim();
    if (!value) { showToast('Please enter a value.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attribute-values`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ attribute_id: attrId, value })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute value.');

      showToast('Custom value created successfully!');
      input.value = '';
      toggleNewValueBox();

      await loadAttributeValueOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrValueSelect').value = json.data.id;
      }
    } catch (e) {
      showToast(e.message || 'Error creating value.', true);
    }
  }

  // Hook populateValueDropdown to show/hide "+ Create New" value link
  const originalPopulate = populateValueDropdown;
  populateValueDropdown = function() {
    originalPopulate();
    const attrSel = document.getElementById('attrAttributeSelect');
    const createValLink = document.getElementById('createNewValLink');
    if (attrSel.value) {
      createValLink.style.display = 'inline-block';
    } else {
      createValLink.style.display = 'none';
    }
  };

  // ==================== VARIANT PHOTOS MODAL ====================
  let activePhotoVariantId = null;

  async function openVariantImagesModal(variantId) {
    activePhotoVariantId = variantId;
    const v = findVariantById(variantId);
    document.getElementById('varPhotoSkuBadge').textContent = v ? (v.sku || ('Variant #' + v.id)) : '';
    document.getElementById('variantPhotosModal').classList.add('open');
    await loadVariantPhotos();
  }

  function closeVariantPhotosModal() {
    activePhotoVariantId = null;
    document.getElementById('variantPhotosModal').classList.remove('open');
    loadVariants(); // Refresh variant list to update photo thumbnail
  }

  async function loadVariantPhotos() {
    const grid = document.getElementById('varImagesGrid');
    grid.innerHTML = '<div class="loading-state">Loading...</div>';

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, { headers: authHeaders() });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load photos.');

      const allImgs = json.data.data || json.data || [];
      const varImgs = allImgs.filter(img => String(img.product_variant_id) === String(activePhotoVariantId));

      if (!varImgs.length) {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1; padding:20px 0;">No photos assigned to this variant yet. Upload one above.</div>';
        return;
      }

      grid.innerHTML = varImgs.map(img => `
        <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden; background:var(--bg-card); position:relative;">
          <img src="${resolveImageSrc(img.image_url)}" style="width:100%; height:110px; object-fit:cover; display:block;" alt="">
          <div style="padding:6px; text-align:center;">
            <button class="btn btn-danger btn-sm" style="width:100%; font-size:11px; padding:3px 6px;" onclick="deleteVariantImage(${img.id})">Delete</button>
          </div>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;">${e.message || 'Failed to load photos.'}</div>`;
    }
  }

  document.getElementById('uploadVarImagesBtn').addEventListener('click', async () => {
    const fileInput = document.getElementById('varImageFileInput');
    if (!fileInput.files || !fileInput.files.length) {
      showToast('Please select at least one photo.', true);
      return;
    }

    const formData = new FormData();
    [...fileInput.files].forEach(f => formData.append('images[]', f));
    formData.append('product_variant_id', activePhotoVariantId);
    const alt = document.getElementById('varImageAltInput').value.trim();
    if (alt) formData.append('alt_text', alt);

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo uploaded for variant!');
        fileInput.value = '';
        document.getElementById('varImageAltInput').value = '';
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Upload failed.', true);
      }
    } catch (e) {
      showToast('Error uploading photo.', true);
    }
  });

  async function deleteVariantImage(imageId) {
    if (!confirm('Are you sure you want to delete this variant photo?')) return;
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo deleted.');
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Failed to delete photo.', true);
      }
    } catch (e) {
      showToast('Error deleting photo.', true);
    }
  }

      } else showToast(json.message || 'Failed to update image.', true);
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  }

  async function deleteImage(imageId) {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Image deleted.');
        await loadImages();

  // ==================== CUSTOM ATTRIBUTES & VALUES ====================
  function toggleNewAttributeBox() {
    const box = document.getElementById('newAttributeBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newAttributeNameInput').focus();
  }

  function toggleNewValueBox() {
    const box = document.getElementById('newValueBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newValueNameInput').focus();
  }

  async function saveNewAttribute() {
    const input = document.getElementById('newAttributeNameInput');
    const name = input.value.trim();
    if (!name) { showToast('Please enter an attribute name.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attributes`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ name })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute.');

      showToast('Custom attribute created successfully!');
      input.value = '';
      toggleNewAttributeBox();

      await loadAttributeOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrAttributeSelect').value = json.data.id;
        populateValueDropdown();
      }
    } catch (e) {
      showToast(e.message || 'Error creating attribute.', true);
    }
  }

  async function saveNewValue() {
    const attrId = document.getElementById('attrAttributeSelect').value;
    if (!attrId) { showToast('Please select an attribute first.', true); return; }

    const input = document.getElementById('newValueNameInput');
    const value = input.value.trim();
    if (!value) { showToast('Please enter a value.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attribute-values`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ attribute_id: attrId, value })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute value.');

      showToast('Custom value created successfully!');
      input.value = '';
      toggleNewValueBox();

      await loadAttributeValueOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrValueSelect').value = json.data.id;
      }
    } catch (e) {
      showToast(e.message || 'Error creating value.', true);
    }
  }

  // Hook populateValueDropdown to show/hide "+ Create New" value link
  const originalPopulate = populateValueDropdown;
  populateValueDropdown = function() {
    originalPopulate();
    const attrSel = document.getElementById('attrAttributeSelect');
    const createValLink = document.getElementById('createNewValLink');
    if (attrSel.value) {
      createValLink.style.display = 'inline-block';
    } else {
      createValLink.style.display = 'none';
    }
  };

  // ==================== VARIANT PHOTOS MODAL ====================
  let activePhotoVariantId = null;

  async function openVariantImagesModal(variantId) {
    activePhotoVariantId = variantId;
    const v = findVariantById(variantId);
    document.getElementById('varPhotoSkuBadge').textContent = v ? (v.sku || ('Variant #' + v.id)) : '';
    document.getElementById('variantPhotosModal').classList.add('open');
    await loadVariantPhotos();
  }

  function closeVariantPhotosModal() {
    activePhotoVariantId = null;
    document.getElementById('variantPhotosModal').classList.remove('open');
    loadVariants(); // Refresh variant list to update photo thumbnail
  }

  async function loadVariantPhotos() {
    const grid = document.getElementById('varImagesGrid');
    grid.innerHTML = '<div class="loading-state">Loading...</div>';

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, { headers: authHeaders() });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load photos.');

      const allImgs = json.data.data || json.data || [];
      const varImgs = allImgs.filter(img => String(img.product_variant_id) === String(activePhotoVariantId));

      if (!varImgs.length) {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1; padding:20px 0;">No photos assigned to this variant yet. Upload one above.</div>';
        return;
      }

      grid.innerHTML = varImgs.map(img => `
        <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden; background:var(--bg-card); position:relative;">
          <img src="${resolveImageSrc(img.image_url)}" style="width:100%; height:110px; object-fit:cover; display:block;" alt="">
          <div style="padding:6px; text-align:center;">
            <button class="btn btn-danger btn-sm" style="width:100%; font-size:11px; padding:3px 6px;" onclick="deleteVariantImage(${img.id})">Delete</button>
          </div>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;">${e.message || 'Failed to load photos.'}</div>`;
    }
  }

  document.getElementById('uploadVarImagesBtn').addEventListener('click', async () => {
    const fileInput = document.getElementById('varImageFileInput');
    if (!fileInput.files || !fileInput.files.length) {
      showToast('Please select at least one photo.', true);
      return;
    }

    const formData = new FormData();
    [...fileInput.files].forEach(f => formData.append('images[]', f));
    formData.append('product_variant_id', activePhotoVariantId);
    const alt = document.getElementById('varImageAltInput').value.trim();
    if (alt) formData.append('alt_text', alt);

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo uploaded for variant!');
        fileInput.value = '';
        document.getElementById('varImageAltInput').value = '';
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Upload failed.', true);
      }
    } catch (e) {
      showToast('Error uploading photo.', true);
    }
  });

  async function deleteVariantImage(imageId) {
    if (!confirm('Are you sure you want to delete this variant photo?')) return;
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo deleted.');
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Failed to delete photo.', true);
      }
    } catch (e) {
      showToast('Error deleting photo.', true);
    }
  }

      } else showToast(json.message || 'Failed to delete image.', true);
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  }

  document.getElementById('uploadImagesBtn').addEventListener('click', async () => {
    const filesInput = document.getElementById('imageFileInput');
    if (!filesInput.files.length) {
      showToast('Please choose at least one image.', true);
      return;
    }

    const formData = new FormData();
    [...filesInput.files].forEach(f => formData.append('images[]', f));
    const alt = document.getElementById('imageAltInput').value.trim();
    if (alt) formData.append('alt_text', alt);

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        method: 'POST',
        headers: authHeaders(false),
        body: formData,
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Images uploaded successfully.');
        filesInput.value = '';
        document.getElementById('imageAltInput').value = '';
        await loadImages();

  // ==================== CUSTOM ATTRIBUTES & VALUES ====================
  function toggleNewAttributeBox() {
    const box = document.getElementById('newAttributeBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newAttributeNameInput').focus();
  }

  function toggleNewValueBox() {
    const box = document.getElementById('newValueBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newValueNameInput').focus();
  }

  async function saveNewAttribute() {
    const input = document.getElementById('newAttributeNameInput');
    const name = input.value.trim();
    if (!name) { showToast('Please enter an attribute name.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attributes`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ name })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute.');

      showToast('Custom attribute created successfully!');
      input.value = '';
      toggleNewAttributeBox();

      await loadAttributeOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrAttributeSelect').value = json.data.id;
        populateValueDropdown();
      }
    } catch (e) {
      showToast(e.message || 'Error creating attribute.', true);
    }
  }

  async function saveNewValue() {
    const attrId = document.getElementById('attrAttributeSelect').value;
    if (!attrId) { showToast('Please select an attribute first.', true); return; }

    const input = document.getElementById('newValueNameInput');
    const value = input.value.trim();
    if (!value) { showToast('Please enter a value.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attribute-values`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ attribute_id: attrId, value })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute value.');

      showToast('Custom value created successfully!');
      input.value = '';
      toggleNewValueBox();

      await loadAttributeValueOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrValueSelect').value = json.data.id;
      }
    } catch (e) {
      showToast(e.message || 'Error creating value.', true);
    }
  }

  // Hook populateValueDropdown to show/hide "+ Create New" value link
  const originalPopulate = populateValueDropdown;
  populateValueDropdown = function() {
    originalPopulate();
    const attrSel = document.getElementById('attrAttributeSelect');
    const createValLink = document.getElementById('createNewValLink');
    if (attrSel.value) {
      createValLink.style.display = 'inline-block';
    } else {
      createValLink.style.display = 'none';
    }
  };

  // ==================== VARIANT PHOTOS MODAL ====================
  let activePhotoVariantId = null;

  async function openVariantImagesModal(variantId) {
    activePhotoVariantId = variantId;
    const v = findVariantById(variantId);
    document.getElementById('varPhotoSkuBadge').textContent = v ? (v.sku || ('Variant #' + v.id)) : '';
    document.getElementById('variantPhotosModal').classList.add('open');
    await loadVariantPhotos();
  }

  function closeVariantPhotosModal() {
    activePhotoVariantId = null;
    document.getElementById('variantPhotosModal').classList.remove('open');
    loadVariants(); // Refresh variant list to update photo thumbnail
  }

  async function loadVariantPhotos() {
    const grid = document.getElementById('varImagesGrid');
    grid.innerHTML = '<div class="loading-state">Loading...</div>';

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, { headers: authHeaders() });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load photos.');

      const allImgs = json.data.data || json.data || [];
      const varImgs = allImgs.filter(img => String(img.product_variant_id) === String(activePhotoVariantId));

      if (!varImgs.length) {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1; padding:20px 0;">No photos assigned to this variant yet. Upload one above.</div>';
        return;
      }

      grid.innerHTML = varImgs.map(img => `
        <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden; background:var(--bg-card); position:relative;">
          <img src="${resolveImageSrc(img.image_url)}" style="width:100%; height:110px; object-fit:cover; display:block;" alt="">
          <div style="padding:6px; text-align:center;">
            <button class="btn btn-danger btn-sm" style="width:100%; font-size:11px; padding:3px 6px;" onclick="deleteVariantImage(${img.id})">Delete</button>
          </div>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;">${e.message || 'Failed to load photos.'}</div>`;
    }
  }

  document.getElementById('uploadVarImagesBtn').addEventListener('click', async () => {
    const fileInput = document.getElementById('varImageFileInput');
    if (!fileInput.files || !fileInput.files.length) {
      showToast('Please select at least one photo.', true);
      return;
    }

    const formData = new FormData();
    [...fileInput.files].forEach(f => formData.append('images[]', f));
    formData.append('product_variant_id', activePhotoVariantId);
    const alt = document.getElementById('varImageAltInput').value.trim();
    if (alt) formData.append('alt_text', alt);

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo uploaded for variant!');
        fileInput.value = '';
        document.getElementById('varImageAltInput').value = '';
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Upload failed.', true);
      }
    } catch (e) {
      showToast('Error uploading photo.', true);
    }
  });

  async function deleteVariantImage(imageId) {
    if (!confirm('Are you sure you want to delete this variant photo?')) return;
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo deleted.');
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Failed to delete photo.', true);
      }
    } catch (e) {
      showToast('Error deleting photo.', true);
    }
  }

      } else {
        showToast(json.message || 'Failed to upload images.', true);
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  // =====================================================================
  // Variants
  // =====================================================================
  async function loadVariants() {
    const tbody = document.getElementById('variantsTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="loading-state">Loading variants...</td></tr>';
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!json.success) {
        tbody.innerHTML = `<tr><td colspan="6" class="empty-state">${json.message || 'Failed to load variants.'}</td></tr>`;
        return;
      }
      variants = json.data.data || json.data || [];
      renderVariants();
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Something went wrong.</td></tr>';
    }
  }

  function renderVariants() {
    const tbody = document.getElementById('variantsTableBody');
    if (!variants.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No variants added yet.</td></tr>';
      return;
    }

    tbody.innerHTML = variants.map(v => {
      const vImg = v.primary_image || (v.images && v.images.length ? v.images[0] : null);
      const thumbHtml = vImg ?
        `<img src="${resolveImageSrc(vImg.image_url)}" style="width:38px; height:38px; object-fit:cover; border-radius:6px; border:1px solid var(--border-color); display:block;" alt="">` :
        `<div style="width:38px; height:38px; background:var(--bg-card-hover); border-radius:6px; border:1px dashed var(--border-color-strong); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:11px;">—</div>`;

      return `
            <tr>
                <td>${thumbHtml}</td>
                <td>${v.sku || '—'}</td>
                <td>${v.barcode || '—'}</td>
                <td>₹${Number(v.price).toLocaleString('en-IN')}</td>
                <td>${v.sale_price ? '₹' + Number(v.sale_price).toLocaleString('en-IN') : '—'}</td>
                <td>${v.is_active ? '<span class="badge status-active">Active</span>' : '<span class="badge status-inactive">Inactive</span>'}</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-outline btn-sm" onclick="openVariantImagesModal(${v.id})" title="Manage variant photos">📷 Photos</button>
                        <button class="btn btn-outline btn-sm" onclick="openAttributesModal(${v.id})">Attributes</button>
                        <button class="btn btn-outline btn-sm" onclick="openInventoryModal(${v.id})">Inventory</button>
                        <button class="btn btn-outline btn-sm" onclick="openVariantFormModal(${v.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="openDeleteVariantModal(${v.id})">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
  }

  function findVariantById(id) {
    return variants.find(v => v.id === id);
  }

  function openVariantFormModal(id = null) {
    editingVariantId = id;
    document.getElementById('variantForm').reset();
    document.getElementById('variantFormError').style.display = 'none';
    document.getElementById('variantActiveGroup').style.display = id ? 'block' : 'none';

    if (id) {
      const v = findVariantById(id);
      document.getElementById('variantFormTitle').textContent = 'Edit Variant';
      if (v) {
        document.getElementById('variantBarcode').value = v.barcode || '';
        document.getElementById('variantPrice').value = v.price || '';
        document.getElementById('variantSalePrice').value = v.sale_price || '';
        document.getElementById('variantWeight').value = v.weight || '';
        document.getElementById('variantLength').value = v.length || '';
        document.getElementById('variantWidth').value = v.width || '';
        document.getElementById('variantHeight').value = v.height || '';
        document.getElementById('variantIsActive').checked = !!v.is_active;
        document.getElementById('variantActiveLabel').textContent = v.is_active ? 'Active' : 'Inactive';
      }
    } else {
      document.getElementById('variantFormTitle').textContent = 'Add Variant';
    }
    document.getElementById('variantFormModal').classList.add('open');
  }

  function closeVariantFormModal() {
    editingVariantId = null;
    document.getElementById('variantFormModal').classList.remove('open');
  }

  document.getElementById('variantIsActive').addEventListener('change', (e) => {
    document.getElementById('variantActiveLabel').textContent = e.target.checked ? 'Active' : 'Inactive';
  });

  document.getElementById('saveVariantBtn').addEventListener('click', async () => {
    const price = document.getElementById('variantPrice').value;
    if (!price) {
      document.getElementById('variantFormError').textContent = 'Price is required.';
      document.getElementById('variantFormError').style.display = 'block';
      return;
    }
    document.getElementById('variantFormError').style.display = 'none';

    const payload = {
      barcode: document.getElementById('variantBarcode').value.trim() || null,
      price: parseFloat(price),
      sale_price: document.getElementById('variantSalePrice').value ? parseFloat(document.getElementById('variantSalePrice').value) : null,
      weight: document.getElementById('variantWeight').value ? parseFloat(document.getElementById('variantWeight').value) : null,
      length: document.getElementById('variantLength').value ? parseFloat(document.getElementById('variantLength').value) : null,
      width: document.getElementById('variantWidth').value ? parseFloat(document.getElementById('variantWidth').value) : null,
      height: document.getElementById('variantHeight').value ? parseFloat(document.getElementById('variantHeight').value) : null,
    };

    const isEdit = !!editingVariantId;
    if (isEdit) payload.is_active = document.getElementById('variantIsActive').checked;

    const url = isEdit ? `${API_BASE}/products/${PRODUCT_ID}/variants/${editingVariantId}` : `${API_BASE}/products/${PRODUCT_ID}/variants`;

    try {
      const res = await fetch(url, {
        method: isEdit ? 'PUT' : 'POST',
        headers: authHeaders(),
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || (isEdit ? 'Variant updated successfully.' : 'Variant created successfully.'));
        closeVariantFormModal();
        await loadVariants();
      } else {
        document.getElementById('variantFormError').textContent = json.message || 'Failed to save variant.';
        document.getElementById('variantFormError').style.display = 'block';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  function openDeleteVariantModal(id) {
    deleteVariantTargetId = id;
    const v = findVariantById(id);
    document.getElementById('deleteVariantText').textContent = v ?
      `Delete variant "${v.sku}"? This cannot be undone.` :
      'Are you sure you want to delete this variant?';
    document.getElementById('deleteVariantModal').classList.add('open');
  }

  function closeDeleteVariantModal() {
    deleteVariantTargetId = null;
    document.getElementById('deleteVariantModal').classList.remove('open');
  }
  document.getElementById('confirmDeleteVariantBtn').addEventListener('click', async () => {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${deleteVariantTargetId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Variant deleted successfully.');
        closeDeleteVariantModal();
        await loadVariants();
      } else {
        showToast(json.message || 'Failed to delete variant.', true);
        closeDeleteVariantModal();
      }
    } catch (e) {
      showToast('Something went wrong.', true);
      closeDeleteVariantModal();
    }
  });

  // =====================================================================
  // Variant Attributes
  // Confirmed APIs:
  //   GET /api/vendor/attributes        -> use only status === "active"
  //   GET /api/vendor/attribute-values  -> use only status === "active",
  //                                         filter by attribute_id === selected attribute's id
  //   GET    /api/vendor/products/{p}/variants/{v}/attributes             (assigned list)
  //   POST   /api/vendor/products/{p}/variants/{v}/attributes             (add)
  //   DELETE /api/vendor/products/{p}/variants/{v}/attributes/{attrRowId} (remove)
  // No manual attribute_id / attribute_value_id fallback.
  // =====================================================================
  let allAttributes = [];
  let allAttributeValues = [];
  let attributesLoaded = false;
  let attributeValuesLoaded = false;
  let assignedAttributeValueIds = []; // for duplicate prevention on the currently open variant

  async function loadAttributeOptions() {
    const sel = document.getElementById('attrAttributeSelect');
    const errBox = document.getElementById('attrAttributeError');
    errBox.style.display = 'none';
    sel.disabled = true;
    sel.innerHTML = '<option value="">Loading attributes...</option>';
    attributesLoaded = false;

    try {
      const res = await fetch(`${API_BASE}/attributes`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load attributes.');

      const raw = json.data.data || json.data || [];
      allAttributes = raw.filter(a => a.status === 'active');

      sel.innerHTML = '<option value="">Select attribute</option>' +
        allAttributes.map(a => `<option value="${a.id}">${a.name}</option>`).join('');
      sel.disabled = false;
      attributesLoaded = true;
    } catch (e) {
      sel.innerHTML = '<option value="">Select attribute</option>';
      sel.disabled = true;
      errBox.style.display = 'flex';
    }
  }

  async function loadAttributeValueOptions() {
    const errBox = document.getElementById('attrValueError');
    errBox.style.display = 'none';
    attributeValuesLoaded = false;

    try {
      const res = await fetch(`${API_BASE}/attribute-values`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load attribute values.');

      const raw = json.data.data || json.data || [];
      allAttributeValues = raw.filter(v => v.status === 'active');
      attributeValuesLoaded = true;
      populateValueDropdown();
    } catch (e) {
      const sel = document.getElementById('attrValueSelect');
      sel.innerHTML = '<option value="">Select attribute first</option>';
      sel.disabled = true;
      errBox.style.display = 'flex';
    }
  }

  function populateValueDropdown() {
    const attrSel = document.getElementById('attrAttributeSelect');
    const valSel = document.getElementById('attrValueSelect');
    const selectedAttributeId = attrSel.value;

    if (!selectedAttributeId) {
      valSel.innerHTML = '<option value="">Select attribute first</option>';
      valSel.disabled = true;
      return;
    }

    if (!attributeValuesLoaded) {
      valSel.innerHTML = '<option value="">Loading values...</option>';
      valSel.disabled = true;
      return;
    }

    const filtered = allAttributeValues.filter(v => String(v.attribute_id) === String(selectedAttributeId));

    if (!filtered.length) {
      valSel.innerHTML = '<option value="">No values available</option>';
      valSel.disabled = true;
      return;
    }

    valSel.innerHTML = '<option value="">Select value</option>' +
      filtered
      .slice()
      .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
      .map(v => `<option value="${v.id}">${v.value}</option>`).join('');
    valSel.disabled = false;
  }

  document.getElementById('attrAttributeSelect').addEventListener('change', () => {
    document.getElementById('attrFormError').style.display = 'none';
    populateValueDropdown();
  });

  async function openAttributesModal(variantId) {
    activeVariantId = variantId;
    document.getElementById('attributesModal').classList.add('open');
    document.getElementById('attrFormError').style.display = 'none';
    document.getElementById('attrAttributeSelect').value = '';
    document.getElementById('attrValueSelect').innerHTML = '<option value="">Select attribute first</option>';
    document.getElementById('attrValueSelect').disabled = true;

    if (!attributesLoaded) await loadAttributeOptions();
    if (!attributeValuesLoaded) await loadAttributeValueOptions();

    await loadVariantAttributes();
  }

  function closeAttributesModal() {
    activeVariantId = null;
    document.getElementById('attributesModal').classList.remove('open');
  }

  async function loadVariantAttributes() {
    const list = document.getElementById('attributesList');
    list.innerHTML = '<div class="loading-state">Loading...</div>';
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${activeVariantId}/attributes`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!json.success) {
        list.innerHTML = `<div class="empty-state">${json.message || 'Failed to load attributes.'}</div>`;
        assignedAttributeValueIds = [];
        return;
      }

      const items = json.data.data || json.data || [];
      assignedAttributeValueIds = items.map(a => a.attribute_value_id ?? a.attribute_value?.id).filter(Boolean);

      if (!items.length) {
        list.innerHTML = '<div class="empty-state">No attributes assigned yet.</div>';
        return;
      }

      list.innerHTML = items.map(a => `
                <div class="attr-value-row">
                    <span class="attr-label"><b>${a.attribute_value?.attribute?.name || ''}</b>: ${a.attribute_value?.value || ''}</span>
                    <button class="btn btn-danger btn-sm" onclick="deleteVariantAttribute(${a.id})">Remove</button>
                </div>
            `).join('');
    } catch (e) {
      list.innerHTML = '<div class="empty-state">Something went wrong.</div>';
      assignedAttributeValueIds = [];
    }
  }

  document.getElementById('addAttrValueBtn').addEventListener('click', async () => {
    const errBox = document.getElementById('attrFormError');
    errBox.style.display = 'none';

    const attributeId = document.getElementById('attrAttributeSelect').value;
    const attributeValueId = document.getElementById('attrValueSelect').value;

    if (!attributeId || !attributeValueId) {
      errBox.textContent = 'Please select both an attribute and a value.';
      errBox.style.display = 'block';
      return;
    }

    if (assignedAttributeValueIds.map(String).includes(String(attributeValueId))) {
      errBox.textContent = 'This attribute value is already assigned to this variant.';
      errBox.style.display = 'block';
      return;
    }

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${activeVariantId}/attributes`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({
          attribute_value_id: attributeValueId
        }),
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Attribute value added.');
        document.getElementById('attrAttributeSelect').value = '';
        document.getElementById('attrValueSelect').innerHTML = '<option value="">Select attribute first</option>';
        document.getElementById('attrValueSelect').disabled = true;
        await loadVariantAttributes();
      } else {
        errBox.textContent = json.message || 'Failed to add attribute value.';
        errBox.style.display = 'block';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  async function deleteVariantAttribute(attributeRowId) {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${activeVariantId}/attributes/${attributeRowId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Attribute value removed.');
        await loadVariantAttributes();
      } else showToast(json.message || 'Failed to remove attribute value.', true);
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  }

  // =====================================================================
  // Variant Inventory
  // =====================================================================
  let inventoryExists = false;

  async function openInventoryModal(variantId) {
    activeVariantId = variantId;
    document.getElementById('inventoryModal').classList.add('open');
    document.getElementById('inventoryFormError').style.display = 'none';
    document.getElementById('inventoryReadonly').style.display = 'none';
    document.getElementById('inventoryForm').reset();

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${variantId}/inventory`, {
        headers: authHeaders()
      });
      const json = await res.json();
      if (!json.success) {
        showToast(json.message || 'Failed to load inventory.', true);
        return;
      }

      if (json.data) {
        inventoryExists = true;
        document.getElementById('invQuantity').value = json.data.quantity ?? '';
        document.getElementById('invReserved').value = json.data.reserved_quantity ?? 0;
        document.getElementById('invMin').value = json.data.minimum_stock ?? '';
        document.getElementById('invMax').value = json.data.maximum_stock ?? '';
        document.getElementById('inventoryReadonly').style.display = 'flex';
        document.getElementById('inventoryReadonly').innerHTML =
          `<div class="kv-row"><span class="k">Available Quantity</span><span class="v">${json.data.available_quantity}</span></div>`;
        document.getElementById('saveInventoryBtn').textContent = 'Update Inventory';
      } else {
        inventoryExists = false;
        document.getElementById('invReserved').value = 0;
        document.getElementById('saveInventoryBtn').textContent = 'Create Inventory';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  }

  function closeInventoryModal() {
    activeVariantId = null;
    document.getElementById('inventoryModal').classList.remove('open');
  }

  document.getElementById('saveInventoryBtn').addEventListener('click', async () => {
    const quantity = document.getElementById('invQuantity').value;
    if (quantity === '') {
      document.getElementById('inventoryFormError').textContent = 'Quantity is required.';
      document.getElementById('inventoryFormError').style.display = 'block';
      return;
    }
    document.getElementById('inventoryFormError').style.display = 'none';

    const payload = {
      quantity: parseInt(quantity, 10),
      reserved_quantity: parseInt(document.getElementById('invReserved').value || 0, 10),
      minimum_stock: document.getElementById('invMin').value ? parseInt(document.getElementById('invMin').value, 10) : null,
      maximum_stock: document.getElementById('invMax').value ? parseInt(document.getElementById('invMax').value, 10) : null,
    };

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/variants/${activeVariantId}/inventory`, {
        method: inventoryExists ? 'PUT' : 'POST',
        headers: authHeaders(),
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (json.success) {
        showToast(json.message || 'Inventory saved successfully.');
        closeInventoryModal();
      } else {
        // Covers "Reserved quantity cannot exceed total quantity.", max/min stock validation, duplicate inventory, etc.
        const firstError = json.errors ? Object.values(json.errors).flat()[0] : null;
        document.getElementById('inventoryFormError').textContent = firstError || json.message || 'Failed to save inventory.';
        document.getElementById('inventoryFormError').style.display = 'block';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  // =====================================================================
  // Submit for Approval
  // =====================================================================
  document.getElementById('submitApprovalBtn').addEventListener('click', () => {
    document.getElementById('submitErrorsBox').style.display = 'none';
    document.getElementById('submitModal').classList.add('open');
  });

  function closeSubmitModal() {
    document.getElementById('submitModal').classList.remove('open');
  }

  document.getElementById('confirmSubmitBtn').addEventListener('click', async () => {
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/submit`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({})
      });
      const json = await res.json();

      if (json.success) {
        showToast(json.message || 'Product submitted for approval successfully.');
        closeSubmitModal();
        product = {
          ...product,
          ...json.data
        };
        renderProductHeader();
      } else {
        const box = document.getElementById('submitErrorsBox');
        const messages = json.errors ? Object.values(json.errors).flat() : [json.message || 'Submission failed.'];
        box.innerHTML = `<strong>${json.message || 'Please fix the following before submitting:'}</strong><ul>${messages.map(m => `<li>${m}</li>`).join('')}</ul>`;
        box.style.display = 'block';
      }
    } catch (e) {
      showToast('Something went wrong.', true);
    }
  });

  // Init
  loadCategoryOptions().then(() => {
    if (product) fillCategoryBrandFields();
  });
  loadBrandOptions().then(() => {
    if (product) fillCategoryBrandFields();
  });
  loadProduct();
  loadImages();

  // ==================== CUSTOM ATTRIBUTES & VALUES ====================
  function toggleNewAttributeBox() {
    const box = document.getElementById('newAttributeBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newAttributeNameInput').focus();
  }

  function toggleNewValueBox() {
    const box = document.getElementById('newValueBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') document.getElementById('newValueNameInput').focus();
  }

  async function saveNewAttribute() {
    const input = document.getElementById('newAttributeNameInput');
    const name = input.value.trim();
    if (!name) { showToast('Please enter an attribute name.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attributes`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ name })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute.');

      showToast('Custom attribute created successfully!');
      input.value = '';
      toggleNewAttributeBox();

      await loadAttributeOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrAttributeSelect').value = json.data.id;
        populateValueDropdown();
      }
    } catch (e) {
      showToast(e.message || 'Error creating attribute.', true);
    }
  }

  async function saveNewValue() {
    const attrId = document.getElementById('attrAttributeSelect').value;
    if (!attrId) { showToast('Please select an attribute first.', true); return; }

    const input = document.getElementById('newValueNameInput');
    const value = input.value.trim();
    if (!value) { showToast('Please enter a value.', true); return; }

    try {
      const res = await fetch(`${API_BASE}/attribute-values`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ attribute_id: attrId, value })
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to save attribute value.');

      showToast('Custom value created successfully!');
      input.value = '';
      toggleNewValueBox();

      await loadAttributeValueOptions();
      if (json.data && json.data.id) {
        document.getElementById('attrValueSelect').value = json.data.id;
      }
    } catch (e) {
      showToast(e.message || 'Error creating value.', true);
    }
  }

  // Hook populateValueDropdown to show/hide "+ Create New" value link
  const originalPopulate = populateValueDropdown;
  populateValueDropdown = function() {
    originalPopulate();
    const attrSel = document.getElementById('attrAttributeSelect');
    const createValLink = document.getElementById('createNewValLink');
    if (attrSel.value) {
      createValLink.style.display = 'inline-block';
    } else {
      createValLink.style.display = 'none';
    }
  };

  // ==================== VARIANT PHOTOS MODAL ====================
  let activePhotoVariantId = null;

  async function openVariantImagesModal(variantId) {
    activePhotoVariantId = variantId;
    const v = findVariantById(variantId);
    document.getElementById('varPhotoSkuBadge').textContent = v ? (v.sku || ('Variant #' + v.id)) : '';
    document.getElementById('variantPhotosModal').classList.add('open');
    await loadVariantPhotos();
  }

  function closeVariantPhotosModal() {
    activePhotoVariantId = null;
    document.getElementById('variantPhotosModal').classList.remove('open');
    loadVariants(); // Refresh variant list to update photo thumbnail
  }

  async function loadVariantPhotos() {
    const grid = document.getElementById('varImagesGrid');
    grid.innerHTML = '<div class="loading-state">Loading...</div>';

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, { headers: authHeaders() });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load photos.');

      const allImgs = json.data.data || json.data || [];
      const varImgs = allImgs.filter(img => String(img.product_variant_id) === String(activePhotoVariantId));

      if (!varImgs.length) {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1; padding:20px 0;">No photos assigned to this variant yet. Upload one above.</div>';
        return;
      }

      grid.innerHTML = varImgs.map(img => `
        <div style="border:1px solid var(--border-color); border-radius:8px; overflow:hidden; background:var(--bg-card); position:relative;">
          <img src="${resolveImageSrc(img.image_url)}" style="width:100%; height:110px; object-fit:cover; display:block;" alt="">
          <div style="padding:6px; text-align:center;">
            <button class="btn btn-danger btn-sm" style="width:100%; font-size:11px; padding:3px 6px;" onclick="deleteVariantImage(${img.id})">Delete</button>
          </div>
        </div>
      `).join('');
    } catch (e) {
      grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;">${e.message || 'Failed to load photos.'}</div>`;
    }
  }

  document.getElementById('uploadVarImagesBtn').addEventListener('click', async () => {
    const fileInput = document.getElementById('varImageFileInput');
    if (!fileInput.files || !fileInput.files.length) {
      showToast('Please select at least one photo.', true);
      return;
    }

    const formData = new FormData();
    [...fileInput.files].forEach(f => formData.append('images[]', f));
    formData.append('product_variant_id', activePhotoVariantId);
    const alt = document.getElementById('varImageAltInput').value.trim();
    if (alt) formData.append('alt_text', alt);

    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images`, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo uploaded for variant!');
        fileInput.value = '';
        document.getElementById('varImageAltInput').value = '';
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Upload failed.', true);
      }
    } catch (e) {
      showToast('Error uploading photo.', true);
    }
  });

  async function deleteVariantImage(imageId) {
    if (!confirm('Are you sure you want to delete this variant photo?')) return;
    try {
      const res = await fetch(`${API_BASE}/products/${PRODUCT_ID}/images/${imageId}`, {
        method: 'DELETE',
        headers: authHeaders()
      });
      const json = await res.json();
      if (json.success) {
        showToast('Photo deleted.');
        await loadVariantPhotos();
      } else {
        showToast(json.message || 'Failed to delete photo.', true);
      }
    } catch (e) {
      showToast('Error deleting photo.', true);
    }
  }

  loadVariants();
</script>
@endpush

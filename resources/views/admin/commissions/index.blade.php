@extends('layouts.admin')

@section('title', 'Commission Settings')

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

    .commission-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

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
    }
    .modal-backdrop.show { display: flex; }
    .modal-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        padding: 26px;
    }
</style>
@endpush

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1>Category Commission Matrix</h1>
        <p>Set platform-wide default commission and category-specific percentage deductions.</p>
    </div>
    <div class="header-right">
        <button class="btn btn-terracotta" onclick="openAddRuleModal()">+ Add Category Commission Rule</button>
    </div>
</div>

<div class="commission-box">
    <div>
        <h3 style="font-size: 18px; font-weight: 700;">Global Default Commission</h3>
        <p style="font-size: 13.5px; color: var(--text-secondary); margin-top: 4px;">Applied to products in categories without a specific override rule.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
        <input type="number" id="globalRateInput" style="width: 100px; font-size: 16px; font-weight: 700; text-align: center;" step="0.1" min="0" max="100">
        <span style="font-size: 16px; font-weight: 700;">%</span>
        <button class="btn btn-outline" onclick="saveGlobalRate()">Save Global Rate</button>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Rule Title</th>
                <th>Target Category</th>
                <th>Commission Rate</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="commissionTableBody">
            <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Loading rules...</td></tr>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="ruleModal">
    <div class="modal-box">
        <h3 style="font-size: 18px; margin-bottom: 16px;">Add / Edit Commission Rule</h3>
        <form id="ruleForm" onsubmit="handleRuleSubmit(event)">
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 6px;">Category</label>
                <select id="ruleCategory" style="width: 100%;" required>
                    <option value="">Select Category</option>
                </select>
            </div>
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 6px;">Commission Rate (%)</label>
                <input type="number" id="ruleRate" style="width: 100%;" step="0.1" min="0" max="100" required placeholder="e.g. 12.5">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12.5px; font-weight: 600; margin-bottom: 6px;">Rule Title / Notes</label>
                <input type="text" id="ruleTitle" style="width: 100%;" placeholder="e.g. Apparel standard rate">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-terracotta">Save Rule</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let categoriesList = [];

    async function loadCommissions() {
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/commissions', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            if (data.success) {
                document.getElementById('globalRateInput').value = data.data.default_rate;
                categoriesList = data.data.categories;
                populateCategorySelect();
                renderRules(data.data.rules);
            }
        } catch (e) { console.error(e); }
    }

    function populateCategorySelect() {
        const select = document.getElementById('ruleCategory');
        select.innerHTML = '<option value="">Select Category</option>' + categoriesList.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    function renderRules(rules) {
        const tbody = document.getElementById('commissionTableBody');
        const categoryRules = rules.filter(r => r.category_id);
        if (categoryRules.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No category override rules defined yet. All categories use the Global Default Rate.</td></tr>`;
            return;
        }

        tbody.innerHTML = categoryRules.map(r => `
            <tr>
                <td><strong>${r.title || 'Commission Rule'}</strong></td>
                <td><span style="font-weight: 600; color: var(--terracotta-dark);">${r.category?.name || 'All'}</span></td>
                <td><strong style="font-size: 15px;">${r.commission_rate}%</strong></td>
                <td>${r.type}</td>
                <td>
                    <span class="badge" style="background: var(--success-soft); color: var(--success);">Active</span>
                </td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="deleteRule(${r.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    async function saveGlobalRate() {
        const rate = document.getElementById('globalRateInput').value;
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch('/api/admin/commissions', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ commission_rate: rate, title: 'Global Fallback Commission' })
            });
            const data = await res.json();
            if (data.success) {
                alert('Global default commission rate saved successfully.');
                loadCommissions();
            }
        } catch (e) { console.error(e); }
    }

    async function handleRuleSubmit(e) {
        e.preventDefault();
        const category_id = document.getElementById('ruleCategory').value;
        const commission_rate = document.getElementById('ruleRate').value;
        const title = document.getElementById('ruleTitle').value;
        const token = localStorage.getItem('meeta_token');

        try {
            const res = await fetch('/api/admin/commissions', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id, commission_rate, title })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                closeModal();
                loadCommissions();
            }
        } catch (e) { console.error(e); }
    }

    async function deleteRule(id) {
        if (!confirm('Are you sure you want to delete this commission rule? Category will revert to global default.')) return;
        const token = localStorage.getItem('meeta_token');
        try {
            const res = await fetch(`/api/admin/commissions/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                loadCommissions();
            }
        } catch (e) { console.error(e); }
    }

    function openAddRuleModal() {
        document.getElementById('ruleForm').reset();
        document.getElementById('ruleModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('ruleModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', loadCommissions);
</script>
@endpush

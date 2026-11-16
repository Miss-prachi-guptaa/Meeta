@extends('vendor.layouts.app')

@section('title', 'Orders')

@push('styles')
<style>
  .orders-header { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:26px; }
  .orders-header h1 { font-size:29px; margin-bottom:5px; }
  .orders-header p { color:var(--text-secondary); }
  .refresh-btn svg { width:16px; height:16px; }
  .order-stats { display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:12px; margin-bottom:22px; }
  .order-stat { padding:16px; cursor:pointer; transition:.18s ease; box-shadow:none; }
  .order-stat:hover,.order-stat.active { border-color:var(--terracotta); transform:translateY(-1px); }
  .order-stat.active { background:var(--terracotta-soft); }
  .order-stat-label { color:var(--text-muted); font-size:11px; font-weight:700; letter-spacing:.45px; text-transform:uppercase; white-space:nowrap; }
  .order-stat-value { font-family:'Fraunces',serif; font-size:25px; font-weight:700; margin-top:4px; }
  .orders-toolbar { padding:16px; margin-bottom:18px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .search-wrap { position:relative; flex:1; min-width:240px; }
  .search-wrap svg { position:absolute; left:13px; top:50%; transform:translateY(-50%); width:17px; color:var(--text-muted); }
  .search-wrap input { width:100%; padding-left:40px; }
  .orders-toolbar select { min-width:180px; }
  .table-card { overflow:hidden; }
  .table-wrap { overflow-x:auto; }
  table { width:100%; border-collapse:collapse; min-width:920px; }
  th { padding:13px 18px; text-align:left; background:#f8f0e6; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; font-size:11px; }
  td { padding:17px 18px; border-top:1px solid var(--border-color); vertical-align:middle; }
  tbody tr { transition:background .15s; }
  tbody tr:hover { background:#fffbf6; }
  .order-number { font-weight:700; color:var(--terracotta-dark); }
  .muted { color:var(--text-muted); font-size:12px; margin-top:3px; }
  .customer-name { font-weight:600; }
  .amount { font-weight:700; white-space:nowrap; }
  .status-badge { display:inline-flex; align-items:center; gap:6px; border-radius:999px; padding:5px 10px; font-size:11px; font-weight:700; text-transform:capitalize; white-space:nowrap; }
  .status-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
  .status-confirmed { color:#9a6411; background:#fff0cf; }
  .status-processing { color:#355e9a; background:#e5eefb; }
  .status-ready_to_ship { color:#835ca3; background:#f0e5f8; }
  .status-shipped { color:#176b77; background:#dff2f3; }
  .status-delivered { color:var(--success); background:var(--success-soft); }
  .status-cancelled { color:var(--danger); background:var(--danger-soft); }
  .table-empty { text-align:center; padding:65px 20px; color:var(--text-muted); }
  .table-empty svg { width:45px; height:45px; margin-bottom:12px; opacity:.55; }
  .table-footer { padding:14px 18px; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; color:var(--text-muted); font-size:12px; }
  .pagination { display:flex; gap:6px; }
  .pagination button { border:1px solid var(--border-color-strong); background:#fff; color:var(--text-primary); border-radius:8px; min-width:34px; height:34px; cursor:pointer; }
  .pagination button:disabled { opacity:.45; cursor:not-allowed; }
  .toast { position:fixed; right:24px; bottom:24px; background:var(--text-primary); color:#fff; padding:12px 18px; border-radius:10px; opacity:0; transform:translateY(12px); pointer-events:none; transition:.2s; z-index:300; }
  .toast.show { opacity:1; transform:none; }
  @media(max-width:1100px){ .order-stats{grid-template-columns:repeat(3,1fr);} }
  @media(max-width:600px){ .orders-header{align-items:flex-start;flex-direction:column}.order-stats{grid-template-columns:repeat(2,1fr)}.refresh-btn{width:100%}.orders-toolbar select{width:100%}.table-footer{align-items:flex-start;gap:12px;flex-direction:column} }
</style>
@endpush

@section('content')
<header class="orders-header">
  <div><h1>Orders</h1><p>Accept new orders, prepare packages and manage delivery.</p></div>
  <button class="btn btn-outline refresh-btn" id="refreshBtn" type="button">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"/></svg> Refresh
  </button>
</header>

<section class="order-stats" id="orderStats"></section>

<section class="card orders-toolbar">
  <div class="search-wrap">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    <input id="searchInput" type="search" placeholder="Search order number...">
  </div>
  <select id="statusFilter" aria-label="Filter by status">
    <option value="">All order statuses</option><option value="confirmed">New orders</option><option value="processing">Processing</option>
    <option value="ready_to_ship">Ready to ship</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option><option value="cancelled">Cancelled</option>
  </select>
</section>

<section class="card table-card">
  <div class="table-wrap"><table>
    <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Amount</th><th>Payment</th><th>Status</th><th></th></tr></thead>
    <tbody id="ordersBody"><tr><td colspan="7" class="table-empty">Loading orders…</td></tr></tbody>
  </table></div>
  <div class="table-footer"><span id="tableSummary">Loading…</span><div class="pagination"><button id="prevBtn">←</button><button id="nextBtn">→</button></div></div>
</section>
<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
  const API_BASE = @json(url('/api/vendor'));
  const ORDER_PAGE = @json(url('/vendor/orders'));
  const token = localStorage.getItem('meeta_vendor_token') || localStorage.getItem('meeta_token');
  const statuses = [
    ['','All orders'],['confirmed','New'],['processing','Processing'],['ready_to_ship','Ready'],['shipped','Shipped'],['delivered','Delivered']
  ];
  let page = 1, lastPage = 1, debounce;
  if (!token) window.location.href = @json(url('/vendor/login'));

  const headers = () => ({Authorization:`Bearer ${token}`,Accept:'application/json'});
  const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
  const label = status => String(status || '').replaceAll('_',' ');
  const money = value => new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR',maximumFractionDigits:2}).format(Number(value || 0));
  const date = value => value ? new Intl.DateTimeFormat('en-IN',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}).format(new Date(value)) : '—';

  function notify(message){ const el=document.getElementById('toast'); el.textContent=message; el.classList.add('show'); setTimeout(()=>el.classList.remove('show'),3000); }
  function renderStats(counts={}){
    document.getElementById('orderStats').innerHTML=statuses.map(([key,name])=>{
      const count=key ? Number(counts[key]||0) : Object.values(counts).reduce((a,b)=>a+Number(b),0);
      return `<button type="button" class="card order-stat ${document.getElementById('statusFilter').value===key?'active':''}" data-status="${key}"><div class="order-stat-label">${name}</div><div class="order-stat-value">${count}</div></button>`;
    }).join('');
    document.querySelectorAll('.order-stat').forEach(btn=>btn.onclick=()=>{document.getElementById('statusFilter').value=btn.dataset.status;page=1;loadOrders();});
  }
  function renderRows(orders){
    const body=document.getElementById('ordersBody');
    if(!orders.length){body.innerHTML=`<tr><td colspan="7" class="table-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2h12l3 5-9 5-9-5 3-5Z"/><path d="M3 7v10l9 5 9-5V7"/></svg><div>No orders found for this filter.</div></td></tr>`;return;}
    body.innerHTML=orders.map(o=>{
      const parent=o.order||{}, items=o.order_items||[], qty=items.reduce((sum,item)=>sum+Number(item.quantity||0),0);
      return `<tr><td><a class="order-number" href="${ORDER_PAGE}/${o.id}">${escapeHtml(o.sub_order_number)}</a><div class="muted">${date(o.created_at)}</div></td>
      <td><div class="customer-name">${escapeHtml(parent.shipping_full_name||'Customer')}</div><div class="muted">${escapeHtml([parent.shipping_city,parent.shipping_state].filter(Boolean).join(', '))}</div></td>
      <td>${qty} item${qty===1?'':'s'}<div class="muted">${items.length?escapeHtml(items[0].product_name):'—'}${items.length>1?` +${items.length-1} more`:''}</div></td>
      <td class="amount">${money(o.total_amount)}</td><td><span class="status-badge status-${parent.payment_status==='paid'?'delivered':'confirmed'}">${escapeHtml(parent.payment_status||'pending')}</span></td>
      <td><span class="status-badge status-${o.order_status}">${label(o.order_status)}</span></td><td><a class="btn btn-outline btn-sm" href="${ORDER_PAGE}/${o.id}">Manage</a></td></tr>`;
    }).join('');
  }
  async function loadOrders(){
    const body=document.getElementById('ordersBody'); body.innerHTML='<tr><td colspan="7" class="table-empty">Loading orders…</td></tr>';
    const params=new URLSearchParams({page,per_page:15}); const status=document.getElementById('statusFilter').value,search=document.getElementById('searchInput').value.trim();
    if(status)params.set('status',status); if(search)params.set('search',search);
    try{const res=await fetch(`${API_BASE}/orders?${params}`,{headers:headers()});const json=await res.json();if(res.status===401){localStorage.removeItem('meeta_vendor_token');location.href=@json(url('/vendor/login'));return;}if(!res.ok||!json.success)throw new Error(json.message||'Unable to load orders.');
      const pagination=json.data.orders; lastPage=pagination.last_page||1;renderStats(json.data.counts);renderRows(pagination.data||[]);
      document.getElementById('tableSummary').textContent=`Showing ${pagination.from||0}–${pagination.to||0} of ${pagination.total||0} orders`;
      document.getElementById('prevBtn').disabled=page<=1;document.getElementById('nextBtn').disabled=page>=lastPage;
    }catch(error){body.innerHTML=`<tr><td colspan="7" class="table-empty">${escapeHtml(error.message)}</td></tr>`;notify(error.message);}
  }
  document.getElementById('searchInput').addEventListener('input',()=>{clearTimeout(debounce);debounce=setTimeout(()=>{page=1;loadOrders()},350)});
  document.getElementById('statusFilter').addEventListener('change',()=>{page=1;loadOrders()});
  document.getElementById('refreshBtn').onclick=loadOrders; document.getElementById('prevBtn').onclick=()=>{if(page>1){page--;loadOrders()}};document.getElementById('nextBtn').onclick=()=>{if(page<lastPage){page++;loadOrders()}};
  loadOrders();
</script>
@endpush

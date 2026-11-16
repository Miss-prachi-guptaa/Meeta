@extends('vendor.layouts.app')

@section('title', 'Order Details')

@push('styles')
<style>
  .detail-top { display:flex; justify-content:space-between; gap:20px; align-items:flex-start; margin-bottom:22px; }
  .back-link { display:inline-flex; gap:7px; align-items:center; color:var(--text-secondary); font-weight:600; margin-bottom:13px; }
  .back-link:hover { color:var(--terracotta); }
  .back-link svg { width:16px; }
  .title-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  .title-row h1 { font-size:27px; }
  .subtext { color:var(--text-muted); margin-top:5px; }
  .detail-actions { display:flex; gap:9px; flex-wrap:wrap; justify-content:flex-end; }
  .btn-danger { color:#fff; background:var(--danger); }
  .btn-success { color:#fff; background:var(--success); }
  .status-badge { display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 11px;font-size:11px;font-weight:700;text-transform:capitalize;white-space:nowrap; }
  .status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}.status-confirmed{color:#9a6411;background:#fff0cf}.status-processing{color:#355e9a;background:#e5eefb}.status-ready_to_ship{color:#835ca3;background:#f0e5f8}.status-shipped,.status-picked_up,.status-in_transit,.status-out_for_delivery{color:#176b77;background:#dff2f3}.status-delivered{color:var(--success);background:var(--success-soft)}.status-cancelled{color:var(--danger);background:var(--danger-soft)}
  .detail-grid { display:grid; grid-template-columns:minmax(0,1.7fr) minmax(300px,.8fr); gap:20px; align-items:start; }
  .stack { display:flex; flex-direction:column; gap:20px; }
  .panel { padding:22px; }
  .panel-title { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:18px; }
  .panel-title h2 { font-size:18px; }
  .item-row { display:grid; grid-template-columns:58px minmax(0,1fr) 80px 110px; gap:14px; align-items:center; padding:15px 0; border-top:1px solid var(--border-color); }
  .item-row:first-of-type { border-top:0; padding-top:0; }
  .item-image { width:58px; height:58px; border-radius:10px; background:var(--terracotta-soft); overflow:hidden; display:flex; align-items:center; justify-content:center; color:var(--terracotta-dark); font-weight:700; }
  .item-image img { width:100%; height:100%; object-fit:cover; }
  .item-name { font-weight:700; }.item-meta { color:var(--text-muted); font-size:12px; margin-top:3px; }.item-price { font-weight:700; text-align:right; }
  .amount-line { display:flex; justify-content:space-between; gap:20px; padding:7px 0; color:var(--text-secondary); }
  .amount-line.total { border-top:1px solid var(--border-color); margin-top:7px; padding-top:14px; font-size:16px; color:var(--text-primary); font-weight:700; }
  .address-name { font-weight:700; font-size:15px; margin-bottom:8px; }.address-lines { color:var(--text-secondary); line-height:1.7; }.phone { display:flex; align-items:center; gap:8px; margin-top:12px; color:var(--text-primary); font-weight:600; }
  .phone svg { width:16px; color:var(--terracotta); }
  .shipment-card { border:1px solid var(--border-color); border-radius:12px; padding:16px; }
  .shipment-head { display:flex; justify-content:space-between; gap:10px; align-items:flex-start; margin-bottom:15px; }.tracking-number { font-weight:700; }.shipment-meta { color:var(--text-muted); font-size:12px; margin-top:3px; }
  .timeline { border-left:2px solid var(--border-color); margin-left:7px; padding-left:19px; }
  .timeline-event { position:relative; padding-bottom:17px; }.timeline-event:last-child { padding-bottom:0; }.timeline-event::before { content:''; position:absolute; width:10px;height:10px;border-radius:50%;background:var(--terracotta);left:-25px;top:4px;border:2px solid var(--bg-card);box-shadow:0 0 0 2px var(--terracotta-soft); }.event-title { font-weight:700; text-transform:capitalize; }.event-meta { color:var(--text-muted);font-size:11px;margin-top:2px; }.event-description { color:var(--text-secondary);font-size:12px;margin-top:3px; }
  .next-step { padding:17px; background:#fff8ee; border:1px solid #edcf9d; border-radius:12px; margin-bottom:16px; }.next-step-label { font-size:10px;text-transform:uppercase;color:#9a6411;font-weight:800;letter-spacing:.6px; }.next-step p { margin-top:5px;color:var(--text-secondary); }
  .loading-card { padding:70px;text-align:center;color:var(--text-muted); }
  .modal-overlay { display:none; position:fixed;inset:0;background:rgba(45,33,23,.52);z-index:400;align-items:center;justify-content:center;padding:20px; }.modal-overlay.open{display:flex}.modal { width:min(560px,100%);max-height:90vh;overflow:auto;padding:25px; }.modal h3{font-size:20px}.modal-sub{color:var(--text-muted);margin:5px 0 20px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.form-group{margin-bottom:14px}.form-group label{display:block;font-weight:600;font-size:12px;margin-bottom:6px}.form-group input,.form-group textarea{width:100%}.form-group textarea{min-height:95px;resize:vertical;border:1px solid var(--border-color-strong);border-radius:9px;padding:10px 12px;font-family:inherit}.modal-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}.form-error{display:none;background:var(--danger-soft);color:var(--danger);padding:10px 12px;border-radius:9px;font-size:12px;margin-bottom:12px}
  .toast{position:fixed;right:24px;bottom:24px;background:var(--text-primary);color:#fff;padding:12px 18px;border-radius:10px;opacity:0;transform:translateY(12px);pointer-events:none;transition:.2s;z-index:500}.toast.error{background:var(--danger)}.toast.show{opacity:1;transform:none}
  @media(max-width:950px){.detail-grid{grid-template-columns:1fr}.detail-top{flex-direction:column}.detail-actions{justify-content:flex-start}}
  @media(max-width:600px){.item-row{grid-template-columns:48px 1fr auto}.item-image{width:48px;height:48px}.item-qty{display:none}.item-price{grid-column:3}.form-grid{grid-template-columns:1fr}.detail-actions .btn{flex:1}.panel{padding:18px}}
</style>
@endpush

@section('content')
<div id="loadingState" class="card loading-card">Loading order details…</div>
<div id="detailContent" hidden>
  <header class="detail-top">
    <div><a class="back-link" href="{{ route('vendor.orders.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>Back to orders</a><div class="title-row"><h1 id="orderNumber">Order</h1><span id="orderStatus"></span></div><div class="subtext" id="placedAt"></div></div>
    <div class="detail-actions" id="detailActions"></div>
  </header>
  <main class="detail-grid">
    <div class="stack">
      <section class="card panel"><div class="panel-title"><h2>Order items</h2><span id="itemCount" class="subtext"></span></div><div id="itemsList"></div></section>
      <section class="card panel" id="shipmentPanel"><div class="panel-title"><h2>Delivery & tracking</h2><span class="status-badge status-confirmed">Not created</span></div><div class="next-step"><div class="next-step-label">Next step</div><p>Accept this order, pack it and request courier pickup.</p></div></section>
    </div>
    <aside class="stack">
      <section class="card panel"><div class="panel-title"><h2>Customer delivery</h2></div><div id="customerAddress"></div></section>
      <section class="card panel"><div class="panel-title"><h2>Payment summary</h2><span id="paymentStatus"></span></div><div id="amountSummary"></div></section>
      <section class="card panel" id="notesPanel" hidden><div class="panel-title"><h2>Customer note</h2></div><p id="customerNotes" class="address-lines"></p></section>
    </aside>
  </main>
</div>

<div class="modal-overlay" id="rejectModal"><div class="card modal"><h3>Reject this order?</h3><p class="modal-sub">The customer will be notified and stock will be restored.</p><div class="form-error" id="rejectError"></div><div class="form-group"><label>Reason for rejection</label><textarea id="rejectReason" placeholder="Explain why this order cannot be fulfilled..."></textarea></div><div class="modal-actions"><button class="btn btn-outline" onclick="closeModal('rejectModal')">Keep order</button><button class="btn btn-danger" onclick="rejectOrder()">Reject order</button></div></div></div>

<div class="modal-overlay" id="shipmentModal"><div class="card modal"><h3>Package & pickup details</h3><p class="modal-sub">Enter final packed dimensions. Dummy Logistics will generate the tracking number.</p><div class="form-error" id="shipmentError"></div><div class="form-grid"><div class="form-group"><label>Weight (kg)</label><input id="weight" type="number" min="0.001" step="0.001" placeholder="1.5"></div><div class="form-group"><label>Courier name</label><input id="partner" value="Dummy Logistics"></div><div class="form-group"><label>Length (cm)</label><input id="length" type="number" min="0.01" step="0.01" placeholder="30"></div><div class="form-group"><label>Width (cm)</label><input id="width" type="number" min="0.01" step="0.01" placeholder="20"></div><div class="form-group"><label>Height (cm)</label><input id="height" type="number" min="0.01" step="0.01" placeholder="15"></div><div class="form-group"><label>Expected delivery</label><input id="expectedDelivery" type="datetime-local"></div></div><div class="form-group"><label>Handling note (optional)</label><textarea id="shipmentRemarks" placeholder="Fragile, keep upright..."></textarea></div><div class="modal-actions"><button class="btn btn-outline" onclick="closeModal('shipmentModal')">Cancel</button><button class="btn btn-terracotta" onclick="createShipment()">Request pickup</button></div></div></div>
<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
  const API_BASE=@json(url('/api/vendor')), ORDER_ID=@json((int) $subOrderId), LOGIN_URL=@json(url('/vendor/login'));
  const token=localStorage.getItem('meeta_vendor_token')||localStorage.getItem('meeta_token'); let currentOrder=null;
  if(!token)location.href=LOGIN_URL;
  const headers=(json=true)=>{const h={Authorization:`Bearer ${token}`,Accept:'application/json'};if(json)h['Content-Type']='application/json';return h};
  const esc=v=>String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
  const label=v=>String(v||'').replaceAll('_',' '), money=v=>new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR'}).format(Number(v||0));
  const formatDate=v=>v?new Intl.DateTimeFormat('en-IN',{dateStyle:'medium',timeStyle:'short'}).format(new Date(v)):'—';
  function toast(message,error=false){const el=document.getElementById('toast');el.textContent=message;el.classList.toggle('error',error);el.classList.add('show');setTimeout(()=>el.classList.remove('show'),3500)}
  function badge(status){return `<span class="status-badge status-${status}">${label(status)}</span>`}
  function apiError(json){return Object.values(json.errors||{}).flat()[0]||json.message||'Something went wrong.'}
  function imageUrl(item){const images=item.product?.images||[];const image=images.find(i=>i.is_primary)||images[0];return image?.public_url||image?.url||null}
  function render(){const o=currentOrder,parent=o.order||{},items=o.order_items||[],shipment=(o.shipments||[])[0];
    document.getElementById('orderNumber').textContent=o.sub_order_number;document.getElementById('orderStatus').innerHTML=badge(o.order_status);document.getElementById('placedAt').textContent=`Placed ${formatDate(parent.created_at)}`;document.getElementById('itemCount').textContent=`${items.reduce((s,i)=>s+Number(i.quantity),0)} items`;
    document.getElementById('itemsList').innerHTML=items.map(i=>{const img=imageUrl(i);return `<div class="item-row"><div class="item-image">${img?`<img src="${esc(img)}" alt="">`:esc((i.product_name||'P')[0])}</div><div><div class="item-name">${esc(i.product_name)}</div><div class="item-meta">${esc(i.variant_name||'Standard')} · SKU ${esc(i.sku||'—')}</div></div><div class="item-qty">Qty ${i.quantity}</div><div class="item-price">${money(i.total_amount)}</div></div>`}).join('')||'<p class="subtext">No items available.</p>';
    const address=[parent.shipping_address_line_1,parent.shipping_address_line_2,parent.shipping_landmark,parent.shipping_city,parent.shipping_state,parent.shipping_postal_code,parent.shipping_country].filter(Boolean).map(esc).join('<br>');
    document.getElementById('customerAddress').innerHTML=`<div class="address-name">${esc(parent.shipping_full_name)}</div><div class="address-lines">${address}</div><div class="phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.61a2 2 0 0 1-.45 2.11L9 10.6a16 16 0 0 0 4.4 4.4l1.16-1.17a2 2 0 0 1 2.11-.45c.83.29 1.71.5 2.61.62A2 2 0 0 1 22 16.92Z"/></svg>${esc(parent.shipping_phone)}</div>`;
    document.getElementById('paymentStatus').innerHTML=badge(parent.payment_status==='paid'?'delivered':'confirmed');document.getElementById('amountSummary').innerHTML=`<div class="amount-line"><span>Items subtotal</span><strong>${money(o.subtotal)}</strong></div><div class="amount-line"><span>Shipping</span><span>Managed on main order</span></div><div class="amount-line total"><span>Vendor order total</span><span>${money(o.total_amount)}</span></div>`;
    if(parent.notes){document.getElementById('notesPanel').hidden=false;document.getElementById('customerNotes').textContent=parent.notes}
    renderActions(o,shipment);renderShipment(shipment);document.getElementById('loadingState').hidden=true;document.getElementById('detailContent').hidden=false;
  }
  function renderActions(o,shipment){let html='';if(o.order_status==='confirmed')html='<button class="btn btn-danger" onclick="openModal(\'rejectModal\')">Reject</button><button class="btn btn-success" onclick="acceptOrder()">Accept & prepare</button>';else if(o.order_status==='processing')html='<button class="btn btn-terracotta" onclick="openModal(\'shipmentModal\')">Pack & request pickup</button>';else if(shipment&&shipment.provider==='dummy'&&nextShipmentStatus(shipment.status))html=`<button class="btn btn-terracotta" onclick="advanceShipment()">Mark ${label(nextShipmentStatus(shipment.status))}</button>`;document.getElementById('detailActions').innerHTML=html}
  function renderShipment(s){const panel=document.getElementById('shipmentPanel');if(!s){panel.innerHTML='<div class="panel-title"><h2>Delivery & tracking</h2><span class="status-badge status-confirmed">Not created</span></div><div class="next-step"><div class="next-step-label">Next step</div><p>Accept this order, pack it and request courier pickup.</p></div>';return}const events=[...(s.tracking||[])].sort((a,b)=>new Date(b.tracked_at)-new Date(a.tracked_at));panel.innerHTML=`<div class="panel-title"><h2>Delivery & tracking</h2>${badge(s.status)}</div><div class="shipment-card"><div class="shipment-head"><div><div class="tracking-number">${esc(s.shipping_partner)} · ${esc(s.tracking_number)}</div><div class="shipment-meta">${esc(s.provider)} provider · ${s.package_weight} kg · ${s.package_length} × ${s.package_width} × ${s.package_height} cm</div></div></div><div class="timeline">${events.map(e=>`<div class="timeline-event"><div class="event-title">${label(e.status)}</div><div class="event-meta">${esc(e.location||'Status update')} · ${formatDate(e.tracked_at)}</div>${e.description?`<div class="event-description">${esc(e.description)}</div>`:''}</div>`).join('')}</div></div>`}
  const transitions={ready_for_pickup:'picked_up',picked_up:'in_transit',in_transit:'out_for_delivery',out_for_delivery:'delivered'};const nextShipmentStatus=status=>transitions[status]||null;
  async function loadOrder(){try{const res=await fetch(`${API_BASE}/orders/${ORDER_ID}`,{headers:headers(false)});const json=await res.json();if(res.status===401){location.href=LOGIN_URL;return}if(!res.ok||!json.success)throw new Error(apiError(json));currentOrder=json.data;render()}catch(e){document.getElementById('loadingState').textContent=e.message;toast(e.message,true)}}
  async function request(url,method,payload){const res=await fetch(url,{method,headers:headers(),body:JSON.stringify(payload)});const json=await res.json();if(!res.ok||!json.success)throw new Error(apiError(json));return json}
  async function acceptOrder(){try{const json=await request(`${API_BASE}/orders/${ORDER_ID}/status`,'PATCH',{status:'processing'});toast(json.message);await loadOrder()}catch(e){toast(e.message,true)}}
  async function rejectOrder(){const reason=document.getElementById('rejectReason').value.trim(),error=document.getElementById('rejectError');if(reason.length<5){error.textContent='Please enter at least 5 characters.';error.style.display='block';return}try{const json=await request(`${API_BASE}/orders/${ORDER_ID}/status`,'PATCH',{status:'cancelled',reason});closeModal('rejectModal');toast(json.message);await loadOrder()}catch(e){error.textContent=e.message;error.style.display='block'}}
  async function createShipment(){const error=document.getElementById('shipmentError'),payload={weight:Number(document.getElementById('weight').value),length:Number(document.getElementById('length').value),width:Number(document.getElementById('width').value),height:Number(document.getElementById('height').value),shipping_partner:document.getElementById('partner').value.trim()||'Dummy Logistics',remarks:document.getElementById('shipmentRemarks').value.trim()||null};const expected=document.getElementById('expectedDelivery').value;if(expected)payload.expected_delivery_at=new Date(expected).toISOString();if(!payload.weight||!payload.length||!payload.width||!payload.height){error.textContent='Weight and all three dimensions are required.';error.style.display='block';return}try{const json=await request(`${API_BASE}/orders/${ORDER_ID}/shipment`,'POST',payload);closeModal('shipmentModal');toast(json.message);await loadOrder()}catch(e){error.textContent=e.message;error.style.display='block'}}
  async function advanceShipment(){const shipment=currentOrder.shipments?.[0],status=nextShipmentStatus(shipment?.status);if(!status)return;try{const json=await request(`${API_BASE}/shipments/${shipment.id}/status`,'PATCH',{status,description:`Dummy courier marked package ${label(status)}.`});toast(json.message);await loadOrder()}catch(e){toast(e.message,true)}}
  function openModal(id){document.getElementById(id).classList.add('open')}function closeModal(id){document.getElementById(id).classList.remove('open');document.querySelector(`#${id} .form-error`).style.display='none'}
  document.querySelectorAll('.modal-overlay').forEach(el=>el.addEventListener('click',e=>{if(e.target===el)closeModal(el.id)}));loadOrder();
</script>
@endpush

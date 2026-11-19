@extends('vendor.layouts.app')

@section('title', 'Order Details')

@push('styles')
<style>
  .detail-top { display:flex; justify-content:space-between; gap:20px; align-items:flex-start; margin-bottom:22px; }
  .back-link { display:inline-flex; gap:7px; align-items:center; color:var(--text-secondary); font-weight:600; margin-bottom:13px; text-decoration:none; }
  .back-link:hover { color:var(--terracotta); }
  .back-link svg { width:16px; }
  .title-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  .title-row h1 { font-size:27px; }
  .subtext { color:var(--text-muted); margin-top:5px; }
  .detail-actions { display:flex; gap:9px; flex-wrap:wrap; justify-content:flex-end; }
  .btn-danger { color:#fff; background:var(--danger); }
  .btn-success { color:#fff; background:var(--success); }
  .btn-label { background:#1e293b; color:#fff; border:none; display:inline-flex; align-items:center; gap:7px; }
  .btn-label:hover { background:#0f172a; color:#fff; }
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
  .shipment-head { display:flex; justify-content:space-between; gap:10px; align-items:flex-start; margin-bottom:15px; }
  .tracking-number { font-weight:700; font-size:15px; color:var(--text-primary); }
  .tracking-link { display:inline-flex; align-items:center; gap:4px; font-size:12px; color:var(--terracotta); text-decoration:none; margin-top:4px; font-weight:600; }
  .tracking-link:hover { text-decoration:underline; }
  .shipment-meta { color:var(--text-muted); font-size:12px; margin-top:3px; }
  .timeline { border-left:2px solid var(--border-color); margin-left:7px; padding-left:19px; }
  .timeline-event { position:relative; padding-bottom:17px; }.timeline-event:last-child { padding-bottom:0; }.timeline-event::before { content:''; position:absolute; width:10px;height:10px;border-radius:50%;background:var(--terracotta);left:-25px;top:4px;border:2px solid var(--bg-card);box-shadow:0 0 0 2px var(--terracotta-soft); }.event-title { font-weight:700; text-transform:capitalize; }.event-meta { color:var(--text-muted);font-size:11px;margin-top:2px; }.event-description { color:var(--text-secondary);font-size:12px;margin-top:3px; }
  .next-step { padding:17px; background:#fff8ee; border:1px solid #edcf9d; border-radius:12px; margin-bottom:16px; }.next-step-label { font-size:10px;text-transform:uppercase;color:#9a6411;font-weight:800;letter-spacing:.6px; }.next-step p { margin-top:5px;color:var(--text-secondary); }
  .loading-card { padding:70px;text-align:center;color:var(--text-muted); }
  .modal-overlay { display:none; position:fixed;inset:0;background:rgba(45,33,23,.52);z-index:400;align-items:center;justify-content:center;padding:20px; }.modal-overlay.open{display:flex}.modal { width:min(620px,100%);max-height:92vh;overflow:auto;padding:25px; }.modal h3{font-size:20px}.modal-sub{color:var(--text-muted);margin:5px 0 20px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.form-group{margin-bottom:14px}.form-group label{display:block;font-weight:600;font-size:12px;margin-bottom:6px}.form-group input,.form-group select,.form-group textarea{width:100%; border:1px solid var(--border-color-strong);border-radius:9px;padding:9px 12px;font-family:inherit;font-size:13px;background:var(--bg-card);color:var(--text-primary);box-sizing:border-box;}.form-group textarea{min-height:80px;resize:vertical}.modal-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}.form-error{display:none;background:var(--danger-soft);color:var(--danger);padding:10px 12px;border-radius:9px;font-size:12px;margin-bottom:12px}
  .toast{position:fixed;right:24px;bottom:24px;background:var(--text-primary);color:#fff;padding:12px 18px;border-radius:10px;opacity:0;transform:translateY(12px);pointer-events:none;transition:.2s;z-index:500}.toast.error{background:var(--danger)}.toast.show{opacity:1;transform:none}

  /* Logistics partner card pills */
  .courier-pill-group { display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:8px; margin-bottom:14px; }
  .courier-pill { border:1.5px solid var(--border-color); border-radius:10px; padding:10px 12px; cursor:pointer; text-align:center; transition:all .15s; background:#fff; }
  .courier-pill:hover { border-color:var(--terracotta); background:#fdf9f5; }
  .courier-pill.selected { border-color:var(--terracotta); background:#fef4eb; box-shadow:0 0 0 1px var(--terracotta); }
  .courier-pill-name { font-weight:700; font-size:13px; color:var(--text-primary); display:block; }
  .courier-pill-sub { font-size:11px; color:var(--text-muted); display:block; margin-top:2px; }

  /* Shipping Label Printable Preview Modal */
  .label-container { width:100%; max-width:480px; margin:0 auto; background:#fff; color:#000; border:2px solid #000; border-radius:4px; padding:16px; font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace; box-sizing:border-box; }
  .label-header { display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:10px; }
  .label-brand { font-size:22px; font-weight:900; letter-spacing:1px; }
  .label-hub { font-size:14px; font-weight:800; border:2px solid #000; padding:3px 8px; border-radius:3px; }
  .label-barcode-box { text-align:center; padding:12px 0; border-bottom:2px solid #000; }
  .label-awb { font-size:16px; font-weight:800; letter-spacing:1.5px; margin-top:6px; font-family:monospace; }
  .label-grid { display:grid; grid-template-columns:1fr 1fr; border-bottom:2px solid #000; }
  .label-col { padding:8px; }
  .label-col:first-child { border-right:2px solid #000; }
  .label-title { font-size:9px; text-transform:uppercase; font-weight:800; letter-spacing:.5px; color:#555; margin-bottom:4px; }
  .label-address { font-size:11px; line-height:1.4; }
  .label-meta-strip { display:grid; grid-template-columns:1fr 1fr 1fr; border-bottom:2px solid #000; font-size:11px; text-align:center; }
  .label-meta-cell { padding:6px; border-right:1px solid #000; }
  .label-meta-cell:last-child { border-right:none; }
  .label-table { width:100%; border-collapse:collapse; font-size:11px; margin-top:8px; }
  .label-table th, .label-table td { border-bottom:1px solid #ccc; padding:5px 4px; text-align:left; }
  .label-table th { background:#f3f3f3; font-weight:700; }
  .barcode-svg { width:100%; max-width:280px; height:50px; display:block; margin:0 auto; }

  @media print {
    body * { visibility: hidden; }
    #printableLabelSection, #printableLabelSection * { visibility: visible; }
    #printableLabelSection { position: absolute; left: 0; top: 0; width: 100%; display: block !important; padding: 0; }
    .label-container { max-width: 100%; border: 2px solid #000; }
  }

  @media(max-width:950px){.detail-grid{grid-template-columns:1fr}.detail-top{flex-direction:column}.detail-actions{justify-content:flex-start}}
  @media(max-width:600px){.item-row{grid-template-columns:48px 1fr auto}.item-image{width:48px;height:48px}.item-qty{display:none}.item-price{grid-column:3}.form-grid{grid-template-columns:1fr}.detail-actions .btn{flex:1}.panel{padding:18px}}
</style>
@endpush

@section('content')
<div id="loadingState" class="card loading-card">Loading order details…</div>
<div id="detailContent" hidden>
  <header class="detail-top">
    <div>
      <a class="back-link" href="{{ route('vendor.orders.index') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        Back to orders
      </a>
      <div class="title-row">
        <h1 id="orderNumber">Order</h1>
        <span id="orderStatus"></span>
      </div>
      <div class="subtext" id="placedAt"></div>
    </div>
    <div class="detail-actions" id="detailActions"></div>
  </header>

  <main class="detail-grid">
    <div class="stack">
      <section class="card panel">
        <div class="panel-title">
          <h2>Order items</h2>
          <span id="itemCount" class="subtext"></span>
        </div>
        <div id="itemsList"></div>
      </section>

      <section class="card panel" id="shipmentPanel">
        <div class="panel-title">
          <h2>Delivery & tracking</h2>
          <span class="status-badge status-confirmed">Not created</span>
        </div>
        <div class="next-step">
          <div class="next-step-label">Next step</div>
          <p>Accept this order, pack it and schedule a courier pickup.</p>
        </div>
      </section>
    </div>

    <aside class="stack">
      <section class="card panel">
        <div class="panel-title"><h2>Customer delivery</h2></div>
        <div id="customerAddress"></div>
      </section>
      <section class="card panel">
        <div class="panel-title"><h2>Payment summary</h2><span id="paymentStatus"></span></div>
        <div id="amountSummary"></div>
      </section>
      <section class="card panel" id="notesPanel" hidden>
        <div class="panel-title"><h2>Customer note</h2></div>
        <p id="customerNotes" class="address-lines"></p>
      </section>
    </aside>
  </main>
</div>

<!-- Reject Order Modal -->
<div class="modal-overlay" id="rejectModal">
  <div class="card modal">
    <h3>Reject this order?</h3>
    <p class="modal-sub">The customer will be notified and stock will be restored.</p>
    <div class="form-error" id="rejectError"></div>
    <div class="form-group">
      <label>Reason for rejection</label>
      <textarea id="rejectReason" placeholder="Explain why this order cannot be fulfilled..."></textarea>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('rejectModal')">Keep order</button>
      <button class="btn btn-danger" onclick="rejectOrder()">Reject order</button>
    </div>
  </div>
</div>

<!-- Realistic Package & Pickup Modal -->
<div class="modal-overlay" id="shipmentModal">
  <div class="card modal">
    <h3>Schedule Courier Pickup & Generate Manifest</h3>
    <p class="modal-sub">Select verified courier partner and enter packed box dimensions.</p>
    <div class="form-error" id="shipmentError"></div>

    <div class="form-group">
      <label>Select Courier Partner</label>
      <div class="courier-pill-group">
        <div class="courier-pill selected" onclick="selectCourier('Delhivery Surface', this)">
          <span class="courier-pill-name">Delhivery</span>
          <span class="courier-pill-sub">Surface · ₹55</span>
        </div>
        <div class="courier-pill" onclick="selectCourier('BlueDart Air Express', this)">
          <span class="courier-pill-name">BlueDart</span>
          <span class="courier-pill-sub">Air · ₹110</span>
        </div>
        <div class="courier-pill" onclick="selectCourier('Shadowfax Express', this)">
          <span class="courier-pill-name">Shadowfax</span>
          <span class="courier-pill-sub">Hyperlocal · ₹45</span>
        </div>
        <div class="courier-pill" onclick="selectCourier('DTDC Standard', this)">
          <span class="courier-pill-name">DTDC</span>
          <span class="courier-pill-sub">Express · ₹60</span>
        </div>
        <div class="courier-pill" onclick="selectCourier('India Post Speed Post', this)">
          <span class="courier-pill-name">India Post</span>
          <span class="courier-pill-sub">Speed · ₹40</span>
        </div>
      </div>
      <input type="hidden" id="partner" value="Delhivery Surface">
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label>Dead Weight (kg)</label>
        <input id="weight" type="number" min="0.001" step="0.001" value="0.850" placeholder="0.850">
      </div>
      <div class="form-group">
        <label>Pickup Time Slot</label>
        <select id="pickupSlot">
          <option value="today_evening">Today (Evening 4:00 PM - 7:00 PM)</option>
          <option value="tomorrow_morning">Tomorrow (Morning 10:00 AM - 1:00 PM)</option>
          <option value="tomorrow_afternoon">Tomorrow (Afternoon 2:00 PM - 5:00 PM)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Length (cm)</label>
        <input id="length" type="number" min="0.01" step="0.01" value="25" placeholder="25">
      </div>
      <div class="form-group">
        <label>Width (cm)</label>
        <input id="width" type="number" min="0.01" step="0.01" value="18" placeholder="18">
      </div>
      <div class="form-group">
        <label>Height (cm)</label>
        <input id="height" type="number" min="0.01" step="0.01" value="12" placeholder="12">
      </div>
      <div class="form-group">
        <label>Expected Delivery Date</label>
        <input id="expectedDelivery" type="datetime-local">
      </div>
    </div>

    <div class="form-group">
      <label>Package Handling Instructions</label>
      <textarea id="shipmentRemarks" placeholder="Fragile handcrafted item, handle with care..."></textarea>
    </div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('shipmentModal')">Cancel</button>
      <button class="btn btn-terracotta" onclick="createShipment()">Generate AWB & Request Pickup</button>
    </div>
  </div>
</div>

<!-- Shipping Label & Packing Slip Modal -->
<div class="modal-overlay" id="labelModal">
  <div class="card modal" style="width:min(540px, 100%);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <h3 style="margin:0;">Shipping Label & Packing Slip</h3>
      <button class="btn btn-outline btn-sm" onclick="closeModal('labelModal')">✕</button>
    </div>

    <div id="printableLabelSection">
      <div class="label-container" id="labelContent">
        <!-- Rendered dynamically -->
      </div>
    </div>

    <div class="modal-actions" style="margin-top:20px;">
      <button class="btn btn-outline" onclick="closeModal('labelModal')">Close</button>
      <button class="btn btn-terracotta" onclick="window.print()">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
        Print Label (4×6)
      </button>
    </div>
  </div>
</div>

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

  function selectCourier(partnerName, el) {
    document.querySelectorAll('.courier-pill').forEach(p=>p.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('partner').value=partnerName;
  }

  function generateBarcodeSVG(code) {
    // Generates a realistic Code-128 styled SVG barcode
    let bars = '';
    let x = 10;
    const clean = String(code || 'AWB12345678').toUpperCase();
    for (let i = 0; i < clean.length; i++) {
      const charCode = clean.charCodeAt(i);
      const w1 = (charCode % 3) + 1.5;
      const w2 = ((charCode * 2) % 4) + 1.2;
      const w3 = ((charCode * 3) % 2) + 1;
      bars += `<rect x="${x}" y="0" width="${w1}" height="45" fill="#000"/>`;
      x += w1 + 1.5;
      bars += `<rect x="${x}" y="0" width="${w2}" height="45" fill="#000"/>`;
      x += w2 + 2;
      bars += `<rect x="${x}" y="0" width="${w3}" height="45" fill="#000"/>`;
      x += w3 + 2;
    }
    return `<svg class="barcode-svg" viewBox="0 0 ${Math.max(x + 10, 240)} 50" preserveAspectRatio="none">${bars}</svg>`;
  }

  function render(){
    const o=currentOrder,parent=o.order||{},items=o.order_items||[],shipment=(o.shipments||[])[0];
    document.getElementById('orderNumber').textContent=o.sub_order_number;
    document.getElementById('orderStatus').innerHTML=badge(o.order_status);
    document.getElementById('placedAt').textContent=`Placed ${formatDate(parent.created_at)}`;
    document.getElementById('itemCount').textContent=`${items.reduce((s,i)=>s+Number(i.quantity),0)} items`;
    
    document.getElementById('itemsList').innerHTML=items.map(i=>{
      const img=imageUrl(i);
      return `<div class="item-row"><div class="item-image">${img?`<img src="${esc(img)}" alt="">`:esc((i.product_name||'P')[0])}</div><div><div class="item-name">${esc(i.product_name)}</div><div class="item-meta">${esc(i.variant_name||'Standard')} · SKU ${esc(i.sku||'—')}</div></div><div class="item-qty">Qty ${i.quantity}</div><div class="item-price">${money(i.total_amount)}</div></div>`
    }).join('')||'<p class="subtext">No items available.</p>';
    
    const address=[parent.shipping_address_line_1,parent.shipping_address_line_2,parent.shipping_landmark,parent.shipping_city,parent.shipping_state,parent.shipping_postal_code,parent.shipping_country].filter(Boolean).map(esc).join('<br>');
    document.getElementById('customerAddress').innerHTML=`<div class="address-name">${esc(parent.shipping_full_name)}</div><div class="address-lines">${address}</div><div class="phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.61a2 2 0 0 1-.45 2.11L9 10.6a16 16 0 0 0 4.4 4.4l1.16-1.17a2 2 0 0 1 2.11-.45c.83.29 1.71.5 2.61.62A2 2 0 0 1 22 16.92Z"/></svg>${esc(parent.shipping_phone)}</div>`;
    document.getElementById('paymentStatus').innerHTML=badge(parent.payment_status==='paid'?'delivered':'confirmed');
    document.getElementById('amountSummary').innerHTML=`<div class="amount-line"><span>Items subtotal</span><strong>${money(o.subtotal)}</strong></div><div class="amount-line"><span>Shipping</span><span>Managed on main order</span></div><div class="amount-line total"><span>Vendor order total</span><span>${money(o.total_amount)}</span></div>`;
    
    if(parent.notes){
      document.getElementById('notesPanel').hidden=false;
      document.getElementById('customerNotes').textContent=parent.notes;
    }
    renderActions(o,shipment);
    renderShipment(shipment);
    document.getElementById('loadingState').hidden=true;
    document.getElementById('detailContent').hidden=false;
  }

  function renderActions(o,shipment){
    let html='';
    if(o.order_status==='confirmed'){
      html='<button class="btn btn-danger" onclick="openModal(\'rejectModal\')">Reject</button><button class="btn btn-success" onclick="acceptOrder()">Accept & prepare</button>';
    } else if(o.order_status==='processing'){
      html='<button class="btn btn-terracotta" onclick="openModal(\'shipmentModal\')">Pack & schedule pickup</button>';
    } else if(shipment){
      html='<button class="btn btn-label" onclick="openShippingLabelModal()"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>Print Shipping Label</button>';
      if(nextShipmentStatus(shipment.status)){
        html+=` <button class="btn btn-terracotta" onclick="advanceShipment()">Simulate: Mark ${label(nextShipmentStatus(shipment.status))}</button>`;
      }
    }
    document.getElementById('detailActions').innerHTML=html;
  }

  function renderShipment(s){
    const panel=document.getElementById('shipmentPanel');
    if(!s){
      panel.innerHTML='<div class="panel-title"><h2>Delivery & tracking</h2><span class="status-badge status-confirmed">Not created</span></div><div class="next-step"><div class="next-step-label">Next step</div><p>Accept this order, pack it and schedule a courier pickup.</p></div>';
      return;
    }
    const events=[...(s.tracking||[])].sort((a,b)=>new Date(b.tracked_at)-new Date(a.tracked_at));
    const trackLink = s.tracking_url ? `<a href="${esc(s.tracking_url)}" target="_blank" class="tracking-link">Track on ${esc(s.shipping_partner)} ↗</a>` : '';
    
    panel.innerHTML=`<div class="panel-title"><h2>Delivery & tracking</h2>${badge(s.status)}</div>
    <div class="shipment-card">
      <div class="shipment-head">
        <div>
          <div class="tracking-number">${esc(s.shipping_partner)} · AWB: ${esc(s.tracking_number)}</div>
          ${trackLink}
          <div class="shipment-meta" style="margin-top:6px;">Package: ${s.package_weight} kg · ${s.package_length} × ${s.package_width} × ${s.package_height} cm · Est. Delivery: ${formatDate(s.expected_delivery_at)}</div>
        </div>
        <button class="btn btn-outline btn-sm" onclick="openShippingLabelModal()">Print Label</button>
      </div>
      <div class="timeline">
        ${events.map(e=>`<div class="timeline-event"><div class="event-title">${label(e.status)}</div><div class="event-meta">${esc(e.location||'Logistics Hub')} · ${formatDate(e.tracked_at)}</div>${e.description?`<div class="event-description">${esc(e.description)}</div>`:''}</div>`).join('')}
      </div>
    </div>`;
  }

  function openShippingLabelModal() {
    const o = currentOrder;
    const parent = o.order || {};
    const s = (o.shipments || [])[0] || {};
    const vendor = o.vendor || {};
    const items = o.order_items || [];
    const pin = parent.shipping_postal_code || '275304';
    const hub = (parent.shipping_city || 'DEL').substring(0, 3).toUpperCase() + '/' + pin.substring(0, 3);
    const awb = s.tracking_number || ('DLHV' + Date.now().toString().slice(-8));

    const html = `
      <div class="label-header">
        <div>
          <div class="label-brand">M E E T A</div>
          <div style="font-size:10px; font-weight:700; color:#444;">EXPRESS LOGISTICS · PREPAID</div>
        </div>
        <div class="label-hub">${esc(hub)}</div>
      </div>

      <div class="label-barcode-box">
        ${generateBarcodeSVG(awb)}
        <div class="label-awb">AWB: ${esc(awb)}</div>
        <div style="font-size:11px; font-weight:700; color:#333; margin-top:2px;">Courier: ${esc(s.shipping_partner || 'Delhivery Surface')}</div>
      </div>

      <div class="label-grid">
        <div class="label-col">
          <div class="label-title">Deliver To (Customer)</div>
          <div class="label-address">
            <strong>${esc(parent.shipping_full_name)}</strong><br>
            ${esc(parent.shipping_address_line_1 || '')}<br>
            ${parent.shipping_address_line_2 ? esc(parent.shipping_address_line_2) + '<br>' : ''}
            ${esc(parent.shipping_city)}, ${esc(parent.shipping_state)} - <strong>${esc(pin)}</strong><br>
            Phone: ${esc(parent.shipping_phone)}
          </div>
        </div>
        <div class="label-col">
          <div class="label-title">Shipped By (Seller Return)</div>
          <div class="label-address">
            <strong>${esc(vendor.store_name || 'Artisan Seller')}</strong><br>
            ${esc(vendor.business_name || 'Meeta Verified Artisan')}<br>
            GSTIN: ${esc(vendor.gst_number || '07AABCU9603R1ZM')}<br>
            Contact: ${esc(vendor.support_email || 'seller@meeta.in')}
          </div>
        </div>
      </div>

      <div class="label-meta-strip">
        <div class="label-meta-cell"><strong>Weight</strong>: ${s.package_weight || '0.85'} KG</div>
        <div class="label-meta-cell"><strong>Dims</strong>: ${s.package_length || '25'}×${s.package_width || '18'}×${s.package_height || '12'} cm</div>
        <div class="label-meta-cell"><strong>Payment</strong>: PREPAID</div>
      </div>

      <div style="padding:8px 0 4px;">
        <div class="label-title">Package Contents (Order ${esc(o.sub_order_number)})</div>
        <table class="label-table">
          <thead>
            <tr>
              <th>Item / Description</th>
              <th style="text-align:center;">Qty</th>
              <th style="text-align:right;">Amount</th>
            </tr>
          </thead>
          <tbody>
            ${items.map(i => `
              <tr>
                <td><strong>${esc(i.product_name)}</strong><br><span style="color:#666; font-size:10px;">SKU: ${esc(i.sku || 'N/A')}</span></td>
                <td style="text-align:center;">${i.quantity}</td>
                <td style="text-align:right;">${money(i.total_amount)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>

      <div style="font-size:9px; color:#555; text-align:center; margin-top:8px; border-top:1px dashed #999; padding-top:6px;">
        Standard Marketplace Handling. This order has been packed and inspected under CCTV.
      </div>
    `;

    document.getElementById('labelContent').innerHTML = html;
    openModal('labelModal');
  }

  const transitions={ready_for_pickup:'picked_up',picked_up:'in_transit',in_transit:'out_for_delivery',out_for_delivery:'delivered'};
  const nextShipmentStatus=status=>transitions[status]||null;

  async function loadOrder(){
    try{
      const res=await fetch(`${API_BASE}/orders/${ORDER_ID}`,{headers:headers(false)});
      const json=await res.json();
      if(res.status===401){location.href=LOGIN_URL;return}
      if(!res.ok||!json.success)throw new Error(apiError(json));
      currentOrder=json.data;
      render();
    }catch(e){
      document.getElementById('loadingState').textContent=e.message;
      toast(e.message,true);
    }
  }

  async function request(url,method,payload){
    const res=await fetch(url,{method,headers:headers(),body:JSON.stringify(payload)});
    const json=await res.json();
    if(!res.ok||!json.success)throw new Error(apiError(json));
    return json;
  }

  async function acceptOrder(){
    try{
      const json=await request(`${API_BASE}/orders/${ORDER_ID}/status`,'PATCH',{status:'processing'});
      toast(json.message);
      await loadOrder();
    }catch(e){toast(e.message,true)}
  }

  async function rejectOrder(){
    const reason=document.getElementById('rejectReason').value.trim(),error=document.getElementById('rejectError');
    if(reason.length<5){error.textContent='Please enter at least 5 characters.';error.style.display='block';return}
    try{
      const json=await request(`${API_BASE}/orders/${ORDER_ID}/status`,'PATCH',{status:'cancelled',reason});
      closeModal('rejectModal');
      toast(json.message);
      await loadOrder();
    }catch(e){error.textContent=e.message;error.style.display='block'}
  }

  async function createShipment(){
    const error=document.getElementById('shipmentError');
    const partner=document.getElementById('partner').value.trim()||'Delhivery Surface';
    const slot=document.getElementById('pickupSlot').value;
    const remarks=document.getElementById('shipmentRemarks').value.trim();
    const finalRemarks = `Pickup Slot: ${slot.replace('_', ' ').toUpperCase()}${remarks ? ' · ' + remarks : ''}`;

    const payload={
      weight:Number(document.getElementById('weight').value),
      length:Number(document.getElementById('length').value),
      width:Number(document.getElementById('width').value),
      height:Number(document.getElementById('height').value),
      shipping_partner:partner,
      remarks:finalRemarks
    };
    
    const expected=document.getElementById('expectedDelivery').value;
    if(expected) payload.expected_delivery_at=new Date(expected).toISOString();

    if(!payload.weight||!payload.length||!payload.width||!payload.height){
      error.textContent='Weight and all three dimensions are required.';
      error.style.display='block';
      return;
    }

    try{
      const json=await request(`${API_BASE}/orders/${ORDER_ID}/shipment`,'POST',payload);
      closeModal('shipmentModal');
      toast('Shipment booked and manifest generated successfully!');
      await loadOrder();
      // Prompt vendor to print shipping label immediately
      openShippingLabelModal();
    }catch(e){
      error.textContent=e.message;
      error.style.display='block';
    }
  }

  async function advanceShipment(){
    const shipment=currentOrder.shipments?.[0],status=nextShipmentStatus(shipment?.status);
    if(!status)return;
    const partner = shipment.shipping_partner || 'Courier';
    const descMap = {
      picked_up: `Package picked up by ${partner} logistics agent from vendor warehouse.`,
      in_transit: `Package in transit via ${partner} National Sorting Hub.`,
      out_for_delivery: `Package is out for delivery with ${partner} delivery executive.`,
      delivered: `Package successfully delivered to customer with OTP confirmation.`
    };
    try{
      const json=await request(`${API_BASE}/shipments/${shipment.id}/status`,'PATCH',{
        status,
        description: descMap[status] || `Courier marked package ${label(status)}.`
      });
      toast(json.message);
      await loadOrder();
    }catch(e){toast(e.message,true)}
  }

  function openModal(id){document.getElementById(id).classList.add('open')}
  function closeModal(id){document.getElementById(id).classList.remove('open');const err=document.querySelector(`#${id} .form-error`);if(err)err.style.display='none';}
  document.querySelectorAll('.modal-overlay').forEach(el=>el.addEventListener('click',e=>{if(e.target===el)closeModal(el.id)}));

  // Auto set expected delivery date default (+4 days) in datetime-local format
  const now = new Date();
  now.setDate(now.getDate() + 4);
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  const defDelivery = now.toISOString().slice(0, 16);
  const expInput = document.getElementById('expectedDelivery');
  if(expInput) expInput.value = defDelivery;

  loadOrder();
</script>
@endpush

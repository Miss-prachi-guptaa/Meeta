@extends('layouts.storefront')
@section('title','Checkout | MEETA')
@push('styles')<style>.checkout-page{padding-top:45px}.lead{color:var(--brown)}.checkout-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:45px;margin-top:38px}.step{display:flex;align-items:center;gap:14px;margin:28px 0 18px}.num{background:var(--brown);color:#fff;padding:9px 13px;border-radius:9px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.field.full{grid-column:1/-1}.field label{display:block;color:var(--muted);font-size:12px;margin-bottom:8px}.field input,.field select,.field textarea{width:100%;padding:14px;border:1px solid #bda99d;background:transparent}.shipping{display:block;border:1px solid var(--line);background:#fff;padding:18px;margin-bottom:12px}.payment{border:1px solid var(--line);background:#eee5e0;padding:22px}.summary{background:#e9dfda;border:1px solid #cdb8ab;padding:25px;position:sticky;top:105px;height:max-content}.summary-item{display:grid;grid-template-columns:65px 1fr auto;gap:12px;margin:16px 0}.summary-item img{width:65px;height:65px;object-fit:cover}.row{display:flex;justify-content:space-between;padding:12px 0}.total{border-top:1px solid #cdb8ab;font-size:22px;color:var(--brown)}.place{width:100%;margin-top:24px}.saved{border:1px solid var(--line);padding:12px;margin-bottom:15px;background:#fff}@media(max-width:800px){.checkout-grid,.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.summary{position:static}}</style>@endpush
@section('content')<main class="checkout-page wrap"><div class="lead">Finalize Your Story</div><h1>Review your selection and secure your artisanal delivery.</h1><div id="checkout"><div class="loading">Preparing secure checkout…</div></div></main>@endsection
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script><script>
if(!protectPage())throw new Error('Authentication required');let shipping='standard',summary=null,addresses=[];const checkoutParams=new URLSearchParams(location.search),checkoutMode=checkoutParams.get('mode')==='buy_now'?'buy_now':'cart',buyNowVariant=Number(checkoutParams.get('product_variant_id')),buyNowQuantity=Number(checkoutParams.get('quantity')||1);const checkoutQuery=()=>{const q=new URLSearchParams({shipping_method:shipping,mode:checkoutMode});if(checkoutMode==='buy_now'){q.set('product_variant_id',buyNowVariant);q.set('quantity',buyNowQuantity)}return q.toString()};
async function init(){try{const [cartRes,addressRes]=await Promise.all([api('/api/customer/checkout?'+checkoutQuery()),api('/api/customer/addresses')]);summary=cartRes.data;addresses=addressRes.data;render()}catch(e){document.getElementById('checkout').innerHTML='<div class="loading">'+e.message+' <a href="'+(checkoutMode==='buy_now'?'/shop':'/cart')+'">Return to shopping</a></div>'}}
function render(){const def=addresses.find(a=>a.is_default)||addresses[0];document.getElementById('checkout').innerHTML=`<div class="checkout-grid"><section><div class="step"><span class="num">1</span><h3>Shipping Information</h3></div>${addresses.length?`<label>Saved address</label><select id="savedAddress" class="saved"><option value="">Use a new address</option>${addresses.map(a=>`<option value="${a.id}" ${a.id===def?.id?'selected':''}>${a.label} — ${a.full_address||a.address_line_1}</option>`).join('')}</select>`:''}<div id="addressForm" class="form-grid" style="${def?'display:none':''}"><div class="field full"><label>FULL NAME</label><input id="full_name"></div><div class="field full"><label>DELIVERY ADDRESS</label><input id="address_line_1"></div><div class="field"><label>CITY</label><input id="city"></div><div class="field"><label>STATE</label><input id="state"></div><div class="field"><label>PIN CODE</label><input id="postal_code"></div><div class="field"><label>PHONE</label><input id="phone"></div></div><div class="step"><span class="num">2</span><h3>Delivery Method</h3></div><label class="shipping"><input type="radio" name="shipping" value="standard" checked> <strong>Standard Heritage Shipping</strong><br><small>5–7 business days · ${summary.shipping_amount?'₹'+summary.shipping_amount:'Free'}</small></label><label class="shipping"><input type="radio" name="shipping" value="express"> <strong>Express Artisan Delivery</strong><br><small>2–3 business days · ₹150</small></label><div class="step"><span class="num">3</span><h3>Payment Method</h3></div><div class="payment" style="display:flex; flex-direction:column; gap:12px;">
<label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; background:var(--bg-card,#fff); border:1.5px solid var(--border-color,#e8dcc9); border-radius:10px; padding:14px;">
  <input type="radio" name="payment_choice" value="razorpay" checked style="margin-top:3px;">
  <div>
    <strong style="color:var(--terracotta-dark,#a5451f); font-size:15px;">Pay Online via Razorpay (Instant)</strong>
    <p style="margin:4px 0 0; color:var(--text-secondary,#6b5a45); font-size:13px;">UPI (Google Pay, PhonePe, Paytm), Credit/Debit Card, NetBanking</p>
  </div>
</label>
<label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; background:var(--bg-card,#fff); border:1.5px solid var(--border-color,#e8dcc9); border-radius:10px; padding:14px;">
  <input type="radio" name="payment_choice" value="cod" style="margin-top:3px;">
  <div>
    <strong style="font-size:15px;">Cash on Delivery (COD)</strong>
    <p style="margin:4px 0 0; color:var(--text-secondary,#6b5a45); font-size:13px;">Pay securely with cash or UPI when package is delivered.</p>
  </div>
</label>
</div><button id="place" class="btn primary place">Place Order · ₹${Number(summary.total).toLocaleString('en-IN')} →</button></section><aside class="summary"><h3>Order Summary</h3>${summary.items.map(i=>`<div class="summary-item"><img src="${i.image}"><div><strong>${i.product}</strong><br><small>Qty: ${i.quantity} ${i.variant?'· '+i.variant:''}</small></div><strong>₹${Number(i.line_total).toLocaleString('en-IN')}</strong></div>`).join('')}<div class="row"><span>Subtotal</span><span>₹${summary.subtotal}</span></div><div class="row"><span>Shipping</span><span>₹${summary.shipping_amount}</span></div><div class="row total"><strong>Total</strong><strong>₹${summary.total}</strong></div><p>♧ Your purchase directly supports independent artisans.</p></aside></div>`;bind()}
function bind(){const saved=document.getElementById('savedAddress');if(saved)saved.onchange=()=>document.getElementById('addressForm').style.display=saved.value?'none':'grid';document.querySelectorAll('[name=shipping]').forEach(r=>r.onchange=async()=>{shipping=r.value;summary=(await api('/api/customer/checkout?'+checkoutQuery())).data;render()});document.getElementById('place').onclick=placeOrder}
async function placeOrder(){
  const btn = document.getElementById('place');
  btn.disabled = true;

  try {
    let addressId = document.getElementById('savedAddress')?.value;
    if (!addressId) {
      const addrPayload = {
        label: 'Home',
        full_name: value('full_name'),
        address_line_1: value('address_line_1'),
        city: value('city'),
        state: value('state'),
        postal_code: value('postal_code'),
        phone: value('phone'),
        country: 'India',
        is_default: true
      };
      if (Object.values(addrPayload).some(v => v === '')) {
        throw Error('Please complete your shipping address');
      }
      addressId = (await api('/api/customer/addresses', { method: 'POST', body: JSON.stringify(addrPayload) })).data.id;
    }

    const selectedPayment = document.querySelector('input[name="payment_choice"]:checked')?.value || 'razorpay';

    const orderPayload = {
      address_id: Number(addressId),
      shipping_method: shipping,
      payment_method: selectedPayment,
      mode: checkoutMode
    };

    if (checkoutMode === 'buy_now') {
      orderPayload.product_variant_id = buyNowVariant;
      orderPayload.quantity = buyNowQuantity;
    }

    // Case 1: Cash on Delivery
    if (selectedPayment === 'cod') {
      const result = await api('/api/customer/checkout/place-order', {
        method: 'POST',
        body: JSON.stringify(orderPayload)
      });
      document.getElementById('checkout').innerHTML = `
        <div class="loading">
          <h2>Order Placed Successfully! (COD)</h2>
          <p>Your order number is <strong>${result.data.order_number}</strong>.</p>
          <p style="margin-top:10px; color:var(--text-secondary,#6b5a45);">Our vendor will prepare and dispatch your package soon.</p>
          <a class="btn primary" style="margin-top:20px; display:inline-block;" href="/">Continue Shopping</a>
        </div>
      `;
      return;
    }

    // Case 2: Online Payment via Razorpay
    btn.textContent = 'Opening Secure Gateway...';

    const rzpInit = await api('/api/customer/checkout/create-razorpay-order?' + checkoutQuery(), {
      method: 'POST',
      body: JSON.stringify({ shipping_method: shipping })
    });

    if (!rzpInit.success || !rzpInit.data) {
      throw new Error(rzpInit.message || 'Unable to initialize Razorpay payment.');
    }

    const rzpData = rzpInit.data;

    const options = {
      key: rzpData.key,
      amount: rzpData.amount,
      currency: rzpData.currency,
      name: 'MEETA Artisan Marketplace',
      description: 'Handcrafted Heritage Goods Purchase',
      image: '/favicon.ico',
      order_id: rzpData.razorpay_order_id,
      handler: async function (response) {
        btn.textContent = 'Verifying Payment...';
        try {
          const finalPayload = {
            ...orderPayload,
            payment_method: 'razorpay',
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_order_id: response.razorpay_order_id,
            razorpay_signature: response.razorpay_signature
          };

          const placeRes = await api('/api/customer/checkout/place-order', {
            method: 'POST',
            body: JSON.stringify(finalPayload)
          });

          document.getElementById('checkout').innerHTML = `
            <div class="loading">
              <h2 style="color:var(--success,#3f7d4f);">Payment Successful!</h2>
              <p>Your order number is <strong>${placeRes.data.order_number}</strong>.</p>
              <p style="margin-top:8px; color:var(--text-secondary,#6b5a45);">Transaction ID: <code>${response.razorpay_payment_id}</code></p>
              <p style="margin-top:10px; color:var(--text-secondary,#6b5a45);">The vendor has received your order and the 24-hour fulfillment timer has started!</p>
              <a class="btn primary" style="margin-top:20px; display:inline-block;" href="/">Continue Shopping</a>
            </div>
          `;
        } catch (err) {
          toast(err.message || 'Error finalizing order.');
          btn.disabled = false;
          btn.textContent = 'Place Order';
        }
      },
      prefill: {
        name: value('full_name') || 'Customer',
        contact: value('phone') || '9876543210'
      },
      theme: {
        color: '#c1592c'
      },
      modal: {
        ondismiss: function () {
          btn.disabled = false;
          btn.textContent = 'Place Order · ₹' + Number(summary.total).toLocaleString('en-IN') + ' →';
          toast('Payment window closed. You can retry anytime.');
        }
      }
    };

    const rzp = new Razorpay(options);
    rzp.on('payment.failed', function (resp) {
      btn.disabled = false;
      btn.textContent = 'Place Order · ₹' + Number(summary.total).toLocaleString('en-IN') + ' →';
      toast('Payment Failed: ' + (resp.error.description || 'Transaction declined.'));
    });
    rzp.open();

  } catch (e) {
    toast(e.message);
    btn.disabled = false;
    btn.textContent = 'Place Order · ₹' + Number(summary?.total || 0).toLocaleString('en-IN') + ' →';
  }
}
const value=id=>document.getElementById(id)?.value.trim()||'';init();
</script>@endpush

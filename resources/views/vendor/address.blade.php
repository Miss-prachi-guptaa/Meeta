<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Business Address - MEETA Vendor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root{
    --cream:#F7F3EA;
    --dark:#2B1D0E;
    --terracotta:#C1743A;
    --gold:#C9A227;
    --text:#3A2C1A;
    --muted:#9C8F7A;
    --border:#E4DCC8;
    --danger:#B3261E;
    --success:#2E7D32;
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    font-family:'Segoe UI', -apple-system, sans-serif;
    background:var(--cream);
    color:var(--text);
  }
  .topbar{
    background:var(--dark);
    color:var(--gold);
    padding:18px 40px;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .topbar .logo{ font-size:22px; font-weight:700; color:#fff; letter-spacing:1px;}
  .topbar .tag{ font-size:11px; letter-spacing:2px; color:var(--gold);}
  .wrapper{ max-width:760px; margin:40px auto; padding:0 20px 60px; }
  .steps{
    display:flex; justify-content:space-between; margin-bottom:36px; position:relative;
  }
  .steps::before{
    content:''; position:absolute; top:14px; left:0; right:0;
    height:2px; background:var(--border); z-index:0;
  }
  .step{ position:relative; z-index:1; text-align:center; flex:1; font-size:12px; color:var(--muted); }
  .step .dot{
    width:28px;height:28px;border-radius:50%;
    background:#fff;border:2px solid var(--border);
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 6px; font-weight:600; font-size:13px; color:var(--muted);
  }
  .step.active .dot{ background:var(--terracotta); border-color:var(--terracotta); color:#fff; }
  .step.active{ color:var(--text); font-weight:600; }
  .step.done .dot{ background:var(--success); border-color:var(--success); color:#fff; }
  .step.done{ color:var(--text); }
  .card{
    background:#fff; border:1px solid var(--border); border-radius:12px;
    padding:36px; box-shadow:0 4px 18px rgba(43,29,14,0.05);
  }
  h1{ font-size:24px; margin:0 0 6px; }
  .subtitle{ color:var(--muted); font-size:14px; margin-bottom:28px; }
  .field{ margin-bottom:22px; }
  .field label{
    display:block; font-size:12px; letter-spacing:1px;
    color:var(--muted); text-transform:uppercase; margin-bottom:6px;
  }
  .field input[type=text]{
    width:100%; padding:12px 14px; border:1px solid var(--border);
    border-radius:8px; font-size:14px; background:var(--cream); color:var(--text);
  }
  .row2{ display:flex; gap:20px; flex-wrap:wrap; }
  .row2 .field{ flex:1; min-width:220px; }
  .error-text{ color:var(--danger); font-size:12px; margin-top:5px; display:none; }
  .field.has-error input{ border-color:var(--danger); }
  .field.has-error .error-text{ display:block; }
  .checkbox-row{
    display:flex; align-items:center; gap:10px; margin-bottom:28px;
    background:var(--cream); border:1px solid var(--border);
    border-radius:8px; padding:14px 18px;
  }
  .checkbox-row input{ width:16px; height:16px; }
  .checkbox-row label{ font-size:13px; color:var(--text); }
  .actions{ display:flex; justify-content:space-between; align-items:center; margin-top:32px; gap:16px; flex-wrap:wrap; }
  .btn{ padding:13px 26px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; }
  .btn:disabled{ opacity:.6; cursor:not-allowed; }
  .btn-outline{ background:#fff; border:1px solid var(--terracotta); color:var(--terracotta); }
  .btn-primary{ background:var(--terracotta); color:#fff; }
  .btn:hover:not(:disabled){ opacity:.9; }
  .toast{
    position:fixed; top:20px; right:20px; padding:14px 20px; border-radius:8px;
    font-size:14px; color:#fff; display:none; z-index:50; max-width:320px;
  }
  .toast.success{ background:var(--success); }
  .toast.error{ background:var(--danger); }
  .loading-overlay{ text-align:center; padding:60px 0; color:var(--muted); font-size:14px; }
</style>
</head>
<body>

<div class="topbar">
  <span class="logo">MEETA</span>
  <span class="tag">VENDOR ONBOARDING</span>
</div>

<div class="wrapper">

  <div class="steps">
    <div class="step done"><div class="dot">✓</div>Profile</div>
    <div class="step active"><div class="dot">2</div>Address</div>
    <div class="step"><div class="dot">3</div>Bank</div>
    <div class="step"><div class="dot">4</div>Documents</div>
    <div class="step"><div class="dot">5</div>Review</div>
  </div>

  <div class="card">
    <div id="initialLoader" class="loading-overlay">Loading your address…</div>

    <div id="formArea" style="display:none;">
      <h1>Add your business address</h1>
      <div class="subtitle">This is where your store operates from. You can edit this later.</div>

      <form id="addressForm" novalidate>
        <div class="field" id="address_line_1Field">
          <label>Address Line 1</label>
          <input type="text" id="address_line_1" placeholder="Shop / Building, Street">
          <div class="error-text"></div>
        </div>

        <div class="field" id="address_line_2Field">
          <label>Address Line 2</label>
          <input type="text" id="address_line_2" placeholder="Area, Locality (optional)">
          <div class="error-text"></div>
        </div>

        <div class="field" id="landmarkField">
          <label>Landmark</label>
          <input type="text" id="landmark" placeholder="e.g. Near City Mall (optional)">
          <div class="error-text"></div>
        </div>

        <div class="row2">
          <div class="field" id="cityField">
            <label>City</label>
            <input type="text" id="city" placeholder="City">
            <div class="error-text"></div>
          </div>
          <div class="field" id="stateField">
            <label>State</label>
            <input type="text" id="state" placeholder="State">
            <div class="error-text"></div>
          </div>
        </div>

        <div class="row2">
          <div class="field" id="postal_codeField">
            <label>Postal Code</label>
            <input type="text" id="postal_code" placeholder="Postal Code">
            <div class="error-text"></div>
          </div>
          <div class="field" id="countryField">
            <label>Country</label>
            <input type="text" id="country" placeholder="Country" value="India">
            <div class="error-text"></div>
          </div>
        </div>

        <div class="checkbox-row">
          <input type="checkbox" id="is_default">
          <label for="is_default">Set as default address</label>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-outline" id="saveDraftBtn">Save as Draft</button>
          <button type="button" class="btn btn-primary" id="saveContinueBtn">Save &amp; Continue →</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
  const token = localStorage.getItem('meeta_token');
  const API_BASE = '/api/vendor/address';

  if(!token){
    window.location.href = '/login';
    return;
  }

  const initialLoader   = document.getElementById('initialLoader');
  const formArea        = document.getElementById('formArea');
  const toast           = document.getElementById('toast');
  const saveDraftBtn    = document.getElementById('saveDraftBtn');
  const saveContinueBtn = document.getElementById('saveContinueBtn');

  const fields = ['address_line_1','address_line_2','landmark','city','state','postal_code','country'];
  const els = {};
  fields.forEach(f => els[f] = document.getElementById(f));
  const isDefaultEl = document.getElementById('is_default');

  let existingId = null; // null => create (POST), otherwise update (PUT)

  function showToast(message, type){
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
  }

  function clearErrors(){
    document.querySelectorAll('.field').forEach(el => {
      el.classList.remove('has-error');
      const err = el.querySelector('.error-text');
      if(err) err.textContent = '';
    });
  }

  function showFieldError(name, message){
    const fieldEl = document.getElementById(name + 'Field');
    if(fieldEl){
      fieldEl.classList.add('has-error');
      fieldEl.querySelector('.error-text').textContent = message;
    }
  }

  async function loadAddress(){
    try{
      const res = await fetch(API_BASE, {
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
      });

      if(res.status === 401){
        localStorage.removeItem('meeta_token');
        window.location.href = '/login';
        return;
      }

      const json = await res.json();

      if(!res.ok || !json.success){
        showToast('Could not load your address. Please refresh.', 'error');
        return;
      }

      const list = json.data || [];
      const existing = list.length ? list[0] : null;

      if(existing){
        existingId = existing.id;
        fields.forEach(f => { if(els[f]) els[f].value = existing[f] || (f === 'country' ? 'India' : ''); });
        isDefaultEl.checked = !!existing.is_default;
      }

      initialLoader.style.display = 'none';
      formArea.style.display = 'block';
    } catch(err){
      showToast('Network error while loading address.', 'error');
    }
  }

  async function saveAddress(redirectAfter){
    clearErrors();

    const btn = redirectAfter ? saveContinueBtn : saveDraftBtn;
    const otherBtn = redirectAfter ? saveDraftBtn : saveContinueBtn;
    btn.disabled = true;
    otherBtn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Saving…';

    const payload = { type: 'business' };
    fields.forEach(f => payload[f] = els[f].value);
    payload.is_default = isDefaultEl.checked ? 1 : 0;
    if(existingId) payload.id = existingId;

    try{
      const res = await fetch(API_BASE, {
        method: existingId ? 'PUT' : 'POST',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const json = await res.json();

      if(res.status === 401){
        localStorage.removeItem('meeta_token');
        window.location.href = '/login';
        return;
      }

      if(res.status === 422 && json.errors){
        Object.keys(json.errors).forEach(field => {
          showFieldError(field, json.errors[field][0]);
        });
        showToast('Please fix the highlighted fields.', 'error');
        return;
      }

      if(!res.ok || !json.success){
        showToast(json.message || 'Something went wrong. Please try again.', 'error');
        return;
      }

      if(json.data && json.data.id) existingId = json.data.id;

      showToast('Address saved successfully.', 'success');

      if(redirectAfter){
        window.location.href = new URLSearchParams(window.location.search).get('return') || '/vendor/bank-account';
      }
    } catch(err){
      showToast('Network error. Please check your connection.', 'error');
    } finally{
      btn.disabled = false;
      otherBtn.disabled = false;
      btn.textContent = originalText;
    }
  }

  saveDraftBtn.addEventListener('click', () => saveAddress(false));
  saveContinueBtn.addEventListener('click', () => saveAddress(true));

  async function guardState(){const res=await fetch('/api/vendor/profile',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}});const json=await res.json();const status=json.data?.status;if(status==='pending'){location.href='/vendor/pending';return false}if(status==='approved'){location.href='/vendor/dashboard';return false}return true}
  guardState().then(ok=>{if(ok)loadAddress()}).catch(()=>showToast('Could not verify application status.','error'));
})();
</script>

</body>
</html>

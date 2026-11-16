<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bank Account - MEETA Vendor</title>
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
    --warn-bg:#FBEFD9;
    --warn-text:#9A6A12;
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
  .card-header{ display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
  h1{ font-size:24px; margin:0 0 6px; }
  .subtitle{ color:var(--muted); font-size:14px; margin-bottom:28px; }
  .badge{
    font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    padding:6px 12px; border-radius:20px; white-space:nowrap; margin-top:2px;
  }
  .badge.verified{ background:#E6F4EA; color:var(--success); }
  .badge.pending{ background:var(--warn-bg); color:var(--warn-text); }
  .field{ margin-bottom:22px; }
  .field label{
    display:block; font-size:12px; letter-spacing:1px;
    color:var(--muted); text-transform:uppercase; margin-bottom:6px;
  }
  .field input[type=text], .field select{
    width:100%; padding:12px 14px; border:1px solid var(--border);
    border-radius:8px; font-size:14px; background:var(--cream); color:var(--text);
  }
  .field input[type=text]:disabled{ opacity:.65; cursor:not-allowed; }
  .row2{ display:flex; gap:20px; flex-wrap:wrap; }
  .row2 .field{ flex:1; min-width:220px; }
  .error-text{ color:var(--danger); font-size:12px; margin-top:5px; display:none; }
  .field.has-error input, .field.has-error select{ border-color:var(--danger); }
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
    <div class="step done"><div class="dot">✓</div>Address</div>
    <div class="step active"><div class="dot">3</div>Bank</div>
    <div class="step"><div class="dot">4</div>Documents</div>
    <div class="step"><div class="dot">5</div>Review</div>
  </div>

  <div class="card">
    <div id="initialLoader" class="loading-overlay">Loading your bank details…</div>

    <div id="formArea" style="display:none;">
      <div class="card-header">
        <div>
          <h1>Add your bank account</h1>
          <div class="subtitle">We use this to send your payouts. You can edit this later.</div>
        </div>
        <span class="badge pending" id="verifiedBadge" style="display:none;"></span>
      </div>

      <form id="bankForm" novalidate>
        <div class="field" id="account_holder_nameField">
          <label>Account Holder Name</label>
          <input type="text" id="account_holder_name" placeholder="As per bank records">
          <div class="error-text"></div>
        </div>

        <div class="row2">
          <div class="field" id="bank_nameField">
            <label>Bank Name</label>
            <input type="text" id="bank_name" placeholder="e.g. State Bank of India">
            <div class="error-text"></div>
          </div>
          <div class="field" id="branch_nameField">
            <label>Branch Name</label>
            <input type="text" id="branch_name" placeholder="Branch (optional)">
            <div class="error-text"></div>
          </div>
        </div>

        <div class="row2">
          <div class="field" id="account_numberField">
            <label>Account Number</label>
            <input type="text" id="account_number" placeholder="Account Number">
            <div class="error-text"></div>
          </div>
          <div class="field" id="ifsc_codeField">
            <label>IFSC Code</label>
            <input type="text" id="ifsc_code" placeholder="e.g. SBIN0001234" maxlength="11" style="text-transform:uppercase;">
            <div class="error-text"></div>
          </div>
        </div>

        <div class="row2">
          <div class="field" id="upi_idField">
            <label>UPI ID</label>
            <input type="text" id="upi_id" placeholder="example@upi (optional)">
            <div class="error-text"></div>
          </div>
          <div class="field" id="account_typeField">
            <label>Account Type</label>
            <select id="account_type">
              <option value="">Select type</option>
              <option value="savings">Savings</option>
              <option value="current">Current</option>
            </select>
            <div class="error-text"></div>
          </div>
        </div>

        <div class="checkbox-row">
          <input type="checkbox" id="is_primary">
          <label for="is_primary">Set as primary account</label>
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
  const API_BASE = '/api/vendor/bank-account';

  if(!token){
    window.location.href = '/login';
    return;
  }

  const initialLoader   = document.getElementById('initialLoader');
  const formArea        = document.getElementById('formArea');
  const toast           = document.getElementById('toast');
  const verifiedBadge   = document.getElementById('verifiedBadge');
  const saveDraftBtn    = document.getElementById('saveDraftBtn');
  const saveContinueBtn = document.getElementById('saveContinueBtn');

  const fields = ['account_holder_name','bank_name','branch_name','account_number','ifsc_code','upi_id','account_type'];
  const els = {};
  fields.forEach(f => els[f] = document.getElementById(f));
  const isPrimaryEl = document.getElementById('is_primary');

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

  function renderVerifiedBadge(isVerified){
    verifiedBadge.style.display = 'inline-block';
    if(isVerified){
      verifiedBadge.textContent = 'Verified';
      verifiedBadge.className = 'badge verified';
    } else {
      verifiedBadge.textContent = 'Verification Pending';
      verifiedBadge.className = 'badge pending';
    }
  }

  async function loadBankAccount(){
    try{
      const res = await fetch(API_BASE, {
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
      });

      if(res.status === 401){
        localStorage.removeItem('meeta_token');
        window.location.href = '/login';
        return;
      }

      if(res.status === 404){
        // no bank account yet — fresh form
        initialLoader.style.display = 'none';
        formArea.style.display = 'block';
        return;
      }

      const json = await res.json();

      if(!res.ok || !json.success){
        showToast('Could not load your bank details. Please refresh.', 'error');
        return;
      }

      const existing = json.data || null;

      if(existing){
        existingId = existing.id;
        fields.forEach(f => { if(els[f]) els[f].value = existing[f] || ''; });
        isPrimaryEl.checked = !!existing.is_primary;
        renderVerifiedBadge(!!existing.is_verified);
      }

      initialLoader.style.display = 'none';
      formArea.style.display = 'block';
    } catch(err){
      showToast('Network error while loading bank details.', 'error');
    }
  }

  async function saveBankAccount(redirectAfter){
    clearErrors();

    const btn = redirectAfter ? saveContinueBtn : saveDraftBtn;
    const otherBtn = redirectAfter ? saveDraftBtn : saveContinueBtn;
    btn.disabled = true;
    otherBtn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Saving…';

    const payload = {};
    fields.forEach(f => payload[f] = els[f].value);
    payload.is_primary = isPrimaryEl.checked ? 1 : 0;
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
      if(json.data) renderVerifiedBadge(!!json.data.is_verified);

      showToast('Bank details saved successfully.', 'success');

      if(redirectAfter){
        window.location.href = new URLSearchParams(window.location.search).get('return') || '/vendor/documents';
      }
    } catch(err){
      showToast('Network error. Please check your connection.', 'error');
    } finally{
      btn.disabled = false;
      otherBtn.disabled = false;
      btn.textContent = originalText;
    }
  }

  saveDraftBtn.addEventListener('click', () => saveBankAccount(false));
  saveContinueBtn.addEventListener('click', () => saveBankAccount(true));

  async function guardState(){const res=await fetch('/api/vendor/profile',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}});const json=await res.json();const status=json.data?.status;if(status==='pending'){location.href='/vendor/pending';return false}if(status==='approved'){location.href='/vendor/dashboard';return false}return true}
  guardState().then(ok=>{if(ok)loadBankAccount()}).catch(()=>showToast('Could not verify application status.','error'));
})();
</script>

</body>
</html>

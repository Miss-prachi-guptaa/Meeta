<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Review & Submit - MEETA Vendor</title>
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
  .topbar{justify-content:space-between}.brand-wrap{display:flex;align-items:center;gap:10px}.signout{margin-left:auto;background:transparent;border:1px solid #ffffff55;color:#fff;padding:9px 14px;border-radius:7px;cursor:pointer}.state-note{border-radius:10px;padding:14px 16px;margin-bottom:20px;background:var(--warn-bg);color:var(--warn-text);display:none}.state-note.rejected{display:block;background:#FBEAE5;color:var(--danger)}.state-note.pending{display:block}.view-only .edit-link{display:none}
  .wrapper{ max-width:820px; margin:40px auto; padding:0 20px 60px; }
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

  .page-head{ margin-bottom:24px; }
  h1{ font-size:24px; margin:0 0 6px; }
  .subtitle{ color:var(--muted); font-size:14px; }

  .card{
    background:#fff; border:1px solid var(--border); border-radius:12px;
    padding:26px 28px; box-shadow:0 4px 18px rgba(43,29,14,0.05);
    margin-bottom:20px;
  }
  .card-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
  .card-head h2{ margin:0; font-size:16px; }
  .edit-link{ font-size:13px; font-weight:600; color:var(--terracotta); text-decoration:none; }
  .edit-link:hover{ text-decoration:underline; }

  .kv-grid{ display:grid; grid-template-columns:1fr 1fr; gap:16px 24px; }
  .kv-grid.single{ grid-template-columns:1fr; }
  .kv{ font-size:14px; }
  .kv .k{ font-size:11px; letter-spacing:.5px; text-transform:uppercase; color:var(--muted); margin-bottom:3px; }
  .kv .v{ color:var(--text); word-break:break-word; }
  .kv .v.empty{ color:var(--muted); font-style:italic; }

  .badge{
    font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    padding:6px 12px; border-radius:20px; white-space:nowrap;
  }
  .badge.pending{ background:var(--warn-bg); color:var(--warn-text); }
  .badge.verified{ background:#E6F4EA; color:var(--success); }
  .badge.rejected{ background:#FBEAE5; color:var(--danger); }
  .badge.not-uploaded{ background:var(--cream); color:var(--muted); border:1px solid var(--border); }
  .badge.default-tag{ background:var(--cream); color:var(--muted); border:1px solid var(--border); margin-left:8px; }

  .doc-row{
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 0; border-bottom:1px solid var(--border);
    gap:12px; flex-wrap:wrap;
  }
  .doc-row:last-child{ border-bottom:none; }
  .doc-row .doc-info{ font-size:14px; }
  .doc-row .doc-info .doc-type{ font-weight:600; }
  .doc-row .doc-info .doc-number{ color:var(--muted); font-size:13px; }
  .doc-row .rejection-inline{ color:var(--danger); font-size:12px; margin-top:2px; }

  .thumbs{ display:flex; gap:14px; margin-bottom:18px; }
  .thumb-box{
    width:90px; height:90px; border-radius:8px; border:1px solid var(--border);
    background:var(--cream); display:flex; align-items:center; justify-content:center;
    overflow:hidden; font-size:11px; color:var(--muted); text-align:center;
  }
  .thumb-box img{ width:100%; height:100%; object-fit:cover; }

  .bottom-actions{ display:flex; justify-content:space-between; align-items:center; margin-top:32px; gap:16px; flex-wrap:wrap; }
  .btn{ padding:13px 26px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; }
  .btn:disabled{ opacity:.6; cursor:not-allowed; }
  .btn-outline{ background:#fff; border:1px solid var(--terracotta); color:var(--terracotta); text-decoration:none; display:inline-block; }
  .btn-primary{ background:var(--terracotta); color:#fff; }
  .btn:hover:not(:disabled){ opacity:.9; }

  .missing-note{
    background:var(--warn-bg); color:var(--warn-text); border:1px solid #F0DDAF;
    border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:20px;
  }

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
  <div class="brand-wrap"><span class="logo">MEETA</span><span class="tag">VENDOR ONBOARDING</span></div>
  <button type="button" class="signout" id="signOutBtn">Sign Out</button>
</div>

<div class="wrapper">

  <div class="steps">
    <div class="step done"><div class="dot">✓</div>Profile</div>
    <div class="step done"><div class="dot">✓</div>Address</div>
    <div class="step done"><div class="dot">✓</div>Bank</div>
    <div class="step done"><div class="dot">✓</div>Documents</div>
    <div class="step active"><div class="dot">5</div>Review</div>
  </div>

  <div id="initialLoader" class="loading-overlay">Loading your details…</div>

  <div id="pageArea" style="display:none;">
    <div class="page-head">
      <h1>Review &amp; Submit</h1>
      <div class="subtitle" id="pageSubtitle">Please confirm everything below is correct before submitting for verification.</div>
    </div>

    <div id="stateNote" class="state-note"></div>
    <div id="missingArea"></div>

    <!-- PROFILE CARD -->
    <div class="card" id="profileCard">
      <div class="card-head">
        <h2>Business / Profile Details</h2>
        <a href="/vendor/profile?return=/vendor/review" class="edit-link">Edit</a>
      </div>
      <div id="profileThumbs" class="thumbs" style="display:none;"></div>
      <div class="kv-grid" id="profileGrid"></div>
    </div>

    <!-- ADDRESS CARD -->
    <div class="card" id="addressCard">
      <div class="card-head">
        <h2>Business Address</h2>
        <a href="/vendor/address?return=/vendor/review" class="edit-link">Edit</a>
      </div>
      <div class="kv-grid" id="addressGrid"></div>
    </div>

    <!-- BANK CARD -->
    <div class="card" id="bankCard">
      <div class="card-head">
        <h2>Bank Account</h2>
        <a href="/vendor/bank-account?return=/vendor/review" class="edit-link">Edit</a>
      </div>
      <div class="kv-grid" id="bankGrid"></div>
    </div>

    <!-- DOCUMENTS CARD -->
    <div class="card" id="documentsCard">
      <div class="card-head">
        <h2>Uploaded Documents</h2>
        <a href="/vendor/documents?return=/vendor/review" class="edit-link">Edit</a>
      </div>
      <div id="documentsList"></div>
    </div>

    <div class="bottom-actions">
      <a href="/vendor/documents" class="btn btn-outline" id="backBtn">Back to Documents</a>
      <button type="button" class="btn btn-primary" id="submitBtn">Submit for Verification</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
  const token = localStorage.getItem('meeta_token');

  if(!token){
    window.location.href = '/login';
    return;
  }

  const authHeaders = {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  };

  const initialLoader = document.getElementById('initialLoader');
  const pageArea       = document.getElementById('pageArea');
  const missingArea    = document.getElementById('missingArea');
  const toast          = document.getElementById('toast');
  const submitBtn      = document.getElementById('submitBtn');
  const backBtn        = document.getElementById('backBtn');
  let applicationStatus = 'draft';

  // Required document types must match Step 4 (documents page)
  const REQUIRED_DOC_TYPES = [
    { key: 'aadhaar',         label: 'Aadhaar Card' },
    { key: 'pan',              label: 'PAN Card' },
    { key: 'cancelled_cheque', label: 'Cancelled Cheque / Bank Passbook' }
  ];

  const DOC_TYPE_LABELS = {
    aadhaar: 'Aadhaar Card',
    pan: 'PAN Card',
    gst: 'GST Certificate',
    shop_license: 'Shop / Trade License',
    cancelled_cheque: 'Cancelled Cheque / Bank Passbook'
  };

  function showToast(message, type){
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
  }

  function escapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function kv(label, value, empty){
    const isEmpty = value === null || value === undefined || value === '';
    return `
      <div class="kv">
        <div class="k">${label}</div>
        <div class="v ${isEmpty ? 'empty' : ''}">${isEmpty ? (empty || 'Not provided') : escapeHtml(value)}</div>
      </div>`;
  }

  function maskAccountNumber(num){
    if(!num) return '';
    const str = String(num);
    if(str.length <= 4) return str;
    return '•••• •••• ' + str.slice(-4);
  }

  function statusBadge(status){
    const map = {
      pending:  ['pending', 'Pending Review'],
      verified: ['verified', 'Verified'],
      rejected: ['rejected', 'Rejected'],
      approved: ['verified', 'Approved']
    };
    const [cls, label] = map[status] || ['not-uploaded', status || 'Unknown'];
    return `<span class="badge ${cls}">${label}</span>`;
  }

  async function fetchJson(url){
    const res = await fetch(url, { headers: authHeaders });
    if(res.status === 401){
      localStorage.removeItem('meeta_token');
      window.location.href = '/login';
      return null;
    }
    if(res.status === 404) return { success: true, data: null };
    return res.json();
  }

  function renderProfile(profile){
    const grid = document.getElementById('profileGrid');
    const thumbsWrap = document.getElementById('profileThumbs');

    if(!profile){
      grid.innerHTML = '<div class="kv"><div class="v empty">Profile details not found.</div></div>';
      return;
    }

    let thumbsHtml = '';
    if(profile.logo){
      thumbsHtml += `<div class="thumb-box"><img src="${escapeHtml(profile.logo)}" alt="Logo"></div>`;
    }
    if(profile.banner){
      thumbsHtml += `<div class="thumb-box"><img src="${escapeHtml(profile.banner)}" alt="Banner"></div>`;
    }
    if(thumbsHtml){
      thumbsWrap.innerHTML = thumbsHtml;
      thumbsWrap.style.display = 'flex';
    }

    grid.innerHTML = [
      kv('Store Name', profile.store_name),
      kv('Business Name', profile.business_name),
      kv('GST Number', profile.gst_number),
      kv('PAN Number', profile.pan_number),
      kv('Support Email', profile.support_email),
      kv('Support Phone', profile.support_phone),
    ].join('') + `
      <div class="kv" style="grid-column:1 / -1;">
        <div class="k">Description</div>
        <div class="v ${!profile.description ? 'empty' : ''}">${profile.description ? escapeHtml(profile.description) : 'Not provided'}</div>
      </div>`;
  }

  function renderAddress(address){
    const grid = document.getElementById('addressGrid');

    if(!address){
      grid.innerHTML = '<div class="kv"><div class="v empty">Address not found.</div></div>';
      return;
    }

    grid.innerHTML = [
      kv('Address Line 1', address.address_line_1),
      kv('Address Line 2', address.address_line_2),
      kv('Landmark', address.landmark),
      kv('City', address.city),
      kv('State', address.state),
      kv('Postal Code', address.postal_code),
      kv('Country', address.country),
    ].join('') + `
      <div class="kv">
        <div class="k">Default Address</div>
        <div class="v">${address.is_default ? 'Yes' : 'No'}</div>
      </div>`;
  }

  function renderBank(bank){
    const grid = document.getElementById('bankGrid');

    if(!bank){
      grid.innerHTML = '<div class="kv"><div class="v empty">Bank account not found.</div></div>';
      return;
    }

    grid.innerHTML = [
      kv('Account Holder Name', bank.account_holder_name),
      kv('Bank Name', bank.bank_name),
      kv('Branch Name', bank.branch_name),
      kv('Account Number', maskAccountNumber(bank.account_number)),
      kv('IFSC Code', bank.ifsc_code),
      kv('UPI ID', bank.upi_id),
      kv('Account Type', bank.account_type ? (bank.account_type.charAt(0).toUpperCase() + bank.account_type.slice(1)) : null),
    ].join('') + `
      <div class="kv">
        <div class="k">Verification Status</div>
        <div class="v">${statusBadge(bank.is_verified ? 'verified' : 'pending')}${bank.is_primary ? '<span class="badge default-tag">Primary</span>' : ''}</div>
      </div>`;
  }

  function renderDocuments(list){
    const container = document.getElementById('documentsList');

    if(!list || !list.length){
      container.innerHTML = '<div class="kv"><div class="v empty">No documents uploaded yet.</div></div>';
      return;
    }

    container.innerHTML = list.map(doc => {
      const label = DOC_TYPE_LABELS[doc.document_type] || doc.document_type;
      const rejectionHtml = (doc.status === 'rejected' && doc.remarks)
        ? `<div class="rejection-inline">Reason: ${escapeHtml(doc.remarks)}</div>`
        : '';
      return `
        <div class="doc-row">
          <div class="doc-info">
            <div class="doc-type">${escapeHtml(label)}</div>
            <div class="doc-number">${escapeHtml(doc.document_number || '')}</div>
            ${rejectionHtml}
          </div>
          ${statusBadge(doc.status)}
        </div>`;
    }).join('');
  }

  function renderMissingWarning(documentsList){
    const uploadedTypes = (documentsList || []).map(d => d.document_type);
    const missing = REQUIRED_DOC_TYPES.filter(t => !uploadedTypes.includes(t.key));
    const rejected = (documentsList || []).filter(d => REQUIRED_DOC_TYPES.some(t => t.key === d.document_type) && d.status === 'rejected');

    if(missing.length || rejected.length){
      missingArea.innerHTML = `
        <div class="missing-note">
          ${missing.length ? `Missing required documents: ${missing.map(m => escapeHtml(m.label)).join(', ')}.` : ''}
          ${rejected.length ? `Replace rejected documents: ${rejected.map(d => escapeHtml(DOC_TYPE_LABELS[d.document_type] || d.document_type)).join(', ')}.` : ''}
        </div>`;
      submitBtn.disabled = true;
    } else {
      missingArea.innerHTML = '';
      submitBtn.disabled = applicationStatus === 'pending';
    }
  }

  function applyApplicationState(profile){
    applicationStatus = profile?.status || 'draft';
    const note = document.getElementById('stateNote');
    const subtitle = document.getElementById('pageSubtitle');
    if(applicationStatus === 'approved') return window.location.href = '/vendor/dashboard';
    if(applicationStatus === 'pending'){
      pageArea.classList.add('view-only');
      note.className = 'state-note pending';
      note.textContent = 'Your application is under review. This is a read-only copy; editing and duplicate submission are disabled until the review is complete.';
      subtitle.textContent = 'Review the information currently being verified by the MEETA team.';
      submitBtn.style.display = 'none';
      backBtn.href = '/vendor/pending';
      backBtn.textContent = '← Back to Application Status';
    } else if(applicationStatus === 'rejected'){
      note.className = 'state-note rejected';
      note.textContent = profile.rejection_reason ? `Changes requested: ${profile.rejection_reason}` : 'Changes were requested. Correct the highlighted application details and reapply.';
      submitBtn.textContent = 'Reapply for Verification';
      backBtn.href = '/vendor/rejected';
      backBtn.textContent = '← Back to Correction Centre';
    }
  }

  async function loadAll(){
    try{
      const [profileRes, addressRes, bankRes, documentsRes] = await Promise.all([
        fetchJson('/api/vendor/profile'),
        fetchJson('/api/vendor/address'),
        fetchJson('/api/vendor/bank-account'),
        fetchJson('/api/vendor/documents'),
      ]);

      if(!profileRes || !addressRes || !bankRes || !documentsRes) return; // redirect already happened

      const profile = profileRes.data || null;
      const addressList = Array.isArray(addressRes.data) ? addressRes.data : (addressRes.data ? [addressRes.data] : []);
      const address = addressList.length ? addressList[0] : null;
      const bank = bankRes.data || null;
      const documentsList = documentsRes.data || [];

      applyApplicationState(profile);

      renderProfile(profile);
      renderAddress(address);
      renderBank(bank);
      renderDocuments(documentsList);
      renderMissingWarning(documentsList);

      initialLoader.style.display = 'none';
      pageArea.style.display = 'block';

    } catch(err){
      showToast('Network error while loading your details.', 'error');
    }
  }

  submitBtn.addEventListener('click', async () => {
    if(applicationStatus === 'pending' || applicationStatus === 'approved') return;
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting…';

    try{
      const res = await fetch('/api/vendor/submit', {
        method: 'POST',
        headers: {
          ...authHeaders,
          'Content-Type': 'application/json'
        }
      });

      const json = await res.json();

      if(res.status === 401){
        localStorage.removeItem('meeta_token');
        window.location.href = '/login';
        return;
      }

      if(!res.ok || !json.success){
        showToast(json.message || 'Submission failed. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
      }

      showToast(json.message || 'Submitted successfully.', 'success');
      window.location.href = '/vendor/pending';

    } catch(err){
      showToast('Network error. Please try again.', 'error');
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });

  loadAll();

  document.getElementById('signOutBtn').addEventListener('click', () => {
    localStorage.removeItem('meeta_token');
    localStorage.removeItem('meeta_user');
    localStorage.removeItem('meeta_vendor');
    window.location.href = '/login';
  });
})();
</script>

</body>
</html>

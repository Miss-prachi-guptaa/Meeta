<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Documents - MEETA Vendor</title>
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

  .doc-card{
    background:#fff; border:1px solid var(--border); border-radius:12px;
    padding:26px 28px; box-shadow:0 4px 18px rgba(43,29,14,0.05);
    margin-bottom:20px;
  }
  .doc-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px; }
  .doc-title{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .doc-title h3{ margin:0; font-size:16px; }
  .req-tag{
    font-size:10px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    padding:3px 9px; border-radius:12px;
  }
  .req-tag.required{ background:#FBEAE5; color:var(--danger); }
  .req-tag.optional{ background:var(--cream); color:var(--muted); border:1px solid var(--border); }

  .badge{
    font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    padding:6px 12px; border-radius:20px; white-space:nowrap;
  }
  .badge.not-uploaded{ background:var(--cream); color:var(--muted); border:1px solid var(--border); }
  .badge.pending{ background:var(--warn-bg); color:var(--warn-text); }
  .badge.verified{ background:#E6F4EA; color:var(--success); }
  .badge.rejected{ background:#FBEAE5; color:var(--danger); }

  .rejection-note{
    background:#FBEAE5; border:1px solid #F0C9C2; color:var(--danger);
    border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:18px;
  }
  .rejection-note strong{ display:block; margin-bottom:2px; font-size:11px; letter-spacing:.5px; text-transform:uppercase; }

  .pending-note{
    background:var(--cream); border:1px solid var(--border); color:var(--muted);
    border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:4px;
  }

  .field{ margin-bottom:16px; }
  .field label{
    display:block; font-size:12px; letter-spacing:1px;
    color:var(--muted); text-transform:uppercase; margin-bottom:6px;
  }
  .field input[type=text]{
    width:100%; padding:12px 14px; border:1px solid var(--border);
    border-radius:8px; font-size:14px; background:var(--cream); color:var(--text);
  }
  .field input[type=text]:disabled{ opacity:.6; cursor:not-allowed; }
  .error-text{ color:var(--danger); font-size:12px; margin-top:5px; display:none; }
  .field.has-error input{ border-color:var(--danger); }
  .field.has-error .error-text{ display:block; }

  .file-row{
    display:flex; align-items:center; gap:12px; flex-wrap:wrap;
    border:1px dashed var(--border); border-radius:8px; padding:14px 16px;
    background:var(--cream); margin-bottom:6px;
  }
  .file-row input[type=file]{ font-size:13px; flex:1; min-width:180px; }
  .file-name-existing{ font-size:13px; color:var(--muted); }

  .doc-actions{ display:flex; justify-content:flex-end; margin-top:14px; }
  .btn{ padding:11px 22px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; }
  .btn:disabled{ opacity:.55; cursor:not-allowed; }
  .btn-outline{ background:#fff; border:1px solid var(--terracotta); color:var(--terracotta); }
  .btn-primary{ background:var(--terracotta); color:#fff; }
  .btn:hover:not(:disabled){ opacity:.9; }

  .bottom-actions{ display:flex; justify-content:space-between; align-items:center; margin-top:32px; gap:16px; flex-wrap:wrap; }
  .bottom-actions .btn{ padding:13px 26px; font-size:14px; }

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
    <div class="step done"><div class="dot">✓</div>Bank</div>
    <div class="step active"><div class="dot">4</div>Documents</div>
    <div class="step"><div class="dot">5</div>Review</div>
  </div>

  <div id="initialLoader" class="loading-overlay">Loading your documents…</div>

  <div id="pageArea" style="display:none;">
    <div class="page-head">
      <h1>Upload your documents</h1>
      <div class="subtitle">Required documents must be uploaded to continue. Rejected documents can be re-uploaded.</div>
    </div>

    <div id="cardsContainer"></div>

    <div class="bottom-actions">
      <a href="/vendor/bank-account" class="btn btn-outline">Back</a>
      <button type="button" class="btn btn-primary" id="continueBtn">Save &amp; Continue →</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
  const token = localStorage.getItem('meeta_token');
  const API_BASE = '/api/vendor/documents';

  if(!token){
    window.location.href = '/login';
    return;
  }

  // NOTE: document_type slugs below are assumed per business requirement
  // ('aadhaar' and 'pan' are confirmed from the real API response;
  // 'gst', 'shop_license', 'cancelled_cheque' are placeholders — confirm
  // the exact slugs your backend expects before going live).
  const DOC_TYPES = [
    { key: 'aadhaar',           label: 'Aadhaar Card',                     required: true  },
    { key: 'pan',                label: 'PAN Card',                        required: true  },
    { key: 'gst',                label: 'GST Certificate',                 required: false },
    { key: 'shop_license',       label: 'Shop / Trade License',            required: false },
    { key: 'cancelled_cheque',   label: 'Cancelled Cheque / Bank Passbook', required: true  },
  ];

  const initialLoader   = document.getElementById('initialLoader');
  const pageArea        = document.getElementById('pageArea');
  const cardsContainer  = document.getElementById('cardsContainer');
  const continueBtn     = document.getElementById('continueBtn');
  const toast           = document.getElementById('toast');

  // in-memory map: document_type -> document record from API
  let docsByType = {};
  let applicationStatus = 'draft';

  function showToast(message, type){
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
  }

  function statusBadgeHtml(status){
    if(!status) return '<span class="badge not-uploaded">Not Uploaded</span>';
    const map = {
      pending:  ['pending', 'Pending Review'],
      verified: ['verified', 'Verified'],
      approved: ['verified', 'Approved'],
      rejected: ['rejected', 'Rejected']
    };
    const [cls, label] = map[status] || ['not-uploaded', status];
    return `<span class="badge ${cls}">${label}</span>`;
  }

  function renderCard(type){
    const doc = docsByType[type.key];
    const status = doc ? doc.status : null;

    // uploading/replacing allowed when: no doc yet, or doc was rejected
    const canEdit = applicationStatus === 'draft' || !doc || status === 'rejected';

    const reqTag = type.required
      ? '<span class="req-tag required">Required</span>'
      : '<span class="req-tag optional">Optional</span>';

    let rejectionHtml = '';
    if(status === 'rejected' && doc.remarks){
      rejectionHtml = `
        <div class="rejection-note">
          <strong>Rejection Reason</strong>
          ${escapeHtml(doc.remarks)}
        </div>`;
    }

    let pendingHtml = '';
    if(status === 'pending' && applicationStatus !== 'draft'){
      pendingHtml = `<div class="pending-note">This document is awaiting admin review and can't be replaced right now.</div>`;
    }

    let verifiedHtml = '';
    if(status === 'verified'){
      verifiedHtml = `<div class="pending-note">This document has been verified and is locked.</div>`;
    }

    const docNumberValue = doc ? escapeAttr(doc.document_number || '') : '';
    const existingFileName = doc ? doc.file_name : '';

    return `
      <div class="doc-card" data-type="${type.key}">
        <div class="doc-head">
          <div class="doc-title">
            <h3>${type.label}</h3>
            ${reqTag}
          </div>
          ${statusBadgeHtml(status)}
        </div>

        ${rejectionHtml}
        ${pendingHtml}
        ${verifiedHtml}

        <div class="field" data-field="document_number">
          <label>Document Number</label>
          <input type="text" class="doc-number-input" value="${docNumberValue}" ${canEdit ? '' : 'disabled'} placeholder="Enter document number">
          <div class="error-text"></div>
        </div>

        <div class="field" data-field="document">
          <div class="file-row">
            <input type="file" class="doc-file-input" ${canEdit ? '' : 'disabled'} accept=".pdf,.jpg,.jpeg,.png">
            ${existingFileName ? `<span class="file-name-existing">Current: ${escapeHtml(existingFileName)}</span>` : ''}
          </div>
          <div class="error-text"></div>
        </div>

        ${canEdit ? `
          <div class="doc-actions">
            <button type="button" class="btn btn-primary doc-upload-btn">
              ${doc ? 'Replace Document' : 'Upload Document'}
            </button>
          </div>
        ` : ''}
      </div>
    `;
  }

  function escapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }
  function escapeAttr(str){
    return String(str).replace(/"/g, '&quot;');
  }

  function renderAllCards(){
    cardsContainer.innerHTML = DOC_TYPES.map(renderCard).join('');
    attachCardEvents();
  }

  function clearCardError(cardEl){
    cardEl.querySelectorAll('.field').forEach(f => {
      f.classList.remove('has-error');
      const err = f.querySelector('.error-text');
      if(err) err.textContent = '';
    });
  }

  function showCardFieldError(cardEl, fieldName, message){
    const fieldEl = cardEl.querySelector(`.field[data-field="${fieldName}"]`);
    if(fieldEl){
      fieldEl.classList.add('has-error');
      fieldEl.querySelector('.error-text').textContent = message;
    }
  }

  function attachCardEvents(){
    cardsContainer.querySelectorAll('.doc-card').forEach(cardEl => {
      const uploadBtn = cardEl.querySelector('.doc-upload-btn');
      if(!uploadBtn) return;

      uploadBtn.addEventListener('click', async () => {
        const typeKey = cardEl.getAttribute('data-type');
        const doc = docsByType[typeKey];
        const numberInput = cardEl.querySelector('.doc-number-input');
        const fileInput = cardEl.querySelector('.doc-file-input');

        clearCardError(cardEl);

        const documentNumber = numberInput.value.trim();
        const file = fileInput.files[0];

        if(!documentNumber){
          showCardFieldError(cardEl, 'document_number', 'Document number is required.');
          return;
        }
        if(!file){
          showCardFieldError(cardEl, 'document', 'Please choose a file to upload.');
          return;
        }

        const formData = new FormData();
        formData.append('document_type', typeKey);
        formData.append('document_number', documentNumber);
        formData.append('document', file);

        uploadBtn.disabled = true;
        const originalText = uploadBtn.textContent;
        uploadBtn.textContent = 'Uploading…';

        try{
          const isReplace = !!doc; // doc only present here when status === 'rejected'
          const url = isReplace ? `${API_BASE}/${doc.id}` : API_BASE;
          const method = 'POST';
          if(isReplace) formData.append('_method', 'PUT');

          const res = await fetch(url, {
            method,
            headers: {
              'Authorization': 'Bearer ' + token,
              'Accept': 'application/json'
              // Content-Type intentionally omitted — browser sets multipart boundary
            },
            body: formData
          });

          const json = await res.json();

          if(res.status === 401){
            localStorage.removeItem('meeta_token');
            window.location.href = '/login';
            return;
          }

          if(res.status === 422 && json.errors){
            Object.keys(json.errors).forEach(field => {
              const mappedField = field === 'document' ? 'document' : 'document_number';
              showCardFieldError(cardEl, mappedField, json.errors[field][0]);
            });
            showToast('Please fix the highlighted fields.', 'error');
            return;
          }

          if(!res.ok || !json.success){
            showToast(json.message || 'Upload failed. Please try again.', 'error');
            return;
          }

          docsByType[typeKey] = json.data;
          renderAllCards();
          showToast(isReplace ? 'Document replaced successfully.' : 'Document uploaded successfully.', 'success');

        } catch(err){
          showToast('Network error. Please try again.', 'error');
        } finally{
          uploadBtn.disabled = false;
          uploadBtn.textContent = originalText;
        }
      });
    });
  }

  async function loadDocuments(){
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
        showToast('Could not load your documents. Please refresh.', 'error');
        return;
      }

      const list = json.data || [];
      docsByType = {};
      list.forEach(doc => { docsByType[doc.document_type] = doc; });

      renderAllCards();
      initialLoader.style.display = 'none';
      pageArea.style.display = 'block';

    } catch(err){
      showToast('Network error while loading documents.', 'error');
    }
  }

  continueBtn.addEventListener('click', () => {
    const missingRequired = DOC_TYPES.filter(t => t.required && !docsByType[t.key]);

    if(missingRequired.length){
      showToast('Please upload: ' + missingRequired.map(t => t.label).join(', '), 'error');
      return;
    }

    window.location.href = new URLSearchParams(window.location.search).get('return') || '/vendor/review';
  });

  async function guardState(){const res=await fetch('/api/vendor/profile',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}});const json=await res.json();const status=json.data?.status;applicationStatus=status||'draft';if(status==='pending'){location.href='/vendor/pending';return false}if(status==='approved'){location.href='/vendor/dashboard';return false}return true}
  guardState().then(ok=>{if(ok)loadDocuments()}).catch(()=>showToast('Could not verify application status.','error'));
})();
</script>

</body>
</html>

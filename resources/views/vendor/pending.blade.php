<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Application Status - MEETA Vendor</title>
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

  .pending-wrap{
    max-width:640px;
    margin:60px auto;
    padding:0 20px 60px;
  }
  .pending-card{
    background:#fff; border:1px solid var(--border); border-radius:14px;
    padding:44px 36px; box-shadow:0 4px 18px rgba(43,29,14,0.06);
    text-align:center;
  }
  .pending-icon{
    width:76px; height:76px; margin:0 auto 22px;
    border-radius:50%; background:var(--warn-bg);
    display:flex; align-items:center; justify-content:center;
  }
  .pending-icon svg{ width:38px; height:38px; }
  .pending-wrap h1{ font-size:22px; margin:0 0 12px; }
  .pending-wrap .lead{ color:var(--muted); font-size:14px; line-height:1.6; margin-bottom:20px; }

  .status-badge{
    display:inline-block; font-size:11px; font-weight:700; letter-spacing:.5px;
    text-transform:uppercase; padding:7px 16px; border-radius:20px;
    background:var(--warn-bg); color:var(--warn-text); margin-bottom:28px;
  }
  .status-badge.rejected{ background:#FBEAE5; color:var(--danger); }
  .status-badge.verified{ background:#E6F4EA; color:var(--success); }

  .store-name{ font-size:18px; font-weight:700; color:var(--dark); margin-bottom:22px; }

  .summary-box{
    text-align:left; background:var(--cream); border:1px solid var(--border);
    border-radius:10px; padding:20px 22px; margin-bottom:22px;
  }
  .summary-box .kv{ display:flex; justify-content:space-between; gap:16px; padding:7px 0; font-size:13px; border-bottom:1px solid var(--border); }
  .summary-box .kv:last-child{ border-bottom:none; }
  .summary-box .kv .k{ color:var(--muted); }
  .summary-box .kv .v{ font-weight:600; text-align:right; word-break:break-word; }

  .note-box{
    background:var(--cream); border:1px solid var(--border); border-radius:10px;
    padding:14px 18px; font-size:13px; color:var(--muted); margin-bottom:30px; text-align:left;
  }

  .pending-actions{ display:flex; justify-content:center; gap:14px; flex-wrap:wrap; }
  .btn{ padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; display:inline-block; transition:opacity .15s; }
  .btn-outline{ background:#fff; border:1px solid var(--terracotta); color:var(--terracotta); }
  .btn-primary{ background:var(--terracotta); color:#fff; }
  .btn:hover{ opacity:.9; }

  .loading-overlay{ text-align:center; padding:60px 0; color:var(--muted); font-size:14px; }
</style>
</head>
<body>

<div class="topbar">
  <span class="logo">MEETA</span>
  <span class="tag">VENDOR ONBOARDING</span>
</div>

<div class="pending-wrap">
  <div id="initialLoader" class="loading-overlay">Loading your application status…</div>

  <div id="pendingArea" class="pending-card" style="display:none;">
    <div class="pending-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="#9A6A12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 16 14"></polyline>
      </svg>
    </div>

    <span class="status-badge" id="statusBadge">Pending Verification</span>

    <h1 id="headingText">Your application is under review</h1>
    <div class="lead" id="leadText">
      Your vendor application has been submitted successfully. Our admin team will review your details and documents.
    </div>

    <div class="store-name" id="storeName"></div>

    <div class="summary-box" id="summaryBox"></div>

    <div class="note-box" id="noteBox">
      Once your application is approved, you'll get access to your vendor dashboard.
    </div>

    <div class="pending-actions">
      <a href="/vendor/review" class="btn btn-outline">View Application</a>
      <button type="button" class="btn btn-primary" onclick="logout()">
    Sign Out
</button>
    </div>
  </div>
</div>

<script>
(function(){
  const token = localStorage.getItem('meeta_token');

  if(!token){
    window.location.href = '/login';
    return;
  }

  const initialLoader = document.getElementById('initialLoader');
  const pendingArea    = document.getElementById('pendingArea');
  const statusBadge    = document.getElementById('statusBadge');
  const headingText    = document.getElementById('headingText');
  const leadText       = document.getElementById('leadText');
  const storeName      = document.getElementById('storeName');
  const summaryBox      = document.getElementById('summaryBox');
  const noteBox         = document.getElementById('noteBox');

  function escapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str == null ? '' : String(str);
    return d.innerHTML;
  }

  function summaryRow(label, value){
    return `
      <div class="kv">
        <div class="k">${label}</div>
        <div class="v">${value ? escapeHtml(value) : '—'}</div>
      </div>`;
  }

  function renderSummary(profile){
    summaryBox.innerHTML = [
      summaryRow('Business Name', profile.business_name),
      summaryRow('GST Number', profile.gst_number),
      summaryRow('PAN Number', profile.pan_number),
      summaryRow('Support Email', profile.support_email),
      summaryRow('Support Phone', profile.support_phone),
    ].join('');
  }

  function renderForStatus(profile){
    const status = profile.status;
    storeName.textContent = profile.store_name || '';
    renderSummary(profile);

    if(status === 'pending'){
      statusBadge.textContent = 'Pending Verification';
      statusBadge.className = 'status-badge';
      headingText.textContent = 'Your application is under review';
      leadText.textContent = 'Your vendor application has been submitted successfully. Our admin team will review your details and documents.';
      noteBox.textContent = "Once your application is approved, you'll get access to your vendor dashboard.";
    } else if(status === 'rejected'){
      statusBadge.textContent = 'Rejected';
      statusBadge.className = 'status-badge rejected';
      headingText.textContent = 'Your application needs changes';
      leadText.textContent = profile.rejection_reason
        ? ('Reason: ' + profile.rejection_reason)
        : 'Our admin team has requested some changes to your application. Please review and resubmit.';
      noteBox.textContent = 'You can edit your details from "View Application" and submit again.';
    } else if(status === 'approved'){
      statusBadge.textContent = 'Approved';
      statusBadge.className = 'status-badge verified';
      headingText.textContent = 'Your application has been approved';
      leadText.textContent = 'Congratulations! Your vendor account has been verified.';
      noteBox.textContent = 'Your vendor dashboard will be available here soon.';
    } else {
      statusBadge.textContent = status || 'Unknown';
      headingText.textContent = 'Application status';
      leadText.textContent = 'We could not determine a specific status for your application.';
      noteBox.textContent = '';
    }

    initialLoader.style.display = 'none';
    pendingArea.style.display = 'block';
  }

  async function loadProfile(){
    try{
      const res = await fetch('/api/vendor/profile', {
        headers: {
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json'
        }
      });

      if(res.status === 401){
        localStorage.removeItem('meeta_token');
        window.location.href = '/login';
        return;
      }

      const json = await res.json();

      if(!res.ok || !json.success || !json.data){
        initialLoader.textContent = 'Could not load your application status. Please refresh.';
        return;
      }

      renderForStatus(json.data);

    } catch(err){
      initialLoader.textContent = 'Network error while loading your application status.';
    }
  }

  loadProfile();
})();

function logout() {
    localStorage.removeItem('meeta_token');
    localStorage.removeItem('meeta_user');
    localStorage.removeItem('meeta_vendor');

    window.location.href = '/login';
}
</script>

</body>
</html>
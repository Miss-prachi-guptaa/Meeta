<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Complete Your Business Profile - MEETA Vendor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --cream: #F7F3EA;
      --dark: #2B1D0E;
      --terracotta: #C1743A;
      --gold: #C9A227;
      --text: #3A2C1A;
      --muted: #9C8F7A;
      --border: #E4DCC8;
      --danger: #B3261E;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', -apple-system, sans-serif;
      background: var(--cream);
      color: var(--text);
    }

    .topbar {
      background: var(--dark);
      color: var(--gold);
      padding: 18px 40px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .topbar .logo {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 1px;
    }

    .topbar .tag {
      font-size: 11px;
      letter-spacing: 2px;
      color: var(--gold);
    }

    .wrapper {
      max-width: 760px;
      margin: 40px auto;
      padding: 0 20px 60px;
    }

    .steps {
      display: flex;
      justify-content: space-between;
      margin-bottom: 36px;
      position: relative;
    }

    .steps::before {
      content: '';
      position: absolute;
      top: 14px;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--border);
      z-index: 0;
    }

    .step {
      position: relative;
      z-index: 1;
      text-align: center;
      flex: 1;
      font-size: 12px;
      color: var(--muted);
    }

    .step .dot {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #fff;
      border: 2px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 6px;
      font-weight: 600;
      font-size: 13px;
      color: var(--muted);
    }

    .step.active .dot {
      background: var(--terracotta);
      border-color: var(--terracotta);
      color: #fff;
    }

    .step.active {
      color: var(--text);
      font-weight: 600;
    }

    .card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 36px;
      box-shadow: 0 4px 18px rgba(43, 29, 14, 0.05);
    }

    h1 {
      font-size: 24px;
      margin: 0 0 6px;
    }

    .subtitle {
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 28px;
    }

    .readonly-block {
      display: flex;
      gap: 20px;
      margin-bottom: 28px;
      background: var(--cream);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 16px 20px;
    }

    .readonly-item {
      flex: 1;
    }

    .readonly-item label {
      font-size: 11px;
      letter-spacing: 1px;
      color: var(--muted);
      text-transform: uppercase;
    }

    .readonly-item div.val {
      font-size: 15px;
      font-weight: 600;
      margin-top: 2px;
    }

    .field {
      margin-bottom: 22px;
    }

    .field label {
      display: block;
      font-size: 12px;
      letter-spacing: 1px;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .field input[type=text],
    .field input[type=email],
    .field input[type=tel],
    .field textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 14px;
      background: var(--cream);
      color: var(--text);
    }

    .field textarea {
      resize: vertical;
      min-height: 100px;
    }

    .char-count {
      font-size: 11px;
      color: var(--muted);
      text-align: right;
      margin-top: 4px;
    }

    .error-text {
      color: var(--danger);
      font-size: 12px;
      margin-top: 5px;
      display: none;
    }

    .field.has-error input,
    .field.has-error textarea {
      border-color: var(--danger);
    }

    .field.has-error .error-text {
      display: block;
    }

    .upload-row {
      display: flex;
      gap: 24px;
      margin-bottom: 28px;
      flex-wrap: wrap;
    }

    .upload-box {
      flex: 1;
      min-width: 220px;
    }

    .upload-box label {
      display: block;
      font-size: 12px;
      letter-spacing: 1px;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .preview {
      width: 100%;
      height: 120px;
      border: 1px dashed var(--border);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--cream);
      overflow: hidden;
      margin-bottom: 8px;
    }

    .preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .preview span {
      color: var(--muted);
      font-size: 12px;
    }

    .upload-box input[type=file] {
      font-size: 12px;
    }

    .upload-box .error-text {
      display: none;
    }

    .upload-box .error-text.show {
      display: block;
    }

    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 32px;
      gap: 16px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 13px 26px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: opacity .15s;
    }

    .btn:disabled {
      opacity: .6;
      cursor: not-allowed;
    }

    .btn-outline {
      background: #fff;
      border: 1px solid var(--terracotta);
      color: var(--terracotta);
    }

    .btn-primary {
      background: var(--terracotta);
      color: #fff;
    }

    .btn:hover:not(:disabled) {
      opacity: .9;
    }

    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 14px 20px;
      border-radius: 8px;
      font-size: 14px;
      color: #fff;
      display: none;
      z-index: 50;
      max-width: 320px;
    }

    .toast.success {
      background: #2E7D32;
    }

    .toast.error {
      background: var(--danger);
    }

    .loading-overlay {
      text-align: center;
      padding: 60px 0;
      color: var(--muted);
      font-size: 14px;
    }
  </style>
</head>

<body>

  <div class="topbar">
    <span class="logo">MEETA</span>
    <span class="tag">VENDOR ONBOARDING</span>
  </div>

  <div class="wrapper">

    <div class="steps">
      <div class="step active">
        <div class="dot">1</div>Profile
      </div>
      <div class="step">
        <div class="dot">2</div>Address
      </div>
      <div class="step">
        <div class="dot">3</div>Bank
      </div>
      <div class="step">
        <div class="dot">4</div>Documents
      </div>
      <div class="step">
        <div class="dot">5</div>Review
      </div>
    </div>

    <div class="card">
      <div id="initialLoader" class="loading-overlay">Loading your profile…</div>

      <div id="formArea" style="display:none;">
        <h1>Complete your business profile</h1>
        <div class="subtitle">Tell buyers who you are. You can edit this later.</div>

        <div class="readonly-block">
          <div class="readonly-item field" id="store_nameField">
            <label>Store Name</label>
            <input class="val" id="storeNameVal" maxlength="255" required>
            <div class="error-text"></div>
          </div>
          <div class="readonly-item field" id="business_nameField">
            <label>Business Name</label>
            <input class="val" id="businessNameVal" maxlength="255" required>
            <div class="error-text"></div>
          </div>
        </div>

        <form id="profileForm" novalidate>
          <div class="upload-row">
            <div class="upload-box">
              <label>Logo</label>
              <div class="preview" id="logoPreviewBox"><span>No logo uploaded</span></div>
              <input type="file" id="logoInput" accept="image/*">
              <div class="error-text" id="logoError"></div>
            </div>
            <div class="upload-box">
              <label>Banner</label>
              <div class="preview" id="bannerPreviewBox"><span>No banner uploaded</span></div>
              <input type="file" id="bannerInput" accept="image/*">
              <div class="error-text" id="bannerError"></div>
            </div>
          </div>

          <div class="field" id="descriptionField">
            <label>Description</label>
            <textarea id="description" maxlength="500" placeholder="Tell buyers about your store…"></textarea>
            <div class="char-count"><span id="descCount">0</span>/500</div>
            <div class="error-text"></div>
          </div>

          <div class="field" id="support_emailField">
            <label>Support Email</label>
            <input type="email" id="support_email" placeholder="support@yourstore.com">
            <div class="error-text"></div>
          </div>

          <div class="field" id="support_phoneField">
            <label>Support Phone</label>
            <input type="tel" id="support_phone" placeholder="+91 00000 00000">
            <div class="error-text"></div>
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
    (function() {
      const token = localStorage.getItem('meeta_token');
      const API_BASE = '/api/vendor/profile';

      if (!token) {
        window.location.href = '/login';
        return;
      }

      const initialLoader = document.getElementById('initialLoader');
      const formArea = document.getElementById('formArea');
      const toast = document.getElementById('toast');
      const logoInput = document.getElementById('logoInput');
      const bannerInput = document.getElementById('bannerInput');
      const logoPreviewBox = document.getElementById('logoPreviewBox');
      const bannerPreviewBox = document.getElementById('bannerPreviewBox');
      const descriptionEl = document.getElementById('description');
      const descCount = document.getElementById('descCount');
      const emailEl = document.getElementById('support_email');
      const phoneEl = document.getElementById('support_phone');
      const saveDraftBtn = document.getElementById('saveDraftBtn');
      const saveContinueBtn = document.getElementById('saveContinueBtn');

      let selectedLogoFile = null;
      let selectedBannerFile = null;

      function showToast(message, type) {
        toast.textContent = message;
        toast.className = 'toast ' + type;
        toast.style.display = 'block';
        setTimeout(() => {
          toast.style.display = 'none';
        }, 3500);
      }

      function clearErrors() {
        document.querySelectorAll('.field').forEach(el => {
          el.classList.remove('has-error');
          const err = el.querySelector('.error-text');
          if (err) err.textContent = '';
        });
        document.querySelectorAll('.upload-box .error-text').forEach(el => {
          el.textContent = '';
          el.classList.remove('show');
        });
      }

      function showFieldError(name, message) {
        if (name === 'logo' || name === 'banner') {
          const el = document.getElementById(name + 'Error');
          el.textContent = message;
          el.classList.add('show');
          return;
        }
        const fieldEl = document.getElementById(name + 'Field');
        if (fieldEl) {
          fieldEl.classList.add('has-error');
          fieldEl.querySelector('.error-text').textContent = message;
        }
      }

      function renderPreview(box, url) {
        box.innerHTML = url ? '<img src="' + url + '" alt="preview">' : '<span>No image uploaded</span>';
      }

      function statusRedirect(status) {
        const correctionMode = status === 'rejected' && Boolean(new URLSearchParams(window.location.search).get('return'));
        if (status === 'rejected' && correctionMode) return false;
        const map = {
          approved: '/vendor/dashboard',
          pending: '/vendor/pending',
          rejected: '/vendor/rejected',
          suspended: '/vendor/suspended'
        };
        if (map[status]) {
          window.location.href = map[status];
          return true;
        }
        return false;
      }

      async function loadProfile() {
        try {
          const res = await fetch(API_BASE, {
            headers: {
              'Authorization': 'Bearer ' + token,
              'Accept': 'application/json'
            }
          });

          if (res.status === 401) {
            localStorage.removeItem('meeta_token');
            window.location.href = '/login';
            return;
          }

          const json = await res.json();

          if (!res.ok || !json.success) {
            showToast('Could not load your profile. Please refresh.', 'error');
            return;
          }

          const data = json.data;
          if (statusRedirect(data.status)) return;

          document.getElementById('storeNameVal').value = data.store_name || '';
          document.getElementById('businessNameVal').value = data.business_name || '';
          descriptionEl.value = data.description || '';
          descCount.textContent = descriptionEl.value.length;
          emailEl.value = data.support_email || '';
          phoneEl.value = data.support_phone || '';
          renderPreview(logoPreviewBox, data.logo);
          renderPreview(bannerPreviewBox, data.banner);

          initialLoader.style.display = 'none';
          formArea.style.display = 'block';
        } catch (err) {
          showToast('Network error while loading profile.', 'error');
        }
      }

      descriptionEl.addEventListener('input', () => {
        descCount.textContent = descriptionEl.value.length;
      });

      logoInput.addEventListener('change', (e) => {
        selectedLogoFile = e.target.files[0] || null;
        if (selectedLogoFile) renderPreview(logoPreviewBox, URL.createObjectURL(selectedLogoFile));
      });

      bannerInput.addEventListener('change', (e) => {
        selectedBannerFile = e.target.files[0] || null;
        if (selectedBannerFile) renderPreview(bannerPreviewBox, URL.createObjectURL(selectedBannerFile));
      });

      async function saveProfile(redirectAfter) {
        clearErrors();

        const btn = redirectAfter ? saveContinueBtn : saveDraftBtn;
        const otherBtn = redirectAfter ? saveDraftBtn : saveContinueBtn;
        btn.disabled = true;
        otherBtn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Saving…';

        const formData = new FormData();
        formData.append('_method', 'PUT'); // method-spoof: multipart can't be sent as a real PUT
        formData.append('store_name', document.getElementById('storeNameVal').value.trim());
        formData.append('business_name', document.getElementById('businessNameVal').value.trim());
        formData.append('description', descriptionEl.value);
        formData.append('support_email', emailEl.value);
        formData.append('support_phone', phoneEl.value);
        if (selectedLogoFile) formData.append('logo', selectedLogoFile);
        if (selectedBannerFile) formData.append('banner', selectedBannerFile);

        try {
          const res = await fetch(API_BASE, {
            method: 'POST',
            headers: {
              'Authorization': 'Bearer ' + token,
              'Accept': 'application/json'
            },
            body: formData
          });

          const json = await res.json();

          if (res.status === 401) {
            localStorage.removeItem('meeta_token');
            window.location.href = '/login';
            return;
          }

          if (res.status === 422 && json.errors) {
            Object.keys(json.errors).forEach(field => {
              showFieldError(field, json.errors[field][0]);
            });
            showToast('Please fix the highlighted fields.', 'error');
            return;
          }

          if (!res.ok || !json.success) {
            showToast(json.message || 'Something went wrong. Please try again.', 'error');
            return;
          }

          showToast('Profile saved successfully.', 'success');
          selectedLogoFile = null;
          selectedBannerFile = null;

          if (redirectAfter) {
            window.location.href = new URLSearchParams(window.location.search).get('return') || '/vendor/address';
          }
        } catch (err) {
          showToast('Network error. Please check your connection.', 'error');
        } finally {
          btn.disabled = false;
          otherBtn.disabled = false;
          btn.textContent = originalText;
        }
      }

      saveDraftBtn.addEventListener('click', () => saveProfile(false));
      saveContinueBtn.addEventListener('click', () => saveProfile(true));

      loadProfile();
    })();
  </script>

</body>

</html>

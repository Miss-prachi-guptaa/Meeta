@extends('layouts.auth')

@section('page-title', 'Become a Vendor — MEETA')
@section('brand-sub', 'Sell on MEETA')

@section('hero')
<h1>Open your stall.<br>Reach every buyer on MEETA.</h1>
<p>Register your business, complete a short verification, and start listing your products.</p>
@endsection

@section('stats')
<div><strong>320+</strong>Active Vendors</div>
<div><strong>0%</strong>Setup Fee</div>
<div><strong>24–48h</strong>Review Time</div>
@endsection

@section('content')

<div id="alert-box" style="display:none;" class="vendor-note"></div>

<h2 class="title">Register as a vendor</h2>
<p class="subtitle">This creates your MEETA account and starts your vendor application.</p>

<form id="vendor-register-form">

  <div class="row2">
    <div class="field">
      <label for="first_name">First name</label>
      <input type="text" id="first_name" name="first_name" placeholder="Rohan" required autofocus>
      <div class="error-text" id="err-first_name"></div>
    </div>
    <div class="field">
      <label for="last_name">Last name</label>
      <input type="text" id="last_name" name="last_name" placeholder="Verma" required>
      <div class="error-text" id="err-last_name"></div>
    </div>
  </div>

  <div class="row2">
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required>
      <div class="error-text" id="err-email"></div>
    </div>
    <div class="field">
      <label for="phone">Phone</label>
      <input type="tel" id="phone" name="phone" placeholder="+91 00000 00000" required>
      <div class="error-text" id="err-phone"></div>
    </div>
  </div>

  <div class="row2">
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
      <div class="error-text" id="err-password"></div>
    </div>
    <div class="field">
      <label for="password_confirmation">Confirm password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
      <div class="error-text" id="err-password_confirmation"></div>
    </div>
  </div>

  <div class="vendor-note">
    This creates your account and starts your vendor application. Address, bank details, and documents are collected in the next steps.
  </div>

  <div class="field">
    <label for="store_name">Store name</label>
    <input type="text" id="store_name" name="store_name" placeholder="e.g. Rahul Super Mart" required>
    <div class="error-text" id="err-store_name"></div>
  </div>

  <div class="field">
    <label for="business_name">Business / Legal entity name</label>
    <input type="text" id="business_name" name="business_name" placeholder="e.g. Rahul Trading Company" required>
    <div class="error-text" id="err-business_name"></div>
  </div>

  <div class="row2">
    <div class="field">
      <label for="gst_number">GST number (optional)</label>
      <input type="text" id="gst_number" name="gst_number" placeholder="22AAAAA0000A1Z5">
      <div class="error-text" id="err-gst_number"></div>
    </div>
    <div class="field">
      <label for="pan_number">PAN number (optional)</label>
      <input type="text" id="pan_number" name="pan_number" placeholder="ABCDE1234F">
      <div class="error-text" id="err-pan_number"></div>
    </div>
  </div>

  <button type="submit" class="submit-btn" id="submit-btn">Create Vendor Account</button>
</form>

<div class="switch-line">
  Already registered as a vendor? <a href="/">Sign in</a>
</div>

<div class="switch-line" style="margin-top:8px;">
  Looking to shop instead? <a href="/register">Create a customer account</a>
</div>

@endsection

@push('scripts')
<script>
  document.getElementById('vendor-register-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    const alertBox = document.getElementById('alert-box');
    alertBox.style.display = 'none';

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Creating account...';

    const payload = {
      first_name: document.getElementById('first_name').value,
      last_name: document.getElementById('last_name').value,
      email: document.getElementById('email').value,
      phone: document.getElementById('phone').value,
      password: document.getElementById('password').value,
      password_confirmation: document.getElementById('password_confirmation').value,
      store_name: document.getElementById('store_name').value,
      business_name: document.getElementById('business_name').value,
      gst_number: document.getElementById('gst_number').value,
      pan_number: document.getElementById('pan_number').value,
    };

    try {
      const res = await fetch('/api/vendor/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();

      if (!res.ok) {
        if (data.errors) {
          Object.entries(data.errors).forEach(([field, messages]) => {
            const el = document.getElementById('err-' + field);
            if (el) el.textContent = messages[0];
          });
        } else {
          alertBox.textContent = data.message || 'Something went wrong. Please try again.';
          alertBox.style.display = 'block';
        }
        return;
      }

      // Confirmed shape: { success, message, data: { token, user, vendor } }
      const token = data.data.token;
      const vendorStatus = data.data.vendor.status; // 'draft' right after registration

      localStorage.setItem('meeta_token', token);
      localStorage.setItem('meeta_user', JSON.stringify(data.data.user));
      localStorage.setItem('meeta_vendor', JSON.stringify(data.data.vendor));

      // Fresh registration is always 'draft' — send to onboarding step 1.
      // Kept as a switch so re-registration edge cases still route correctly.
      switch (vendorStatus) {
        case 'pending':
          window.location.href = '/vendor/pending';
          break;
        case 'approved':
          window.location.href = '/vendor/dashboard';
          break;
        default: // 'draft'
          window.location.href = '/vendor/profile';
      }

    } catch (err) {
      alertBox.textContent = 'Something went wrong. Please try again.';
      alertBox.style.display = 'block';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Create Vendor Account';
    }
  });
</script>
@endpush
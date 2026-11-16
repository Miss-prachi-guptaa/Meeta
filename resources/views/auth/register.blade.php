@extends('layouts.auth')

@section('page-title', 'Create Account — MEETA')

@section('hero')
  <h1>One marketplace.<br>Everything you need.</h1>
  <p>Create your account to start shopping across every store on MEETA — one cart, one checkout.</p>
@endsection

@section('stats')
  <div><strong>320+</strong>Registered Vendors</div>
  <div><strong>1000+</strong>Products Listed</div>
  <div><strong>24/7</strong>Order Support</div>
@endsection

@section('content')

  <div id="alert-box" style="display:none;" class="vendor-note"></div>

  <h2 class="title">Create your account</h2>
  <p class="subtitle">It only takes a minute.</p>

  <form id="register-form">

    <div class="row2">
      <div class="field">
        <label for="first_name">First name</label>
        <input type="text" id="first_name" name="first_name" placeholder="Meera" required autofocus>
        <div class="error-text" id="err-first_name"></div>
      </div>
      <div class="field">
        <label for="last_name">Last name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Sharma" required>
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

    <button type="submit" class="submit-btn" id="submit-btn">Create Account</button>
  </form>

  <div class="switch-line">
    Already have an account? <a id="login-link" href="/login">Sign in</a>
  </div>

  <div class="switch-line" style="margin-top:8px;">
    Want to sell instead? <a href="/vendor/register">Become a vendor</a>
  </div>

@endsection

@push('scripts')
<script>
const customerRedirect = new URLSearchParams(window.location.search).get('redirect');
if (customerRedirect && customerRedirect.startsWith('/') && !customerRedirect.startsWith('//')) {
  document.getElementById('login-link').setAttribute('href', '/login?redirect=' + encodeURIComponent(customerRedirect));
}
document.getElementById('register-form').addEventListener('submit', async function (e) {
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
  };

  try {
    const res = await fetch('/api/register', {
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

    // Expected shape: { token: '...', user: {...} }
    localStorage.setItem('meeta_token', data.token);
    localStorage.setItem('meeta_user', JSON.stringify(data.user));

    window.location.href = customerRedirect && customerRedirect.startsWith('/') && !customerRedirect.startsWith('//') ? customerRedirect : '/home';

  } catch (err) {
    alertBox.textContent = 'Something went wrong. Please try again.';
    alertBox.style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Create Account';
  }
});
</script>
@endpush

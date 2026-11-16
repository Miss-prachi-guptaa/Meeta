@extends('layouts.auth')

@section('page-title', 'Sign In — MEETA')

@section('hero')
  <h1>Welcome back<br>to your marketplace.</h1>
  <p>Sign in to track orders, manage your wishlist, or continue your vendor onboarding.</p>
@endsection

@section('stats')
  <div><strong>320+</strong>Registered Vendors</div>
  <div><strong>1000+</strong>Products Listed</div>
  <div><strong>24/7</strong>Order Support</div>
@endsection

@section('content')

  <div id="alert-box" style="display:none;" class="vendor-note"></div>

  <h2 class="title">{{ request()->is('admin/*') ? 'Admin sign in' : (request()->is('vendor/*') ? 'Vendor sign in' : 'Welcome back') }}</h2>
  <p class="subtitle">{{ request()->is('admin/*') ? 'Access the secure marketplace back office.' : (request()->is('vendor/*') ? 'Manage your artisan storefront and orders.' : 'Sign in to continue shopping, manage your wishlist and track orders.') }}</p>

  <form id="login-form">

    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required autofocus>
      <div class="error-text" id="err-email"></div>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
      <div class="error-text" id="err-password"></div>
    </div>

    <a class="forgot" href="/forgot-password">Forgot password?</a>

    <button type="submit" class="submit-btn" id="submit-btn">Sign In</button>
  </form>

  @unless(request()->is('admin/*'))
  <div class="switch-line">
    New to MEETA? <a id="register-link" href="/register">Create an account</a>
  </div>

  @unless(request()->is('vendor/*'))
  <div class="switch-line" style="margin-top:8px;">
    Want to sell instead? <a href="/vendor/register">Become a vendor</a>
  </div>
  @endunless
  @endunless

@endsection

@push('scripts')
<script>
const customerRedirect = new URLSearchParams(window.location.search).get('redirect');
if (customerRedirect && customerRedirect.startsWith('/') && !customerRedirect.startsWith('//')) {
  document.getElementById('register-link')?.setAttribute('href', '/register?redirect=' + encodeURIComponent(customerRedirect));
}
document.getElementById('login-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
  const alertBox = document.getElementById('alert-box');
  alertBox.style.display = 'none';

  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Signing in...';

  const payload = {
    email: document.getElementById('email').value,
    password: document.getElementById('password').value,
  };

  try {
    const res = await fetch('/api/login', {
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
        alertBox.textContent = data.message || 'Invalid email or password.';
        alertBox.style.display = 'block';
      }
      return;
    }

    // Confirmed shape: { success, message, token, user: { ..., role } }
    const token = data.token;
    const role = data.user.role;

    localStorage.setItem('meeta_token', token);
    localStorage.setItem('meeta_user', JSON.stringify(data.user));

    if (role === 'admin') {
      window.location.href = '/admin/dashboard';
      return;
    }

    // Check whether this user has a vendor profile/application
const profileRes = await fetch('/api/vendor/profile', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json',
  },
});

if (profileRes.ok) {
  const profileData = await profileRes.json();
  const status = profileData?.data?.status;

  switch (status) {
    case 'approved':
      window.location.href = '/vendor/dashboard';
      break;

    case 'pending':
      window.location.href = '/vendor/pending';
      break;

    case 'rejected':
      window.location.href = '/vendor/rejected';
      break;

    case 'suspended':
      window.location.href = '/vendor/suspended';
      break;

    case 'draft':
    default:
      window.location.href = '/vendor/profile';
      break;
  }

  return;
}

// No vendor profile → normal customer
const intended = new URLSearchParams(window.location.search).get('redirect');
window.location.href = intended && intended.startsWith('/') && !intended.startsWith('//') ? intended : '/home';

  } catch (err) {
    alertBox.textContent = 'Something went wrong. Please try again.';
    alertBox.style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Sign In';
  }
});
</script>
@endpush

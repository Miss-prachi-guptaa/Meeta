{{--
    Reusable vendor sidebar.
    Included by resources/views/vendor/layouts/app.blade.php — don't include it directly in pages.

    Route names below are guarded with Route::has() so this doesn't break before
    the corresponding pages exist yet. Only 'vendor.dashboard' is assumed to exist
    (per the existing approval/auth flow). Update the route names here once each
    future page (Store Profile, Payouts, Settings) is built.
--}}
@php
$navItems = [
[
'label' => 'Dashboard',
'route' => 'vendor.dashboard',
'icon' => '
<rect x="3" y="3" width="7" height="7" rx="1.5" />
<rect x="14" y="3" width="7" height="7" rx="1.5" />
<rect x="3" y="14" width="7" height="7" rx="1.5" />
<rect x="14" y="14" width="7" height="7" rx="1.5" />',
],
[
'label' => 'Products',
'route' => 'vendor.products.index',
'icon' => '
<path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z" />
<circle cx="7" cy="7" r="1.2" />',
],
[
'label' => 'Orders',
'route' => 'vendor.orders.index',
'icon' => '
<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
<path d="M3 6h18" />
<path d="M16 10a4 4 0 0 1-8 0" />',
],
[
'label' => 'Store Profile',
'route' => 'vendor.store.edit',
'icon' => '
<path d="M3 21V9l9-6 9 6v12" />
<path d="M9 21v-6h6v6" />',
],
[
'label' => 'Payouts',
'route' => 'vendor.payouts.index',
'icon' => '
<rect x="2" y="5" width="20" height="14" rx="2" />
<line x1="2" y1="10" x2="22" y2="10" />',
],
[
'label' => 'Settings',
'route' => 'vendor.settings.index',
'icon' => '
<circle cx="12" cy="12" r="3" />
<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" />',
],
];
@endphp

<aside class="vendor-sidebar" id="vendorSidebar">
  <div class="sidebar-brand">
    <div class="logo">MEE<span>TA</span></div>
    <div class="sub">Vendor Panel</div>
  </div>

  <nav class="sidebar-nav">
    @foreach ($navItems as $item)
    @php
    $exists = Route::has($item['route']);
    $url = $exists ? route($item['route']) : '#';
    $isActive = $exists && request()->routeIs($item['route'] . '*');
    @endphp
    <a href="{{ $url }}"
      class="{{ $isActive ? 'active' : '' }} {{ $exists ? '' : 'disabled' }}"
      @unless($exists) title="Coming soon" @endunless>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        {!! $item['icon'] !!}
      </svg>
      {{ $item['label'] }}
    </a>
    @endforeach
  </nav>

  <div class="sidebar-footer">
    {{-- Existing vendor logout route/session logic is untouched — only wired here.
             Adjust the route name/action below if your actual logout route differs. --}}
    <form method="POST" action="{{ Route::has('vendor.logout') ? route('vendor.logout') : url('/vendor/logout') }}">
      @csrf
      <button type="submit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        Logout
      </button>
    </form>
  </div>
</aside>

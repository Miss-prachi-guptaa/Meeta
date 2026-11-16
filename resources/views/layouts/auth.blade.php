<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('page-title', 'MEETA')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Jost:wght@400;500;600&family=Cormorant+Garamond:ital@0;1&display=swap" rel="stylesheet">
<style>
  :root{
    --ivory:#F4ECD8;
    --card:#FFFCF5;
    --espresso:#2B1D12;
    --terracotta:#B8722E;
    --terracotta-dark:#96591F;
    --madder:#7A2E2E;
    --gold:#C9A227;
    --line: rgba(43,29,18,0.16);
    --muted: rgba(43,29,18,0.55);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    font-family:'Jost', sans-serif;
    background:var(--ivory);
    color:var(--espresso);
    background-image: radial-gradient(circle at 1px 1px, rgba(43,29,18,0.06) 1px, transparent 0);
    background-size: 22px 22px;
  }
  .wrap{ min-height:100vh; display:grid; grid-template-columns: 1.05fr 1fr; }
  @media (max-width: 880px){ .wrap{grid-template-columns:1fr;} .brandside{min-height:220px;} }

  .brandside{
    position:relative;
    background: linear-gradient(160deg, #241608 0%, #3B2413 55%, #58330F 100%);
    color:#F4ECD8;
    padding:56px 52px;
    display:flex; flex-direction:column; justify-content:space-between;
    overflow:hidden;
  }
  .brandside::before{
    content:"";
    position:absolute; inset:0;
    background-image:
      repeating-linear-gradient(45deg, rgba(201,162,39,0.07) 0 2px, transparent 2px 26px),
      repeating-linear-gradient(-45deg, rgba(201,162,39,0.05) 0 2px, transparent 2px 26px);
    pointer-events:none;
  }
  .brand-mark{position:relative; z-index:2;}
  .brand-mark .name{ font-family:'Marcellus', serif; font-size:28px; letter-spacing:1px; }
  .brand-mark .sub{ font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--gold); margin-top:2px; }
  .hero-copy{position:relative; z-index:2; max-width:420px;}
  .hero-copy h1{
    font-family:'Cormorant Garamond', serif; font-style:italic; font-weight:400;
    font-size:38px; line-height:1.15; margin:0 0 18px; color:#F8F2E4;
  }
  .hero-copy p{ font-size:15px; line-height:1.7; color:rgba(244,236,216,0.75); margin:0; }
  .stamp-row{
    position:relative; z-index:2; display:flex; gap:28px; padding-top:28px;
    border-top:1px solid rgba(201,162,39,0.3); font-size:12px; letter-spacing:1px; color:rgba(244,236,216,0.65);
  }
  .stamp-row strong{ display:block; font-family:'Marcellus', serif; font-size:20px; color:var(--gold); letter-spacing:0.5px; }

  .formside{ display:flex; align-items:center; justify-content:center; padding:48px 32px; }
  .card{ width:100%; max-width:420px; }

  h2.title{ font-family:'Marcellus', serif; font-size:26px; margin:0 0 6px; }
  p.subtitle{ font-size:13.5px; color:var(--muted); margin:0 0 28px; line-height:1.5; }

  form{display:flex; flex-direction:column; gap:16px;}
  .field label{
    display:block; font-size:11.5px; letter-spacing:1.5px; text-transform:uppercase;
    color:var(--muted); margin-bottom:7px;
  }
  .field input, .field select{
    width:100%; border:1px solid var(--line); background:var(--card);
    border-radius:8px; padding:12px 14px; font-family:'Jost', sans-serif;
    font-size:14px; color:var(--espresso); outline:none; transition:.2s;
  }
  .field input:focus, .field select:focus{
    border-color:var(--terracotta); box-shadow:0 0 0 3px rgba(184,114,46,0.15);
  }
  .field input.is-invalid{ border-color:var(--madder); }
  .field .error-text{ font-size:11.5px; color:var(--madder); margin-top:5px; }
  .row2{display:grid; grid-template-columns:1fr 1fr; gap:14px;}

  .vendor-note{
    font-size:11.5px; color:var(--madder); background:rgba(122,46,46,0.06);
    border:1px solid rgba(122,46,46,0.18); padding:10px 12px; border-radius:8px; line-height:1.5;
  }
  .alert-success{
    font-size:12.5px; color:#2f5233; background:rgba(63,143,79,0.08);
    border:1px solid rgba(63,143,79,0.25); padding:10px 12px; border-radius:8px;
  }

  .submit-btn{
    margin-top:6px; padding:14px 0; border:none; border-radius:8px;
    background:var(--terracotta); color:#FFF7EA; font-family:'Jost', sans-serif;
    font-size:13.5px; letter-spacing:1.5px; text-transform:uppercase; cursor:pointer; transition:.2s;
  }
  .submit-btn:hover{ background:var(--terracotta-dark); }

  .switch-line{ text-align:center; font-size:13px; color:var(--muted); margin-top:22px; }
  .switch-line a{ color:var(--madder); text-decoration:none; font-weight:500; }
  .forgot{ text-align:right; font-size:12px; color:var(--muted); text-decoration:none; margin-top:-6px; }
</style>
@stack('styles')
</head>
<body>

<div class="wrap">

  <div class="brandside">
    <div class="brand-mark">
      <div class="name">MEETA</div>
      <div class="sub">@yield('brand-sub', 'Multivendor Marketplace')</div>
    </div>

    <div class="hero-copy">
      @yield('hero')
    </div>

    <div class="stamp-row">
      @yield('stats')
    </div>
  </div>

  <div class="formside">
    <div class="card">
      @yield('content')
    </div>
  </div>

</div>

@stack('scripts')
</body>
</html>
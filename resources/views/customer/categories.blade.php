<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craft Categories | MEETA</title>
    <meta name="description" content="Explore handcrafted pottery, woodwork, metalware, textiles and more from Indian artisans.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root{--ink:#241d19;--brown:#914400;--line:#ead7cc;--cream:#fffaf6;--paper:#fdf7f2;--muted:#766961}
        *{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:"DM Sans",sans-serif}a{color:inherit;text-decoration:none}button,input{font:inherit}
        .grain{position:fixed;inset:0;pointer-events:none;opacity:.18;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.82' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.12'/%3E%3C/svg%3E")}
        header{height:82px;border-bottom:1px solid var(--line);background:rgba(255,250,246,.94);position:sticky;top:0;z-index:10}.nav{max-width:1240px;height:100%;margin:auto;padding:0 28px;display:flex;align-items:center;gap:34px}.logo{display:flex;align-items:center;gap:12px;margin-right:auto}.mark{width:28px;height:48px;border-left:2px solid;border-right:2px solid;position:relative}.mark:before,.mark:after{content:"";position:absolute;left:-6px;right:-6px;border-top:2px solid}.mark:before{top:8px}.mark:after{top:16px}.brand{font-size:20px;letter-spacing:.35em}.brand small{display:block;font-size:6px;letter-spacing:.4em;color:var(--muted);margin-top:5px}.links{display:flex;align-items:center;gap:28px;font-size:14px}.links a{padding:30px 0}.links a.active{color:var(--brown);border-bottom:2px solid var(--brown)}.search{width:230px;display:flex;align-items:center;border:1px solid var(--line);border-radius:12px;padding:10px 14px;background:#fff7f2}.search input{border:0;outline:0;background:transparent;width:100%;margin-left:8px}.icons{display:flex;gap:16px;font-size:20px}.menu{display:none;background:none;border:0;font-size:24px}
        main{max-width:1240px;margin:auto;padding:38px 28px 90px}.crumb{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}.crumb span{color:var(--brown);margin-left:10px}.hero{text-align:center;padding:65px 20px 75px;position:relative;overflow:hidden}.hero:before{content:"";position:absolute;width:260px;height:260px;border:35px dotted rgba(145,68,0,.045);border-radius:50%;left:-50px;top:18px}.eyebrow{text-transform:uppercase;letter-spacing:.42em;color:var(--brown);font-size:11px}.hero h1{font-family:"Playfair Display",serif;font-size:54px;font-weight:500;margin:24px 0 14px}.hero p{max-width:720px;margin:auto;color:var(--muted);font-size:17px;line-height:1.65}.ornament{display:flex;align-items:center;gap:12px;justify-content:center;color:var(--brown);margin-top:34px}.ornament:before,.ornament:after{content:"";height:1px;width:58px;background:var(--line)}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:64px 46px}.card{min-width:0}.photo{height:390px;position:relative;overflow:hidden;background:#eadfd7}.photo img{width:100%;height:100%;object-fit:cover;transition:transform .6s ease}.card:hover img{transform:scale(1.035)}.tag{position:absolute;top:18px;right:0;background:var(--brown);color:white;padding:8px 17px;font-size:9px;letter-spacing:.22em;text-transform:uppercase}.titleline{display:flex;align-items:baseline;justify-content:space-between;border-bottom:1px solid var(--line);padding:22px 0 12px}.titleline h2{font-family:"Playfair Display",serif;font-size:28px;font-weight:500;margin:0}.count{font-size:10px;color:#bb9a87;text-transform:uppercase}.desc{color:var(--muted);line-height:1.55;font-size:14px;min-height:44px;margin:15px 0}.discover{color:var(--brown);font-weight:600;font-size:11px;letter-spacing:.12em;text-transform:uppercase}.discover span{display:inline-block;transition:transform .2s}.card:hover .discover span{transform:translateX(6px)}
        .state{text-align:center;padding:80px 20px}.state.hidden{display:none}.spinner{width:34px;height:34px;border:3px solid var(--line);border-top-color:var(--brown);border-radius:50%;margin:auto;animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
        footer{border-top:1px solid var(--line);background:#f1e8e2}.foot{max-width:1240px;margin:auto;padding:48px 28px;display:grid;grid-template-columns:1.3fr repeat(3,1fr);gap:50px}.foot h3{font-size:13px;text-transform:uppercase;letter-spacing:.12em}.foot p,.foot a{font-size:13px;color:var(--muted);line-height:2;display:block}
        @media(max-width:960px){.links{display:none}.menu{display:block}.search{margin-left:auto}.grid{grid-template-columns:repeat(2,1fr)}.hero h1{font-size:44px}.foot{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:620px){header{height:70px}.nav{padding:0 18px}.brand{font-size:16px}.mark{height:40px}.search{display:none}main{padding:24px 18px 60px}.hero{padding:48px 0 55px}.hero h1{font-size:37px}.hero p{font-size:15px}.grid{grid-template-columns:1fr;gap:48px}.photo{height:420px}.foot{grid-template-columns:1fr;gap:24px}}
    </style>
</head>
<body>
<div class="grain"></div>
<header><nav class="nav">
    <a class="logo" href="/"><span class="mark"></span><span class="brand">MEETA<small>HOME DECOR HUB</small></span></a>
    <div class="links"><a href="/">Home</a><a class="active" href="/categories">Categories</a><a href="#">By State</a><a href="#">Artisans</a><a href="#">Collections</a><a href="#">Offers</a></div>
    <label class="search"><span>⌕</span><input id="search" placeholder="Search crafts..." aria-label="Search crafts"></label>
    <div class="icons"><span>♡</span><span>♧</span><span>♙</span></div><button class="menu" aria-label="Open menu">☰</button>
</nav></header>
<main>
    <div class="crumb">Home / <span>Categories</span></div>
    <section class="hero"><div class="eyebrow">The curated directory</div><h1>Explore the Soul of India</h1><p>From the sun-drenched dunes of Rajasthan to the mist-covered hills of the East, discover chapters of human ingenuity told through hands, heritage, and heart.</p><div class="ornament">✦</div></section>
    <div id="loading" class="state"><div class="spinner"></div><p>Curating the collection…</p></div>
    <div id="error" class="state hidden"><p>We couldn't load the live directory. Showing our curated preview.</p></div>
    <section id="categoryGrid" class="grid" aria-live="polite"></section>
</main>
<footer><div class="foot"><div><div class="brand">MEETA<small>HOME DECOR HUB</small></div><p>Preserving the soul of Bharat's heritage through premium, ethically sourced artisanal crafts.</p></div><div><h3>Our world</h3><a href="#">Our Story</a><a href="#">Artisan Directory</a><a href="#">Sustainability</a></div><div><h3>Care & service</h3><a href="#">Shipping & Returns</a><a href="#">Privacy Policy</a><a href="#">Contact Us</a></div><div><h3>Join the circle</h3><p>Subscribe for early access to new collections.</p></div></div></footer>
<script>
const fallback=[
 {name:'Pottery',slug:'pottery',description:'Terracotta and ceramic treasures shaped by ancestral kilns and patient hands.',image:'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=900&q=85',tag:'Earth Born'},
 {name:'Woodwork',slug:'woodwork',description:'Intricate carvings in sheesham and teak, reflecting the architectural soul of India.',image:'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85',tag:'Teak & Tale'},
 {name:'Metalware',slug:'metalware',description:'Masterpieces in brass, copper and bell-metal cast using ancient techniques.',image:'https://images.unsplash.com/photo-1577702312706-e23ff063064f?auto=format&fit=crop&w=900&q=85',tag:'Eternal Flame'},
 {name:'Textiles',slug:'textiles',description:'Handwoven stories, embroidered traditions and naturally dyed fabrics.',image:'https://images.unsplash.com/photo-1606293926075-69a00dbfde81?auto=format&fit=crop&w=900&q=85',tag:'Woven Tales'},
 {name:'Jewellery',slug:'jewellery',description:'Wearable heritage shaped by generations of specialist artisan families.',image:'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=85',tag:'Royal Adorn'},
 {name:'Home Decor',slug:'home-decor',description:'Thoughtful accents that bring warmth, character and culture into living spaces.',image:'https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=900&q=85',tag:'Living Spaces'}
];
const grid=document.getElementById('categoryGrid'),loading=document.getElementById('loading'),error=document.getElementById('error');
function cards(items){grid.innerHTML=items.map((item,i)=>`<article class="card"><a href="/shop${item.id?'?category_id='+item.id:''}"><div class="photo"><img src="${item.image||fallback[i%fallback.length].image}" alt="${item.name} handcrafted collection" loading="lazy" onerror="this.src='${fallback[i%fallback.length].image}'"><span class="tag">${item.tag||fallback[i%fallback.length].tag}</span></div><div class="titleline"><h2>${item.name}</h2><span class="count">${item.products_count||''} ${item.products_count?'pieces':''}</span></div><p class="desc">${item.description||fallback[i%fallback.length].description}</p><div class="discover">Discover collection <span>→</span></div></a></article>`).join('')}
fetch('/api/customer/categories').then(r=>{if(!r.ok)throw Error();return r.json()}).then(r=>{
 const live=r.data||[],slugs=new Set(live.map(i=>i.slug));
 cards([...live,...fallback.filter(i=>!slugs.has(i.slug))].slice(0,9));
}).catch(()=>{error.classList.remove('hidden');cards(fallback)}).finally(()=>loading.classList.add('hidden'));
document.getElementById('search').addEventListener('keydown',e=>{if(e.key==='Enter'&&e.target.value.trim())location.href='/shop?search='+encodeURIComponent(e.target.value.trim())});
</script>
</body></html>

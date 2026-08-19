<?php // index.php — Sua Biblioteca Landing Principal v2 ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Sua Biblioteca — Sua biblioteca de exercícios com GIFs</title>
<meta name="description" content="Escolha seu treino de treino personalizada com GIFs animados. Musculação, Calistenia e Alongamento. A partir de R$7,90.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --red:#e53e1a;
  --red-dim:rgba(229,62,26,.1);
  --red-glow:rgba(229,62,26,.3);
  --dark:#060606;
  --card:#0f0f0f;
  --surf:#161616;
  --surf2:#1c1c1c;
  --border:rgba(255,255,255,.06);
  --border2:rgba(255,255,255,.12);
  --text:#efefef;
  --muted:#5a5a5a;
  --muted2:#888;
}
html{scroll-behavior:smooth}
body{background:var(--dark);color:var(--text);font-family:'Inter',sans-serif;-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* HEADER */
.hdr{position:sticky;top:0;z-index:200;display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;background:rgba(6,6,6,.9);backdrop-filter:blur(16px);border-bottom:1px solid var(--border)}
.logo{font-family:'Barlow Condensed',sans-serif;font-size:24px;font-weight:900;color:#fff;text-decoration:none;letter-spacing:-.3px}
.logo b{color:var(--red)}
.hdr-btn{font-size:13px;font-weight:600;background:var(--red);color:#fff;padding:7px 16px;border-radius:8px;text-decoration:none;transition:background .15s}
.hdr-btn:hover{background:#c93318}

/* HERO */
.hero{position:relative;min-height:92vh;display:flex;flex-direction:column;justify-content:flex-end;padding:0 0 2.5rem;overflow:hidden}
.hero-bg{
  position:absolute;inset:0;
  background:
    radial-gradient(ellipse 100% 60% at 50% 0%, rgba(229,62,26,.15) 0%, transparent 60%),
    radial-gradient(ellipse 60% 40% at 80% 80%, rgba(229,62,26,.06) 0%, transparent 50%),
    var(--dark);
}
.hero-gif-grid{
  position:absolute;top:0;left:0;right:0;height:62%;
  display:grid;grid-template-columns:repeat(3,1fr);gap:2px;
  overflow:hidden;
  mask-image:linear-gradient(to bottom,rgba(0,0,0,.8) 0%,rgba(0,0,0,.3) 70%,transparent 100%);
  -webkit-mask-image:linear-gradient(to bottom,rgba(0,0,0,.8) 0%,rgba(0,0,0,.3) 70%,transparent 100%);
}
.hero-gif-col{display:flex;flex-direction:column;gap:2px}
.hero-gif-col:nth-child(2){margin-top:-30px}
.hero-gif-col:nth-child(3){margin-top:-60px}
.hgif{width:100%;aspect-ratio:1;background:var(--surf);overflow:hidden;border-radius:4px}
.hgif img{width:100%;height:100%;object-fit:cover;opacity:.6;filter:brightness(.7) contrast(1.1)}
.hero-content{position:relative;z-index:2;padding:0 1.25rem}
.hero-tag{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--red);background:var(--red-dim);border:1px solid rgba(229,62,26,.15);padding:4px 10px;border-radius:20px;margin-bottom:1rem}
.hero h1{font-family:'Barlow Condensed',sans-serif;font-size:clamp(2.8rem,11vw,4rem);font-weight:900;line-height:.95;text-transform:uppercase;letter-spacing:-1px;margin-bottom:.75rem}
.hero h1 em{color:var(--red);font-style:italic}
.hero-sub{font-size:.9rem;color:var(--muted2);line-height:1.6;max-width:340px;margin-bottom:1.75rem}
.hero-ctas{display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.5rem}
.btn-hero-main{display:block;width:100%;padding:15px;background:var(--red);color:#fff;font-size:15px;font-weight:700;text-align:center;text-decoration:none;border-radius:10px;border:none;cursor:pointer;box-shadow:0 0 32px var(--red-glow);transition:all .15s}
.btn-hero-main:hover{background:#c93318;box-shadow:0 0 48px var(--red-glow)}
.btn-hero-sec{display:block;width:100%;padding:13px;background:transparent;color:var(--muted2);font-size:13px;font-weight:500;text-align:center;text-decoration:none;border:1px solid var(--border2);border-radius:10px;transition:all .15s}
.btn-hero-sec:hover{border-color:var(--red);color:var(--red)}
.hero-proof{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap}
.proof-item{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px}
.proof-dot{width:4px;height:4px;border-radius:50%;background:var(--muted)}

/* TRUST BAR */
.trust{display:flex;justify-content:space-around;padding:1.25rem;background:var(--card);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.trust-item{text-align:center}
.trust-num{font-family:'Barlow Condensed',sans-serif;font-size:1.6rem;font-weight:900;color:#fff;line-height:1}
.trust-label{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-top:2px}

/* PRODUTO CARDS */
.produtos{padding:2rem 1.25rem}
.sec-eyebrow{font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:1.25rem}
.sec-title{font-family:'Barlow Condensed',sans-serif;font-size:1.8rem;font-weight:900;text-transform:uppercase;margin-bottom:1.25rem;line-height:1.1}

/* Card principal musculação */
.card-hero{
  background:var(--card);
  border:1px solid rgba(229,62,26,.25);
  border-radius:16px;
  overflow:hidden;
  margin-bottom:10px;
  position:relative;
}
.card-hero-accent{height:3px;background:linear-gradient(90deg,var(--red),#ff6b3d)}
.card-hero-body{padding:1.25rem}
.card-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.875rem}
.card-badge{display:inline-block;font-size:9px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;padding:3px 9px;border-radius:5px}
.badge-hot{background:var(--red-dim);color:var(--red);border:1px solid rgba(229,62,26,.2)}
.badge-new{background:rgba(217,119,6,.1);color:#d97706;border:1px solid rgba(217,119,6,.2)}
.badge-calm{background:rgba(22,163,74,.1);color:#16a34a;border:1px solid rgba(22,163,74,.2)}
.badge-pro{background:rgba(37,99,235,.1);color:#4a8ef0;border:1px solid rgba(37,99,235,.2)}
.card-price-wrap{text-align:right}
.card-price{font-family:'Barlow Condensed',sans-serif;font-size:2rem;font-weight:900;line-height:1}
.price-c{color:var(--red)}
.price-o{color:#d97706}
.price-g{color:#16a34a}
.price-b{color:#4a8ef0}
.card-price-sub{font-size:10px;color:var(--muted);margin-top:2px}
.card-name{font-family:'Barlow Condensed',sans-serif;font-size:1.6rem;font-weight:900;text-transform:uppercase;margin-bottom:.25rem}
.card-desc{font-size:.82rem;color:var(--muted2);line-height:1.6;margin-bottom:1rem}
.card-gif-strip{display:flex;gap:4px;margin-bottom:1rem;overflow:hidden;border-radius:8px}
.cgif{flex:1;aspect-ratio:1;background:var(--surf);border-radius:6px;overflow:hidden}
.cgif img{width:100%;height:100%;object-fit:cover}
.card-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:1.125rem}
.ctag{font-size:10px;background:var(--surf);border:1px solid var(--border);padding:3px 8px;border-radius:5px;color:rgba(255,255,255,.5)}
.btn-card{display:block;width:100%;padding:13px;border-radius:9px;font-size:14px;font-weight:700;text-align:center;text-decoration:none;border:none;cursor:pointer;transition:all .15s}
.btn-c{background:var(--red);color:#fff}
.btn-c:hover{background:#c93318}
.btn-outline{background:transparent;color:var(--muted2);border:1px solid var(--border2)}
.btn-outline:hover{border-color:var(--red);color:var(--red)}
.btn-b{background:#1d4ed8;color:#fff}
.btn-b:hover{background:#1e40af}

/* Grid 2 col */
.card-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px}
.card-sm{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1rem;display:flex;flex-direction:column}
.card-sm .card-name{font-size:1.2rem}
.card-sm .card-price{font-size:1.6rem}
.card-sm .card-desc{font-size:.75rem;margin-bottom:.75rem;flex:1}
.card-sm .card-tags{margin-bottom:.875rem}
.card-sm .card-gif-strip{margin-bottom:.875rem}
.card-sm .cgif{aspect-ratio:1}

/* Bundle */
.card-bundle{
  background:var(--surf);
  border:1px dashed rgba(255,255,255,.12);
  border-radius:14px;
  padding:1.125rem;
  margin-bottom:10px;
}
.bundle-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:.625rem}
.bundle-name{font-family:'Barlow Condensed',sans-serif;font-size:1.3rem;font-weight:900;text-transform:uppercase}
.bundle-right .val{font-family:'Barlow Condensed',sans-serif;font-size:1.7rem;font-weight:900;color:var(--red);line-height:1;text-align:right}
.bundle-right .save{font-size:10px;color:#16a34a;margin-top:2px;text-align:right}
.bundle-inc{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:.875rem}
.b-tag{font-size:10px;background:var(--card);border:1px solid var(--border);padding:3px 8px;border-radius:5px;color:rgba(255,255,255,.45)}
.bundle-desc{font-size:.78rem;color:var(--muted2);line-height:1.5;margin-bottom:.875rem}

/* Condomínio */
.card-cond{
  background:linear-gradient(135deg,#080d1a,#0c1628);
  border:1px solid rgba(74,142,240,.18);
  border-radius:16px;
  overflow:hidden;
  margin-bottom:10px;
  position:relative;
}
.card-cond-accent{height:3px;background:linear-gradient(90deg,#2563eb,#60a5fa)}
.card-cond-body{padding:1.25rem}
.cond-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.875rem}
.cond-name{font-family:'Barlow Condensed',sans-serif;font-size:1.5rem;font-weight:900;text-transform:uppercase;color:#fff}
.cond-sub{font-size:.75rem;color:#4a7aaa;margin-top:2px}
.cond-price .val{font-family:'Barlow Condensed',sans-serif;font-size:1.8rem;font-weight:900;color:#4a8ef0;line-height:1;text-align:right}
.cond-price .per{font-size:10px;color:#4a7aaa;text-align:right;margin-top:2px}
.cond-feats{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:1rem}
.cf{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.5);background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.1);padding:6px 8px;border-radius:7px}
.cf-icon{font-size:14px;flex-shrink:0}

/* FEATURES */
.features{padding:2rem 1.25rem;border-top:1px solid var(--border)}
.feat-list{display:flex;flex-direction:column;gap:0}
.feat-row{display:flex;gap:14px;padding:1.125rem 0;border-bottom:1px solid var(--border);align-items:flex-start}
.feat-row:last-child{border-bottom:none}
.feat-icon-wrap{width:40px;height:40px;background:var(--red-dim);border:1px solid rgba(229,62,26,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.feat-t{font-size:14px;font-weight:600;color:#fff;margin-bottom:3px}
.feat-d{font-size:12px;color:var(--muted2);line-height:1.55}

/* COMO FUNCIONA */
.como{padding:2rem 1.25rem;background:var(--card);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.steps{display:flex;flex-direction:column;gap:0}
.step{display:flex;gap:14px;padding:1rem 0;border-bottom:1px solid var(--border);align-items:flex-start}
.step:last-child{border-bottom:none}
.step-n{
  width:34px;height:34px;border-radius:8px;
  background:var(--red);color:#fff;
  font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:900;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.step-t{font-size:14px;font-weight:600;color:#fff;margin-bottom:3px}
.step-d{font-size:12px;color:var(--muted2);line-height:1.55}

/* DEPOIMENTOS */
.depos{padding:2rem 1.25rem}
.depo-grid{display:flex;flex-direction:column;gap:8px}
.depo{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem}
.depo-text{font-size:.82rem;color:var(--muted2);line-height:1.65;margin-bottom:.75rem;font-style:italic}
.depo-author{display:flex;align-items:center;gap:.625rem}
.depo-avatar{width:36px;height:36px;border-radius:50%;background:var(--surf);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.depo-name{font-size:13px;font-weight:600;color:#fff}
.depo-role{font-size:11px;color:var(--muted)}
.stars{color:var(--red);font-size:11px;margin-bottom:.5rem;letter-spacing:2px}

/* CTA FINAL */
.cta-final{padding:2.5rem 1.25rem;text-align:center;background:radial-gradient(ellipse 80% 50% at 50% 50%,rgba(229,62,26,.08),transparent)}
.cta-final h2{font-family:'Barlow Condensed',sans-serif;font-size:clamp(1.8rem,7vw,2.5rem);font-weight:900;text-transform:uppercase;line-height:1.05;margin-bottom:.75rem}
.cta-final h2 em{color:var(--red);font-style:italic}
.cta-final p{font-size:.85rem;color:var(--muted2);margin-bottom:1.5rem;line-height:1.6}
.cta-price-row{display:flex;justify-content:center;gap:1.25rem;margin-bottom:1.5rem;flex-wrap:wrap}
.cta-price-item{text-align:center}
.cta-price-item .p{font-family:'Barlow Condensed',sans-serif;font-size:1.5rem;font-weight:900;line-height:1}
.cta-price-item .l{font-size:11px;color:var(--muted)}

/* AVISO */
.aviso{margin:0 1.25rem 1.5rem;background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem}
.aviso p{font-size:11px;color:var(--muted);line-height:1.7;text-align:center}
.aviso a{color:rgba(255,255,255,.3);text-decoration:none}
.aviso a:hover{color:var(--red)}

/* FOOTER */
.footer{padding:1.5rem 1.25rem 2.5rem;border-top:1px solid var(--border);text-align:center}
.footer-logo{font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;margin-bottom:.625rem}
.footer-logo b{color:var(--red)}
.footer-links{display:flex;justify-content:center;gap:1.25rem;flex-wrap:wrap;margin-bottom:.75rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-links a:hover{color:var(--red)}
.footer-copy{font-size:11px;color:var(--muted2)}

@media(min-width:600px){
  .hero{min-height:80vh}
  .card-grid{grid-template-columns:1fr 1fr}
  .hero-ctas{flex-direction:row}
  .btn-hero-main,.btn-hero-sec{width:auto;padding:14px 24px}
}
@media(max-width:340px){
  .card-grid{grid-template-columns:1fr}
  .cond-feats{grid-template-columns:1fr}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="hdr">
  <a href="/" class="logo">Sua <b>Biblioteca</b></a>
  <a href="musculacao.php" class="hdr-btn">Explorar →</a>
</header>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>

  <!-- Grid de GIFs no fundo -->
  <div class="hero-gif-grid">
    <div class="hero-gif-col">
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Peitoral/Supino%20inclinado%20com%20barra.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Costas/Remada%20Curvada%20com%20Barra.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Pernas/Agachamento%20Hack.gif" alt="" loading="lazy"></div>
    </div>
    <div class="hero-gif-col">
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Gl%C3%BAteos/Abdu%C3%A7%C3%A3o%20de%20quadril%20com%20cabo.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/B%C3%ADceps/Rosca%20Scott%20com%20Halteres.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Ombros/Desenvolvimento%20de%20ombro%20com%20barra%20sentado.gif" alt="" loading="lazy"></div>
    </div>
    <div class="hero-gif-col">
      <div class="hgif"><img src="/gifs/calistenia/Flex%C3%A3o%20diamante.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/calistenia/Barra%20fixa%20com%20peso.gif" alt="" loading="lazy"></div>
      <div class="hgif"><img src="/gifs/alongamento/Postura%20do%20sapo.gif" alt="" loading="lazy"></div>
    </div>
  </div>

  <div class="hero-content">
    <div class="hero-tag">⚡ Conteúdo educativo</div>
    <h1>Sua<br>biblioteca<br>de <em>treino</em><br>com GIFs</h1>
    <p class="hero-sub">Escolha seus exercícios, veja a execução em GIF e monte sua ficha personalizada. A partir de R$7,90.</p>
    <div class="hero-ctas">
      <a href="musculacao.php" class="btn-hero-main">Estudar minha ficha →</a>
      <a href="#produtos" class="btn-hero-sec">Ver todos os planos</a>
    </div>
    <div class="hero-proof">
      <span class="proof-item">✓ 500+ GIFs de musculação</span>
      <span class="proof-dot"></span>
      <span class="proof-item">✓ Acesso por 3 meses</span>
      <span class="proof-dot"></span>
      <span class="proof-item">✓ Pix instantâneo</span>
    </div>
  </div>
</section>

<!-- TRUST BAR -->
<div class="trust">
  <div class="trust-item">
    <div class="trust-num">500<span style="color:var(--red)">+</span></div>
    <div class="trust-label">GIFs musculação</div>
  </div>
  <div class="trust-item">
    <div class="trust-num">123</div>
    <div class="trust-label">Exercícios calistenia</div>
  </div>
  <div class="trust-item">
    <div class="trust-num">136</div>
    <div class="trust-label">Alongamentos</div>
  </div>
  <div class="trust-item">
    <div class="trust-num">3<span style="color:var(--red)">m</span></div>
    <div class="trust-label">De acesso</div>
  </div>
</div>

<!-- PRODUTOS -->
<section class="produtos" id="produtos">
  <div class="sec-eyebrow">Escolha seu treino</div>
  <div class="sec-title">Monte sua<br>ficha agora</div>

  <!-- MUSCULAÇÃO -->
  <div class="card-hero">
    <div class="card-hero-accent"></div>
    <div class="card-hero-body">
      <div class="card-top">
        <span class="card-badge badge-hot">🔥 Mais popular</span>
        <div class="card-price-wrap">
          <div class="card-price price-c">R$7,90</div>
          <div class="card-price-sub">pagamento único</div>
        </div>
      </div>
      <div class="card-name">💪 Musculação</div>
      <p class="card-desc">Academia com GIFs animados. Você escolhe cada exercício da sua ficha por grupo muscular — do iniciante ao avançado.</p>
      <div class="card-gif-strip">
        <div class="cgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Peitoral/Supino%20inclinado%20com%20barra.gif" alt="Supino" loading="lazy"></div>
        <div class="cgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Costas/Remada%20Curvada%20com%20Barra.gif" alt="Remada" loading="lazy"></div>
        <div class="cgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Pernas/Agachamento%20Hack.gif" alt="Agachamento" loading="lazy"></div>
        <div class="cgif"><img src="/gifs/musculacao/500%20Gifs%20de%20Muscula%C3%A7%C3%A3o/Gl%C3%BAteos/Abdu%C3%A7%C3%A3o%20de%20quadril%20com%20cabo.gif" alt="Glúteos" loading="lazy"></div>
      </div>
      <div class="card-tags">
        <span class="ctag">12 grupos musculares</span>
        <span class="ctag">Você escolhe os exercícios</span>
        <span class="ctag">Iniciante ao avançado</span>
        <span class="ctag">Cardio incluso</span>
        <span class="ctag">Válido 3 meses</span>
      </div>
      <a href="musculacao.php" class="btn-card btn-c">Estudar musculação →</a>
    </div>
  </div>

  <!-- CALISTENIA + ALONGAMENTO -->
  <div class="card-grid">
    <div class="card-sm">
      <span class="card-badge badge-new">Sem equipamento</span>
      <div class="card-gif-strip" style="margin-top:.5rem">
        <div class="cgif"><img src="/gifs/calistenia/Flex%C3%A3o%20diamante.gif" alt="" loading="lazy"></div>
        <div class="cgif"><img src="/gifs/calistenia/Barra%20fixa%20com%20peso.gif" alt="" loading="lazy"></div>
      </div>
      <div class="card-name">🤸 Calistenia</div>
      <div class="card-price price-o">R$9,90</div>
      <p class="card-desc">Em casa ou no parque, com o peso do próprio corpo.</p>
      <div class="card-tags">
        <span class="ctag">123 GIFs</span>
        <span class="ctag">Sem academia</span>
      </div>
      <a href="calistenia.php" class="btn-card btn-outline">Ver →</a>
    </div>
    <div class="card-sm">
      <span class="card-badge badge-calm">Mobilidade</span>
      <div class="card-gif-strip" style="margin-top:.5rem">
        <div class="cgif"><img src="/gifs/alongamento/Postura%20do%20sapo.gif" alt="" loading="lazy"></div>
        <div class="cgif"><img src="/gifs/alongamento/Alongamento%20Borboleta.gif" alt="" loading="lazy"></div>
      </div>
      <div class="card-name">🧘 Alongamento</div>
      <div class="card-price price-g">R$7,90</div>
      <p class="card-desc">Flexibilidade e mobilidade por região do corpo.</p>
      <div class="card-tags">
        <span class="ctag">136 GIFs</span>
        <span class="ctag">Por região</span>
      </div>
      <a href="alongamento.php" class="btn-card btn-outline">Ver →</a>
    </div>
  </div>

  <!-- BUNDLE -->
  <div class="card-bundle">
    <div class="bundle-top">
      <div>
        <span class="card-badge badge-hot" style="margin-bottom:.4rem">📦 Melhor custo-benefício</span>
        <div class="bundle-name">Bundle completo</div>
      </div>
      <div class="bundle-right">
        <div class="val">R$18,90</div>
        <div class="save">✓ Economize R$6,80</div>
      </div>
    </div>
    <div class="bundle-inc">
      <span class="b-tag">💪 Musculação</span>
      <span class="b-tag">🤸 Calistenia</span>
      <span class="b-tag">🧘 Alongamento</span>
    </div>
    <p class="bundle-desc">Os 3 produtos em um único acesso. Treine em qualquer situação — na academia, em casa ou ao ar livre.</p>
    <a href="bundle.php" class="btn-card btn-c">Biblioteca completa →</a>
  </div>

  <!-- CONDOMÍNIO -->
  <div class="card-cond">
    <div class="card-cond-accent"></div>
    <div class="card-cond-body">
      <div class="cond-top">
        <div>
          <span class="card-badge badge-pro" style="margin-bottom:.4rem">🏢 Para síndicos</span>
          <div class="cond-name">Academia do condomínio</div>
          <div class="cond-sub">Moradores têm acesso à biblioteca grátis</div>
        </div>
        <div class="cond-price">
          <div class="val">R$50</div>
          <div class="per">/mês</div>
        </div>
      </div>
      <div class="cond-feats">
        <div class="cf"><span class="cf-icon">📱</span>QR Code para moradores</div>
        <div class="cf"><span class="cf-icon">🏋️</span>Gestão de equipamentos</div>
        <div class="cf"><span class="cf-icon">📊</span>Presenças na academia</div>
        <div class="cf"><span class="cf-icon">⚠️</span>Avisos de manutenção</div>
        <div class="cf"><span class="cf-icon">💪</span>Ficha grátis p/ moradores</div>
        <div class="cf"><span class="cf-icon">📋</span>Relatórios completos</div>
      </div>
      <a href="condominio.php" class="btn-card btn-b">Conhecer o plano condomínio →</a>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features">
  <div class="sec-eyebrow">Por que o Sua Biblioteca</div>
  <div class="feat-list">
    <div class="feat-row">
      <div class="feat-icon-wrap">🎥</div>
      <div>
        <div class="feat-t">GIFs animados de execução</div>
        <div class="feat-d">Veja cada exercício em movimento antes de escolher. Sem vídeo, sem app — funciona direto no navegador.</div>
      </div>
    </div>
    <div class="feat-row">
      <div class="feat-icon-wrap">🎯</div>
      <div>
        <div class="feat-t">Você monta a sua ficha</div>
        <div class="feat-d">Escolhe cada exercício por grupo muscular. A ficha é sua, do seu jeito, no seu ritmo.</div>
      </div>
    </div>
    <div class="feat-row">
      <div class="feat-icon-wrap">⚡</div>
      <div>
        <div class="feat-t">Liberação imediata via Pix</div>
        <div class="feat-d">Pagou, acesso liberado em segundos. Link exclusivo enviado por e-mail.</div>
      </div>
    </div>
    <div class="feat-row">
      <div class="feat-icon-wrap">📅</div>
      <div>
        <div class="feat-t">3 meses de acesso</div>
        <div class="feat-d">Use quando quiser, quantas vezes precisar. De qualquer dispositivo, sem instalar nada.</div>
      </div>
    </div>
    <div class="feat-row">
      <div class="feat-icon-wrap">⚖️</div>
      <div>
        <div class="feat-t">Conteúdo 100% educativo</div>
        <div class="feat-d">Material de referência sobre exercícios físicos. Não prescreve treino — consulte sempre um profissional de Educação Física.</div>
      </div>
    </div>
  </div>
</section>

<!-- COMO FUNCIONA -->
<section class="como">
  <div class="sec-eyebrow">Como funciona</div>
  <div class="steps">
    <div class="step"><div class="step-n">1</div><div><div class="step-t">Escolha seu produto</div><div class="step-d">Musculação, Calistenia, Alongamento ou bundle completo.</div></div></div>
    <div class="step"><div class="step-n">2</div><div><div class="step-t">Responda o quiz (2 min)</div><div class="step-d">Sexo, objetivo, nível e dias disponíveis. Rápido e sem cadastro.</div></div></div>
    <div class="step"><div class="step-n">3</div><div><div class="step-t">Pague via Pix</div><div class="step-d">Seguro via Mercado Pago. Confirmação em segundos.</div></div></div>
    <div class="step"><div class="step-n">4</div><div><div class="step-t">Escolha seus exercícios</div><div class="step-d">Veja os GIFs e selecione um a um por grupo muscular.</div></div></div>
    <div class="step"><div class="step-n">5</div><div><div class="step-t">Acesse por 3 meses</div><div class="step-d">Link exclusivo no seu e-mail. Qualquer dispositivo, a qualquer hora.</div></div></div>
  </div>
</section>

<!-- DEPOIMENTOS -->
<section class="depos">
  <div class="sec-eyebrow">O que dizem</div>
  <div class="depo-grid">
    <div class="depo">
      <div class="stars">★★★★★</div>
      <p class="depo-text">"Finalmente uma coisa simples e direta. Escolhi meus exercícios, vi o GIF de cada um e tá na mão. Sem app, sem mensalidade."</p>
      <div class="depo-author"><div class="depo-avatar">👨</div><div><div class="depo-name">Rafael M.</div><div class="depo-role">Academia há 1 ano</div></div></div>
    </div>
    <div class="depo">
      <div class="stars">★★★★★</div>
      <p class="depo-text">"O GIF de cada exercício ajuda muito. Eu sou iniciante e ficava perdida — agora sei exatamente como fazer cada movimento."</p>
      <div class="depo-author"><div class="depo-avatar">👩</div><div><div class="depo-name">Ana Paula S.</div><div class="depo-role">Iniciante na musculação</div></div></div>
    </div>
    <div class="depo">
      <div class="stars">★★★★★</div>
      <p class="depo-text">"Comprei o bundle e uso o de calistenia nos dias que não dá pra ir na academia. Vale muito o preço."</p>
      <div class="depo-author"><div class="depo-avatar">👨</div><div><div class="depo-name">Carlos H.</div><div class="depo-role">Treina 5x por semana</div></div></div>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="cta-final">
  <h2>Escolha seu treino<br><em>agora mesmo</em></h2>
  <p>Biblioteca educativa de exercícios. Acesso por 3 meses.</p>
  <div class="cta-price-row">
    <div class="cta-price-item"><div class="p" style="color:var(--red)">R$7,90</div><div class="l">Musculação</div></div>
    <div class="cta-price-item"><div class="p" style="color:#d97706">R$9,90</div><div class="l">Calistenia</div></div>
    <div class="cta-price-item"><div class="p" style="color:#16a34a">R$7,90</div><div class="l">Alongamento</div></div>
    <div class="cta-price-item"><div class="p" style="color:var(--red)">R$18,90</div><div class="l">Bundle 3 em 1</div></div>
  </div>
  <a href="musculacao.php" class="btn-hero-main" style="max-width:360px;margin:0 auto;display:block">Explorar biblioteca →</a>
</section>

<!-- AVISO LEGAL -->
<div class="aviso">
  <p>⚠️ <strong style="color:rgba(255,255,255,.4)">Conteúdo educativo:</strong> O material disponibilizado tem caráter exclusivamente educativo sobre exercícios físicos. Não constitui prescrição de treino e não substitui avaliação e acompanhamento de profissional de Educação Física habilitado.<br>
  <a href="privacidade.php">Privacidade</a> · <a href="termos.php">Termos de Uso</a> · <a href="aviso-saude.php">Aviso de Saúde</a></p>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">Sua <b>Biblioteca</b></div>
  <div class="footer-links">
    <a href="privacidade.php">Privacidade</a>
    <a href="termos.php">Termos</a>
    <a href="aviso-saude.php">Aviso de Saúde</a>
    <a href="condominio.php">Condomínios</a>
  </div>
  <div class="footer-copy">© 2026 Sua Biblioteca · Biblioteca educativa de exercícios</div>
</footer>

</body>
</html>
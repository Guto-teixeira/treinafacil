<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_completo'])) {
    $_SESSION['quiz'] = [
        'sexo'        => $_POST['sexo'] ?? 'M',
        'objetivo'    => $_POST['objetivo'] ?? '',
        'nivel'       => $_POST['nivel'] ?? 'Iniciante',
        'dias_semana' => (int)($_POST['dias_semana'] ?? 3),
        'tempo'       => $_POST['tempo'] ?? '60min',
        'restricao'   => $_POST['restricao'] ?? 'nenhuma',
        'idade'       => $_POST['idade'] ?? '',
        'nome'        => $_POST['nome'] ?? '',
        'email'       => $_POST['email'] ?? '',
        'tipo_produto'=> 'calistenia',
        'aceite_marketing' => (isset($_POST['aceite_marketing']) && $_POST['aceite_marketing'] === '1') ? 1 : 0,
    ];
    header('Location: criar_pedido.php?produto=calistenia');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Calistenia — Sua Biblioteca de Exercícios</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --red:#e53e1a;--red-dim:rgba(229,62,26,.12);--red-glow:rgba(229,62,26,.3);
  --dark:#060606;--card:#0f0f0f;--surf:#161616;
  --border:rgba(255,255,255,.07);--border2:rgba(255,255,255,.14);
  --text:#efefef;--muted:#5a5a5a;--muted2:#999;
}
html,body{height:100%;margin:0}
body{background:var(--dark);color:var(--text);font-family:'Inter',sans-serif;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column}

/* HEADER */
.hdr{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;background:rgba(6,6,6,.9);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200}
.logo{font-family:'Barlow Condensed',sans-serif;font-size:20px;font-weight:900;color:#fff;text-decoration:none}
.logo b{color:var(--red)}
.back{font-size:12px;color:var(--muted2);text-decoration:none;display:flex;align-items:center;gap:4px}
.back:hover{color:var(--red)}

/* PROGRESS */
.prog-wrap{padding:.625rem 1.25rem;background:rgba(6,6,6,.8);border-bottom:1px solid var(--border);position:sticky;top:49px;z-index:100}
.prog-top{display:flex;justify-content:space-between;margin-bottom:.375rem}
.prog-label{font-size:11px;color:var(--muted2)}
.prog-pct{font-size:11px;color:var(--red);font-weight:600}
.prog-bar{height:2px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden}
.prog-fill{height:100%;background:var(--red);border-radius:2px;transition:width .4s ease}

/* STEP WRAPPER */
.steps-container{flex:1}
.step{display:none;min-height:calc(100vh - 100px)}
.step.active{display:flex;flex-direction:column}

/* FOTO HERO (passo de sexo) */
.photo-step{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:0;position:relative;min-height:calc(100vh - 100px)}
.photo-opt{
  position:relative;cursor:pointer;overflow:hidden;
  display:flex;flex-direction:column;justify-content:flex-end;
  min-height:400px;
}
.photo-opt img{
  position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:top center;
  transition:transform .3s ease,filter .3s ease;
  filter:brightness(.7);
}
.photo-opt:hover img,.photo-opt.selected img{filter:brightness(.85);transform:scale(1.03)}
.photo-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(0,0,0,.85) 0%,rgba(0,0,0,.2) 50%,transparent 100%);
}
.photo-opt.selected .photo-overlay{background:linear-gradient(to top,rgba(229,62,26,.6) 0%,rgba(229,62,26,.15) 50%,transparent 100%)}
.photo-label{
  position:relative;z-index:2;
  padding:1.25rem;text-align:center;
}
.photo-name{font-family:'Barlow Condensed',sans-serif;font-size:1.5rem;font-weight:900;text-transform:uppercase;color:#fff;letter-spacing:.5px}
.photo-check{
  width:28px;height:28px;border-radius:50%;
  border:2px solid rgba(255,255,255,.4);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto .5rem;
  transition:all .15s;
}
.photo-opt.selected .photo-check{background:var(--red);border-color:var(--red)}
.photo-check::after{content:'✓';font-size:13px;font-weight:700;color:#fff;opacity:0}
.photo-opt.selected .photo-check::after{opacity:1}
.photo-divider{
  position:absolute;left:50%;top:0;bottom:0;width:2px;
  background:var(--dark);z-index:10;
  transform:translateX(-50%);
}
.photo-vs{
  position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);z-index:20;
  width:36px;height:36px;border-radius:50%;
  background:var(--dark);border:2px solid var(--border2);
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:700;color:var(--muted2);
}

/* TELA DE PERGUNTA PADRÃO */
.question-step{flex:1;display:flex;flex-direction:column}
.q-bg{
  position:relative;height:220px;overflow:hidden;flex-shrink:0;
}
.q-bg img{width:100%;height:100%;object-fit:cover;object-position:center top;filter:brightness(.35)}
.q-bg-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to bottom,transparent 0%,var(--dark) 100%);
}
.q-bg-content{
  position:absolute;bottom:0;left:0;right:0;
  padding:1.25rem;
}
.q-eyebrow{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red);margin-bottom:.375rem}
.q-title{font-family:'Barlow Condensed',sans-serif;font-size:2rem;font-weight:900;text-transform:uppercase;line-height:1;color:#fff}
.q-sub{font-size:.78rem;color:rgba(255,255,255,.5);margin-top:.375rem;line-height:1.5}

.q-body{padding:1.25rem;flex:1;display:flex;flex-direction:column}

/* OPTIONS */
.options{display:flex;flex-direction:column;gap:7px;margin-bottom:1.25rem;flex:1}
.opt-btn{
  display:flex;align-items:center;gap:12px;
  padding:13px 14px;
  background:var(--card);border:1.5px solid var(--border);
  border-radius:10px;cursor:pointer;
  font-size:14px;font-weight:500;color:var(--text);
  text-align:left;width:100%;transition:all .15s;
}
.opt-btn:hover{border-color:rgba(229,62,26,.35);background:rgba(229,62,26,.04)}
.opt-btn.selected{border-color:var(--red);background:var(--red-dim)}
.opt-icon{font-size:22px;flex-shrink:0;width:34px;text-align:center}
.opt-text{flex:1}
.opt-label{font-size:14px;font-weight:500;color:#fff;display:block}
.opt-desc{font-size:11px;color:var(--muted2);display:block;margin-top:1px}
.opt-check{width:22px;height:22px;border-radius:50%;border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s}
.opt-btn.selected .opt-check{background:var(--red);border-color:var(--red)}
.opt-check::after{content:'✓';font-size:11px;font-weight:700;color:#fff;opacity:0}
.opt-btn.selected .opt-check::after{opacity:1}

/* GRID 2 cols */
.opts-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-bottom:1.25rem;flex:1}
.opt-grid{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:.875rem .75rem;
  background:var(--card);border:1.5px solid var(--border);
  border-radius:10px;cursor:pointer;text-align:center;
  transition:all .15s;min-height:80px;
}
.opt-grid:hover{border-color:rgba(229,62,26,.35)}
.opt-grid.selected{border-color:var(--red);background:var(--red-dim)}
.opt-grid-icon{font-size:24px;margin-bottom:.4rem}
.opt-grid-label{font-size:13px;font-weight:600;color:#fff}
.opt-grid-sub{font-size:10px;color:var(--muted2);margin-top:2px}

/* INPUT */
.input-group{margin-bottom:.875rem}
.input-label{font-size:11px;font-weight:600;color:var(--muted2);margin-bottom:.375rem;display:block;text-transform:uppercase;letter-spacing:.05em}
.input-field{width:100%;padding:13px 14px;background:var(--card);border:1.5px solid var(--border);border-radius:10px;color:#fff;font-size:15px;font-family:'Inter',sans-serif;transition:border-color .15s;outline:none}
.input-field:focus{border-color:var(--red)}
.input-field::placeholder{color:var(--muted)}

/* CONSENTIMENTO MARKETING */
.consent-row{display:flex;align-items:flex-start;gap:8px;padding:.625rem 0;cursor:pointer}
.consent-row input[type="checkbox"]{width:18px;height:18px;margin-top:2px;accent-color:var(--red);flex-shrink:0}
.consent-row span{font-size:11px;color:var(--muted2);line-height:1.5}
.consent-row span em{color:var(--muted);font-style:normal}

/* BOTÃO */
.btn-wrap{padding:0 1.25rem 1.5rem}
.btn-next{display:block;width:100%;padding:15px;background:var(--red);color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;transition:all .15s;opacity:.35;pointer-events:none;box-shadow:0 0 0 var(--red-glow)}
.btn-next.ready{opacity:1;pointer-events:all;box-shadow:0 0 24px var(--red-glow)}
.btn-next.ready:hover{background:#c93318}
.btn-always{display:block;width:100%;padding:15px;background:var(--red);color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;transition:all .15s;box-shadow:0 0 24px var(--red-glow)}
.btn-always:hover{background:#c93318}

/* PREVIEW PASSO 9 */
.preview-hero{
  position:relative;height:240px;overflow:hidden;flex-shrink:0;
  display:flex;align-items:flex-end;
}
.preview-hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;filter:brightness(.25)}
.preview-hero-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,transparent,var(--dark))}
.preview-hero-content{position:relative;z-index:2;padding:1.25rem;width:100%}
.preview-hero-title{font-family:'Barlow Condensed',sans-serif;font-size:1.8rem;font-weight:900;text-transform:uppercase;line-height:1;margin-bottom:.375rem}
.preview-hero-title em{color:var(--red);font-style:italic}
.preview-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:.625rem}
.preview-tag{font-size:10px;background:var(--red-dim);border:1px solid rgba(229,62,26,.2);color:var(--red);padding:3px 8px;border-radius:5px;font-weight:500}

.preview-body{padding:1.25rem;flex:1;display:flex;flex-direction:column;gap:.875rem}
.preview-blur{
  background:var(--card);border:1px solid var(--border);border-radius:10px;
  padding:.875rem;filter:blur(3px);user-select:none;pointer-events:none;
  font-size:12px;color:var(--muted2);line-height:1.9;
}
.price-card{background:var(--surf);border:1px solid rgba(229,62,26,.2);border-radius:12px;padding:1.125rem;text-align:center}
.price-val{font-family:'Barlow Condensed',sans-serif;font-size:2.8rem;font-weight:900;color:var(--red);line-height:1}
.price-label{font-size:11px;color:var(--muted2);margin-top:3px}
.price-feats{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-top:.875rem}
.price-feat{font-size:11px;color:var(--muted2);display:flex;align-items:center;gap:5px}
.price-feat::before{content:'✓';color:var(--red);font-weight:700;flex-shrink:0}
.aviso-legal{font-size:10px;color:var(--muted);text-align:center;line-height:1.6;padding:.625rem}

/* FOTOS UNSPLASH */
.photo-opt-img-m{background-image:url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&q=80')}
.photo-opt-img-f{background-image:url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80')}

@media(min-width:600px){
  .photo-step{min-height:500px}
  .q-bg{height:280px}
}
</style>
</head>
<body>
 
<header class="hdr">
  <a href="/" class="logo">Sua <b>Biblioteca</b></a>
  <a href="/" class="back">← Voltar</a>
</header>
<div class="prog-wrap">
  <div class="prog-top">
    <span class="prog-label" id="prog-label">Passo 1 de 9</span>
    <span class="prog-pct" id="prog-pct">11%</span>
  </div>
  <div class="prog-bar"><div class="prog-fill" id="prog-fill" style="width:11%"></div></div>
</div>

<form method="POST" id="qform">
<input type="hidden" name="quiz_completo" value="1">
<input type="hidden" name="sexo" id="f-sexo">
<input type="hidden" name="objetivo" id="f-objetivo">
<input type="hidden" name="nivel" id="f-nivel">
<input type="hidden" name="dias_semana" id="f-dias">
<input type="hidden" name="tempo" id="f-tempo">
<input type="hidden" name="restricao" id="f-restricao">
<input type="hidden" name="idade" id="f-idade">
<input type="hidden" name="nome" id="f-nome">
<input type="hidden" name="email" id="f-email">
<input type="hidden" name="aceite_marketing" id="f-marketing" value="0">

<div class="steps-container">

<!-- PASSO 1: SEXO com fotos -->
<div class="step active" id="step-1">
  <div class="photo-step">
    <div class="photo-opt" id="opt-m" onclick="selectPhoto('M')">
      <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=800&q=80&fit=crop&crop=top" alt="Masculino" loading="eager">
      <div class="photo-overlay"></div>
      <div class="photo-label">
        <div class="photo-check"></div>
        <div class="photo-name">Masculino</div>
      </div>
    </div>
    <div class="photo-divider"></div>
    <div class="photo-vs">ou</div>
    <div class="photo-opt" id="opt-f" onclick="selectPhoto('F')">
      <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80&fit=crop&crop=top" alt="Feminino" loading="eager">
      <div class="photo-overlay"></div>
      <div class="photo-label">
        <div class="photo-check"></div>
        <div class="photo-name">Feminino</div>
      </div>
    </div>
  </div>
  <div style="padding:.875rem 1.25rem;background:rgba(6,6,6,.9);border-top:1px solid var(--border)">
    <div style="font-size:11px;color:var(--muted);text-align:center;margin-bottom:.625rem">PASSO 1 DE 9 — Selecione para continuar</div>
    <button type="button" class="btn-next" id="btn-1" onclick="nextStep(2)">Continuar →</button>
  </div>
</div>

<!-- PASSO 2: OBJETIVO -->
<div class="step" id="step-2">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 2 — Objetivo</div>
        <div class="q-title">Seu objetivo principal</div>
        <div class="q-sub">Vamos filtrar os exercícios mais adequados para o seu foco</div>
      </div>
    </div>
    <div class="q-body">
      <div class="options">
        <button type="button" class="opt-btn" onclick="selectOpt(this,'objetivo','hipertrofia',3)">
          <span class="opt-icon">💪</span>
          <span class="opt-text"><span class="opt-label">Ganhar massa muscular</span><span class="opt-desc">Hipertrofia e força</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'objetivo','emagrecimento',3)">
          <span class="opt-icon">🔥</span>
          <span class="opt-text"><span class="opt-label">Perder gordura</span><span class="opt-desc">Definição e emagrecimento</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'objetivo','definicao',3)">
          <span class="opt-icon">⚡</span>
          <span class="opt-text"><span class="opt-label">Definição muscular</span><span class="opt-desc">Manter massa e reduzir gordura</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'objetivo','condicionamento',3)">
          <span class="opt-icon">🏃</span>
          <span class="opt-text"><span class="opt-label">Condicionamento geral</span><span class="opt-desc">Saúde e disposição</span></span>
          <span class="opt-check"></span>
        </button>
      </div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-2" onclick="nextStep(3)">Continuar →</button></div>
  </div>
</div>

<!-- PASSO 3: NÍVEL -->
<div class="step" id="step-3">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 3 — Experiência</div>
        <div class="q-title">Seu nível de treino</div>
        <div class="q-sub">Define a quantidade e intensidade dos exercícios</div>
      </div>
    </div>
    <div class="q-body">
      <div class="options">
        <button type="button" class="opt-btn" onclick="selectOpt(this,'nivel','Iniciante',4)">
          <span class="opt-icon">🌱</span>
          <span class="opt-text"><span class="opt-label">Iniciante</span><span class="opt-desc">Menos de 6 meses de treino · 3 exercícios por grupo</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'nivel','Intermediário',4)">
          <span class="opt-icon">📈</span>
          <span class="opt-text"><span class="opt-label">Intermediário</span><span class="opt-desc">6 meses a 2 anos de treino · 4 exercícios por grupo</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'nivel','Avançado',4)">
          <span class="opt-icon">🏆</span>
          <span class="opt-text"><span class="opt-label">Avançado</span><span class="opt-desc">Mais de 2 anos de treino · 5 exercícios por grupo</span></span>
          <span class="opt-check"></span>
        </button>
      </div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-3" onclick="nextStep(4)">Continuar →</button></div>
  </div>
</div>

<!-- PASSO 4: DIAS -->
<div class="step" id="step-4">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 4 — Frequência</div>
        <div class="q-title">Dias por semana</div>
        <div class="q-sub">Quantos dias você pode treinar?</div>
      </div>
    </div>
    <div class="q-body">
      <div class="opts-grid">
        <button type="button" class="opt-grid" onclick="selectGrid(this,'dias','2',5)">
          <div class="opt-grid-icon">2️⃣</div>
          <div class="opt-grid-label">2 dias</div>
          <div class="opt-grid-sub">Full body</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'dias','3',5)">
          <div class="opt-grid-icon">3️⃣</div>
          <div class="opt-grid-label">3 dias</div>
          <div class="opt-grid-sub">ABC</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'dias','4',5)">
          <div class="opt-grid-icon">4️⃣</div>
          <div class="opt-grid-label">4 dias</div>
          <div class="opt-grid-sub">Upper/Lower</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'dias','5',5)">
          <div class="opt-grid-icon">5️⃣</div>
          <div class="opt-grid-label">5 dias</div>
          <div class="opt-grid-sub">ABCDE</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'dias','6',5)" style="grid-column:1/-1">
          <div class="opt-grid-icon">6️⃣</div>
          <div class="opt-grid-label">6 dias</div>
          <div class="opt-grid-sub">Alta frequência</div>
        </button>
      </div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-4" onclick="nextStep(5)">Continuar →</button></div>
  </div>
</div>

<!-- PASSO 5: TEMPO -->
<div class="step" id="step-5">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1574680096145-d05b474e2155?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 5 — Tempo</div>
        <div class="q-title">Tempo por sessão</div>
        <div class="q-sub">Quanto tempo você tem por treino?</div>
      </div>
    </div>
    <div class="q-body">
      <div class="options">
        <button type="button" class="opt-btn" onclick="selectOpt(this,'tempo','30min',6)">
          <span class="opt-icon">⏱️</span>
          <span class="opt-text"><span class="opt-label">Até 30 minutos</span><span class="opt-desc">Treino rápido e objetivo</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'tempo','60min',6)">
          <span class="opt-icon">⏰</span>
          <span class="opt-text"><span class="opt-label">40 a 60 minutos</span><span class="opt-desc">Treino completo</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'tempo','90min',6)">
          <span class="opt-icon">🕐</span>
          <span class="opt-text"><span class="opt-label">Mais de 1 hora</span><span class="opt-desc">Volume alto</span></span>
          <span class="opt-check"></span>
        </button>
      </div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-5" onclick="nextStep(6)">Continuar →</button></div>
  </div>
</div>

<!-- PASSO 6: RESTRIÇÃO -->
<div class="step" id="step-6">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 6 — Saúde</div>
        <div class="q-title">Alguma restrição física?</div>
        <div class="q-sub">Para filtrar exercícios adequados ao seu corpo</div>
      </div>
    </div>
    <div class="q-body">
      <div class="options">
        <button type="button" class="opt-btn" onclick="selectOpt(this,'restricao','nenhuma',7)">
          <span class="opt-icon">✅</span>
          <span class="opt-text"><span class="opt-label">Nenhuma restrição</span><span class="opt-desc">Posso fazer qualquer exercício</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'restricao','joelho',7)">
          <span class="opt-icon">🦵</span>
          <span class="opt-text"><span class="opt-label">Joelho ou quadril</span><span class="opt-desc">Dor ou limitação nessa região</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'restricao','coluna',7)">
          <span class="opt-icon">🦴</span>
          <span class="opt-text"><span class="opt-label">Coluna</span><span class="opt-desc">Hérnia, lombalgia ou dor nas costas</span></span>
          <span class="opt-check"></span>
        </button>
        <button type="button" class="opt-btn" onclick="selectOpt(this,'restricao','ombro',7)">
          <span class="opt-icon">💪</span>
          <span class="opt-text"><span class="opt-label">Ombro ou cotovelo</span><span class="opt-desc">Dor ou limitação nos membros superiores</span></span>
          <span class="opt-check"></span>
        </button>
      </div>
      <div style="font-size:10px;color:var(--muted);text-align:center;padding:.5rem 0">⚠️ Conteúdo educativo — consulte sempre um profissional de saúde</div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-6" onclick="nextStep(7)">Continuar →</button></div>
  </div>
</div>   
    
<!-- PASSO 7: IDADE -->
<div class="step" id="step-7">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 7 — Perfil</div>
        <div class="q-title">Sua faixa etária</div>
        <div class="q-sub">Para filtrar exercícios compatíveis com sua fase</div>
      </div>
    </div>
    <div class="q-body">
      <div class="opts-grid">
        <button type="button" class="opt-grid" onclick="selectGrid(this,'idade','15-25',8)">
          <div class="opt-grid-icon">🔵</div>
          <div class="opt-grid-label">15 – 25 anos</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'idade','26-35',8)">
          <div class="opt-grid-icon">🟢</div>
          <div class="opt-grid-label">26 – 35 anos</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'idade','36-45',8)">
          <div class="opt-grid-icon">🟡</div>
          <div class="opt-grid-label">36 – 45 anos</div>
        </button>
        <button type="button" class="opt-grid" onclick="selectGrid(this,'idade','46+',8)">
          <div class="opt-grid-icon">🟠</div>
          <div class="opt-grid-label">46 ou mais</div>
        </button>
      </div>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-7" onclick="nextStep(8)">Continuar →</button></div>
  </div>
</div>

<!-- PASSO 8: DADOS -->
<div class="step" id="step-8">
  <div class="question-step">
    <div class="q-bg">
      <img src="https://images.unsplash.com/photo-1549060279-7e168fcee0c2?w=800&q=80" alt="" loading="lazy">
      <div class="q-bg-overlay"></div>
      <div class="q-bg-content">
        <div class="q-eyebrow">Passo 8 — Identificação</div>
        <div class="q-title">Quase lá!</div>
        <div class="q-sub">Para enviarmos o link da sua biblioteca após a confirmação</div>
      </div>
    </div>
    <div class="q-body">
      <div class="input-group">
        <label class="input-label">Seu nome</label>
        <input type="text" class="input-field" id="inp-nome" placeholder="Como você se chama?" oninput="checkInputs()">
      </div>
      <div class="input-group">
        <label class="input-label">Seu e-mail</label>
        <input type="email" class="input-field" id="inp-email" placeholder="seu@email.com" oninput="checkInputs()">
      </div>
      <div style="font-size:11px;color:var(--muted);line-height:1.6;padding:.5rem 0">🔒 Seus dados são usados apenas para enviar o link da biblioteca. Não enviamos spam.</div>
      <label class="consent-row">
        <input type="checkbox" id="chk-marketing" onchange="R.marketing=this.checked">
        <span>Quero receber promoções e novidades por e-mail/WhatsApp <em>(opcional — você pode cancelar quando quiser)</em></span>
      </label>
    </div>
    <div class="btn-wrap"><button type="button" class="btn-next" id="btn-8" onclick="nextStep(9)">Ver prévia →</button></div>
  </div>
</div>

<!-- PASSO 9: PRÉVIA + PAGAMENTO -->
<div class="step" id="step-9">
  <div class="question-step">
    <div class="preview-hero">
      <img src="https://images.unsplash.com/photo-1534367610401-9f5ed68180aa?w=800&q=80" alt="" loading="lazy">
      <div class="preview-hero-overlay"></div>
      <div class="preview-hero-content">
        <div class="preview-hero-title">Sua biblioteca<br>está <em>pronta!</em></div>
        <div class="preview-tags" id="preview-tags"></div>
      </div>
    </div>
    <div class="preview-body">
      <div class="preview-blur">
        Treino A: Peitoral + Tríceps — Flexão de Braço 3x12 · Mergulho no Banco 3x10 · Flexão Diamante 3x10<br>
        Treino B: Costas + Bíceps — Barra Fixa 3x8 · Remada Invertida 3x10 · Rosca Concentrada 3x12<br>
        Treino C: Pernas — Agachamento Livre 3x15 · Afundo 3x12 · Panturrilha em Pé 3x15<br>
        Treino D: Ombros — Flexão Pike 3x10 · Elevação Lateral com Elástico 3x12
      </div>
      <div class="price-card">
        <div class="price-val">R$7,90</div>
        <div class="price-label">pagamento único · acesso por 3 meses</div>
        <div class="price-feats">
          <div class="price-feat">120+ GIFs animados</div>
          <div class="price-feat">Você escolhe os exercícios</div>
          <div class="price-feat">Link exclusivo por e-mail</div>
          <div class="price-feat">Acesso por 3 meses</div>
        </div>
      </div>
      <div class="aviso-legal">⚠️ Conteúdo educativo — não substitui orientação de profissional de Educação Física habilitado</div>
    </div>
    <div class="btn-wrap">
      <button type="button" class="btn-always" onclick="submitQuiz()">🔒 Desbloquear por R$7,90 via Pix →</button>
    </div>
  </div>
</div>

</div>
</form>

<script>
const TOTAL=9;
let R={sexo:'',objetivo:'',nivel:'',dias:'3',tempo:'',restricao:'',idade:'',nome:'',email:'',marketing:false};

function prog(n){
  const p=Math.round((n/TOTAL)*100);
  document.getElementById('prog-fill').style.width=p+'%';
  document.getElementById('prog-label').textContent=`Passo ${n} de ${TOTAL}`;
  document.getElementById('prog-pct').textContent=p+'%';
}

function nextStep(n){
  if(n===9){R.nome=document.getElementById('inp-nome').value.trim();R.email=document.getElementById('inp-email').value.trim();buildPreview();}
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('step-'+n).classList.add('active');
  prog(n);window.scrollTo(0,0);
}

function selectPhoto(val){
  R.sexo=val;
  document.getElementById('opt-m').classList.toggle('selected',val==='M');
  document.getElementById('opt-f').classList.toggle('selected',val==='F');
  document.getElementById('btn-1').classList.add('ready');
}

function selectOpt(btn,campo,val,next){
  btn.closest('.options').querySelectorAll('.opt-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');R[campo]=val;
  document.getElementById('btn-'+(next-1)).classList.add('ready');
}

function selectGrid(btn,campo,val,next){
  btn.closest('.opts-grid').querySelectorAll('.opt-grid').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');R[campo]=val;
  document.getElementById('btn-'+(next-1)).classList.add('ready');
}

function checkInputs(){
  const n=document.getElementById('inp-nome').value.trim();
  const e=document.getElementById('inp-email').value.trim();
  const btn=document.getElementById('btn-8');
  if(n.length>=2&&e.includes('@')&&e.includes('.')){btn.classList.add('ready');R.nome=n;R.email=e;}
  else btn.classList.remove('ready');
}

function buildPreview(){
  const labels={
    sexo:{M:'👨 Masculino',F:'👩 Feminino'},
    objetivo:{hipertrofia:'💪 Hipertrofia',emagrecimento:'🔥 Emagrecimento',definicao:'⚡ Definição',condicionamento:'🏃 Condicionamento'},
    nivel:{Iniciante:'🌱 Iniciante','Intermediário':'📈 Intermediário','Avançado':'🏆 Avançado'},
  };
  const tags=[
    labels.sexo[R.sexo],labels.objetivo[R.objetivo],labels.nivel[R.nivel],
    R.dias+' dias/semana'
  ].filter(Boolean);
  document.getElementById('preview-tags').innerHTML=tags.map(t=>`<span class="preview-tag">${t}</span>`).join('');
}

function submitQuiz(){
  document.getElementById('f-sexo').value=R.sexo;
  document.getElementById('f-objetivo').value=R.objetivo;
  document.getElementById('f-nivel').value=R.nivel;
  document.getElementById('f-dias').value=R.dias;
  document.getElementById('f-tempo').value=R.tempo;
  document.getElementById('f-restricao').value=R.restricao;
  document.getElementById('f-idade').value=R.idade;
  document.getElementById('f-nome').value=R.nome;
  document.getElementById('f-email').value=R.email;
  document.getElementById('f-marketing').value=R.marketing?'1':'0';
  document.getElementById('qform').submit();
}
</script>
</body>
</html>
    
    
    
    
    
    
    
    
    
    
    
    
<?php
// ============================================================
// ficha.php v3 — ClicTreino
// Exibe guia com exercícios escolhidos pelo aluno
// ============================================================
require_once 'config.php';
require_once 'logica_divisao.php';

$token = trim($_GET['token'] ?? '');
if (empty($token)) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("
    SELECT p.*, f.id as ficha_id, f.perfil, f.dias_semana, f.escolhas_aluno, f.conteudo,
           c.nome as cliente_nome
    FROM pedidos p
    LEFT JOIN fichas f ON f.pedido_id = p.id
    LEFT JOIN clientes c ON c.id = p.cliente_id
    WHERE p.token_ficha = ? LIMIT 1
");
$stmt->execute([$token]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido)                            { http_response_code(404); die('<h1>Link inválido</h1>'); }
if (!$pedido || !in_array($pedido['mp_status'], ['approved', 'demo'], true)) { header('Location: index.php'); exit; }
if (empty($pedido['escolhas_aluno']))    { header('Location: selecao.php?token=' . urlencode($token)); exit; }

$conteudo_salvo = json_decode($pedido['conteudo'] ?? '{}', true) ?? [];
$exercicios_escolhidos = $conteudo_salvo['exercicios_escolhidos'] ?? [];

// Se ainda não escolheu exercícios, redireciona
if (empty($exercicios_escolhidos)) {
    header('Location: escolher_exercicios.php?token=' . urlencode($token));
    exit;
}

$escolhas    = json_decode($pedido['escolhas_aluno'], true) ?? [];
$perfil      = json_decode($pedido['perfil'] ?? '{}', true);
$nivel_aluno = $perfil['nivel'] ?? 'Iniciante';
$dias        = (int)($pedido['dias_semana'] ?? count($escolhas));

$nomeAluno  = $pedido['cliente_nome'] ?? 'Atleta';
$dataCompra = date('d/m/Y', strtotime($pedido['criado_em']));
$validade   = date('d/m/Y', strtotime($pedido['criado_em'] . ' +3 months'));

// Pré-carrega exercícios escolhidos por grupo
$dados_grupos = [];
foreach ($exercicios_escolhidos as $grupo => $ids) {
    if (empty($ids)) continue;
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt2 = $pdo->prepare("
        SELECT id, nome, gif_pasta, gif_arquivo, instrucoes, nivel, grupo_muscular
        FROM exercicios
        WHERE id IN ($placeholders) AND ativo = 1
        ORDER BY FIELD(id, $placeholders)
    ");
    $params = array_merge($ids, $ids);
    $stmt2->execute($params);
    $dados_grupos[$grupo] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

$nivel_class = match($nivel_aluno) { 'Intermediário' => 'nivel-int', 'Avançado' => 'nivel-ava', default => 'nivel-ini' };
$nivel_emoji = match($nivel_aluno) { 'Intermediário' => '🟡', 'Avançado' => '🔴', default => '🟢' };
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guia de Exercícios</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{background:#0a0a0a;color:#f0f0f0;font-family:system-ui,-apple-system,sans-serif;min-height:100vh}
.header{background:#111;border-bottom:1px solid #1e1e1e;padding:.875rem 1.25rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.logo{font-size:16px;font-weight:700;color:#fff}.logo span{color:#e53e1a}
.hbadge{font-size:11px;color:#555;background:#1a1a1a;border:1px solid #2a2a2a;padding:3px 10px;border-radius:20px}
.aviso-topo{background:#111a0a;border-bottom:1px solid #1e2e10;padding:7px 1.25rem;font-size:11px;color:#7a9a5a}
.hero{padding:1.5rem 1.25rem 1rem;background:linear-gradient(135deg,#111 0%,#1a0a07 100%);border-bottom:1px solid #1e1e1e}
.hero-tag{display:inline-block;background:#e53e1a;color:#fff;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:.625rem}
.hero h1{font-size:22px;font-weight:700;line-height:1.2;margin-bottom:.375rem}
.hero h1 span{color:#e53e1a}
.hero-meta{display:flex;gap:.875rem;flex-wrap:wrap;margin-top:.625rem}
.meta{font-size:12px;color:#666}.meta strong{color:#999}
.nivel-pill{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px}
.nivel-ini{background:#EAF3DE;color:#27500A}
.nivel-int{background:#FAEEDA;color:#7A4F00}
.nivel-ava{background:#FAECE7;color:#712B13}
.nav-dias{display:flex;gap:6px;overflow-x:auto;padding:.875rem 1.25rem;background:#111;border-bottom:1px solid #1a1a1a;scrollbar-width:none}
.nav-dias::-webkit-scrollbar{display:none}
.nav-btn{flex-shrink:0;padding:7px 14px;background:#1a1a1a;color:#888;border:1px solid #2a2a2a;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;transition:all .15s;white-space:nowrap}
.nav-btn:hover{border-color:#e53e1a;color:#fff}
.nav-btn.active{background:#e53e1a;border-color:#e53e1a;color:#fff}
.main{padding:1.25rem;max-width:700px;margin:0 auto}
.dia-section{display:none}
.dia-section.active{display:block}
.grupo-section{margin-bottom:1.75rem}
.grupo-header{display:flex;align-items:center;gap:10px;margin-bottom:.875rem;padding-bottom:.75rem;border-bottom:1px solid #1e1e1e}
.grupo-emoji{width:38px;height:38px;background:#1a1a1a;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.grupo-nome{font-size:15px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.04em}
.grupo-count{font-size:11px;color:#555;margin-top:2px}
.ex-card{display:flex;gap:12px;align-items:center;padding:10px;background:#111;border:1px solid #1e1e1e;border-radius:10px;margin-bottom:8px;cursor:pointer;transition:border-color .15s}
.ex-card:hover{border-color:#333}
.ex-thumb{width:64px;height:64px;border-radius:8px;background:#1a1a1a;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:22px}
.ex-thumb img{width:100%;height:100%;object-fit:cover}
.ex-body{flex:1;min-width:0}
.ex-nome{font-size:13px;font-weight:500;color:#fff;margin-bottom:5px;line-height:1.3}
.ex-stats{display:flex;gap:5px;flex-wrap:wrap}
.ex-stat{font-size:10px;background:#1a1a1a;border:1px solid #252525;padding:2px 7px;border-radius:5px;color:#666}
.ex-arrow{color:#333;font-size:18px;flex-shrink:0}
.btn-trocar{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#555;background:none;border:1px solid #252525;border-radius:8px;padding:6px 12px;cursor:pointer;margin-bottom:1.25rem;transition:all .15s}
.btn-trocar:hover{border-color:#e53e1a;color:#e53e1a}
.footer-info{margin-top:2rem;padding:1.25rem;background:#111;border:1px solid #1e1e1e;border-radius:12px;text-align:center}
.footer-info p{font-size:12px;color:#444;line-height:1.8}
.footer-info strong{color:#666}
.footer-logo{font-size:13px;font-weight:700;color:#333;margin-top:.625rem}
.footer-logo span{color:#e53e1a}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:1000;display:none;align-items:center;justify-content:center;padding:1.25rem}
.modal-overlay.open{display:flex}
.modal-box{background:#111;border:1px solid #2a2a2a;border-radius:16px;max-width:420px;width:100%;overflow:hidden}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #1e1e1e}
.modal-titulo{font-size:15px;font-weight:600;color:#fff}
.modal-fechar{background:none;border:none;color:#555;font-size:22px;cursor:pointer;line-height:1}
.modal-fechar:hover{color:#fff}
.modal-gif-wrap{background:#000;display:flex;align-items:center;justify-content:center;min-height:220px;max-height:320px;overflow:hidden}
.modal-gif-wrap img{width:100%;max-height:320px;object-fit:contain}
.modal-gif-ph{font-size:60px;opacity:.3}
.modal-info{padding:14px 16px}
.modal-instrucoes{font-size:13px;color:#888;line-height:1.7;margin-top:8px}
.modal-stats{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}
.modal-stat{flex:1;min-width:60px;background:#1a1a1a;border:1px solid #252525;border-radius:8px;padding:8px;text-align:center}
.modal-stat-val{font-size:16px;font-weight:700;color:#fff}
.modal-stat-label{font-size:9px;color:#555;text-transform:uppercase;letter-spacing:.05em}
@media(max-width:480px){.hero h1{font-size:19px}.ex-thumb{width:54px;height:54px}}
</style>
</head>
<body>
<header class="header">
  <div class="logo">Guia de <span>Exercícios</span></div>
  <div class="hbadge">🔒 Acesso exclusivo</div>
</header>
<div class="aviso-topo">⚠️ <strong>Conteúdo educativo:</strong> não substitui a orientação de um profissional de Educação Física.</div>
<div class="hero">
  <div class="hero-tag">Conteúdo educativo</div>
  <h1>Seu guia de <span>exercícios</span></h1>
  <div class="hero-meta">
    <div class="meta">👤 <strong><?= htmlspecialchars($nomeAluno) ?></strong></div>
    <div class="meta"><span class="nivel-pill <?= $nivel_class ?>"><?= $nivel_emoji ?> <?= htmlspecialchars($nivel_aluno) ?></span></div>
    <div class="meta">📅 Válido até <strong><?= $validade ?></strong></div>
    <div class="meta">💪 <strong><?= count($escolhas) ?> treinos</strong></div>
  </div>
</div>

<nav class="nav-dias" role="tablist">
  <?php foreach ($escolhas as $i => $dia): ?>
  <button class="nav-btn <?= $i===0?'active':'' ?>" onclick="mostrarDia(<?= $i ?>)" role="tab">
    Treino <?= htmlspecialchars($dia['letra']) ?>
  </button>
  <?php endforeach; ?>
</nav>

<main class="main" id="main">
  <button class="btn-trocar" onclick="location.href='selecao.php?token=<?= urlencode($token) ?>'">
    ↺ Recomeçar seleção
  </button>

  <?php foreach ($escolhas as $i => $dia): ?>
  <section class="dia-section <?= $i===0?'active':'' ?>" id="dia-<?= $i ?>">

    <?php foreach ($dia['grupos'] as $grupo): ?>
    <?php
      $exs = $dados_grupos[$grupo] ?? [];
      if (empty($exs)) continue;
      $emoji = match($grupo) {
        'Peitoral'=>'🏋️','Costas'=>'🔙','Pernas'=>'🦵','Glúteos'=>'🍑',
        'Bíceps'=>'💪','Tríceps'=>'💪','Ombros'=>'🎯','Trapézio'=>'🔺',
        'Panturrilhas'=>'🦶','Antebraços'=>'🤜','Eretores da espinha'=>'🧘',
        'Cardio Academia'=>'🏃', default=>'💪'
      };
      $series = match($nivel_aluno) { 'Avançado' => '5', 'Intermediário' => '4', default => '3' };
      $reps   = match($nivel_aluno) { 'Avançado' => '6-8', 'Intermediário' => '8-10', default => '10-12' };
    ?>
    <div class="grupo-section">
      <div class="grupo-header">
        <div class="grupo-emoji"><?= $emoji ?></div>
        <div>
          <div class="grupo-nome"><?= htmlspecialchars($grupo) ?></div>
          <div class="grupo-count"><?= count($exs) ?> exercício<?= count($exs)!=1?'s':'' ?> selecionado<?= count($exs)!=1?'s':'' ?></div>
        </div>
      </div>

      <?php foreach ($exs as $ex): ?>
      <?php $gifUrl = getGifUrl($ex['gif_pasta'] ?? '', $ex['gif_arquivo'] ?? ''); ?>
      <div class="ex-card" onclick="abrirModal('<?= htmlspecialchars(addslashes($ex['nome'])) ?>','<?= $gifUrl ?>','<?= htmlspecialchars(addslashes($ex['instrucoes'] ?? '')) ?>','<?= $series ?>','<?= $reps ?>')">
        <div class="ex-thumb">
          <?php if ($gifUrl): ?>
          <img src="<?= $gifUrl ?>" alt="<?= htmlspecialchars($ex['nome']) ?>" loading="lazy">
          <?php else: ?>
          <?= $emoji ?>
          <?php endif; ?>
        </div>
        <div class="ex-body">
          <div class="ex-nome"><?= htmlspecialchars($ex['nome']) ?></div>
          <div class="ex-stats">
            <span class="ex-stat"><?= $series ?> séries</span>
            <span class="ex-stat"><?= $reps ?> reps</span>
          </div>
        </div>
        <div class="ex-arrow">›</div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

  </section>
  <?php endforeach; ?>

  <div class="footer-info">
    <p>Guia gerado para <strong><?= htmlspecialchars($nomeAluno) ?></strong> · Válido até <strong><?= $validade ?></strong><br>
    ⚠️ Material <strong>educativo</strong> — não substitui orientação de profissional de Educação Física.<br>
    Dúvidas? <strong>@clictreino</strong></p>
    <div class="footer-logo">Guia de <span>Exercícios</span></div>
  </div>
</main>

<!-- MODAL -->
<div class="modal-overlay" id="modal" onclick="fecharFora(event)">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-titulo" id="modal-titulo"></span>
      <button class="modal-fechar" onclick="fecharModal()">×</button>
    </div>
    <div class="modal-gif-wrap" id="modal-gif"><div class="modal-gif-ph">💪</div></div>
    <div class="modal-info">
      <div class="modal-instrucoes" id="modal-instrucoes"></div>
      <div class="modal-stats">
        <div class="modal-stat"><div class="modal-stat-val" id="modal-series"></div><div class="modal-stat-label">Séries</div></div>
        <div class="modal-stat"><div class="modal-stat-val" id="modal-reps"></div><div class="modal-stat-label">Repetições</div></div>
      </div>
    </div>
  </div>
</div>

<script>
function mostrarDia(idx) {
  document.querySelectorAll('.dia-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('dia-' + idx).classList.add('active');
  document.querySelectorAll('.nav-btn')[idx].classList.add('active');
  document.getElementById('main').scrollIntoView({behavior:'smooth',block:'start'});
}
function abrirModal(nome, gif, instrucoes, series, reps) {
  document.getElementById('modal-titulo').textContent = nome;
  document.getElementById('modal-series').textContent = series;
  document.getElementById('modal-reps').textContent   = reps;
  document.getElementById('modal-instrucoes').textContent = instrucoes || 'Execute com controle e amplitude completa.';
  document.getElementById('modal-gif').innerHTML = gif
    ? `<img src="${gif}" alt="${nome}" style="width:100%;max-height:320px;object-fit:contain">`
    : '<div class="modal-gif-ph">💪</div>';
  document.getElementById('modal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function fecharModal() {
  document.getElementById('modal').classList.remove('open');
  document.body.style.overflow = '';
  document.getElementById('modal-gif').innerHTML = '<div class="modal-gif-ph">💪</div>';
}
function fecharFora(e) { if (e.target===document.getElementById('modal')) fecharModal(); }
document.addEventListener('keydown', e => { if (e.key==='Escape') fecharModal(); });
</script>
</body>
</html>
<?php
// ============================================================
// escolher_exercicios.php — ClicTreino
// Wizard: aluno escolhe os GIFs de cada grupo muscular
// URL: app.clictreino.com.br/escolher_exercicios.php?token=XXXX
// ============================================================
require_once 'config.php';
require_once 'logica_divisao.php';

$token = trim($_GET['token'] ?? '');
if (empty($token)) { header('Location: index.php'); exit; }

// Busca pedido e ficha
$stmt = $pdo->prepare("
    SELECT p.*, f.id as ficha_id, f.perfil, f.dias_semana, f.escolhas_aluno, f.conteudo
    FROM pedidos p
    LEFT JOIN fichas f ON f.pedido_id = p.id
    LEFT JOIN clientes c ON c.id = p.cliente_id
    WHERE p.token_ficha = ? LIMIT 1
");
$stmt->execute([$token]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido || $pedido['mp_status'] !== 'approved') { header('Location: index.php'); exit; }
if (empty($pedido['escolhas_aluno'])) { header('Location: selecao.php?token=' . urlencode($token)); exit; }

// Lê perfil
$perfil      = json_decode($pedido['perfil'] ?? '{}', true);
$nivel_aluno = $perfil['nivel'] ?? 'Iniciante';
$dias        = (int)($pedido['dias_semana'] ?? 3);
$escolhas    = json_decode($pedido['escolhas_aluno'], true) ?? [];

// Monta lista de todos os grupos a percorrer
$todos_grupos = [];
foreach ($escolhas as $dia) {
    foreach ($dia['grupos'] as $grupo) {
        if (!in_array($grupo, $todos_grupos)) {
            $todos_grupos[] = $grupo;
        }
    }
}

// Quantos exercícios o aluno pode escolher
// getLimite mantido para compatibilidade
function getLimite(string $nivel, int $dias): int {
    if ($dias <= 2) return 1;
    return match($nivel) {
        'Iniciante'     => 3,
        'Intermediário' => 4,
        'Avançado'      => 5,
        default         => 3,
    };
}
$limite = getLimiteExercicios($nivel_aluno, $dias);

// Passo atual
$passo = max(0, min((int)($_GET['passo'] ?? 0), count($todos_grupos) - 1));
$grupo_atual = $todos_grupos[$passo] ?? '';
$total_passos = count($todos_grupos);

// Busca exercícios do grupo e nível atual
$stmt2 = $pdo->prepare("
    SELECT id, nome, gif_pasta, gif_arquivo, instrucoes, nivel
    FROM exercicios
    WHERE grupo_muscular = ? AND nivel = ? AND ativo = 1
    ORDER BY id ASC
");
$stmt2->execute([$grupo_atual, $nivel_aluno]);
$exercicios = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Se não tem exercícios desse nível, pega todos ativos do grupo
if (empty($exercicios)) {
    $stmt3 = $pdo->prepare("SELECT id, nome, gif_pasta, gif_arquivo, instrucoes, nivel FROM exercicios WHERE grupo_muscular = ? AND ativo = 1 ORDER BY id ASC");
    $stmt3->execute([$grupo_atual]);
    $exercicios = $stmt3->fetchAll(PDO::FETCH_ASSOC);
}

// Salva escolhas do passo atual (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids_escolhidos = $_POST['exercicios_ids'] ?? [];
    $ids_escolhidos = array_slice(array_map('intval', $ids_escolhidos), 0, $limite);

    // Lê conteúdo atual
    $conteudo = json_decode($pedido['conteudo'] ?? '{}', true) ?? [];
    if (!isset($conteudo['exercicios_escolhidos'])) $conteudo['exercicios_escolhidos'] = [];
    $conteudo['exercicios_escolhidos'][$grupo_atual] = $ids_escolhidos;

    $stmt4 = $pdo->prepare("UPDATE fichas SET conteudo = ? WHERE pedido_id = ?");
    $stmt4->execute([json_encode($conteudo, JSON_UNESCAPED_UNICODE), $pedido['id']]);

    // Próximo passo ou ficha final
    $proximo = $passo + 1;
    if ($proximo >= $total_passos) {
        header('Location: ficha.php?token=' . urlencode($token));
    } else {
        header('Location: escolher_exercicios.php?token=' . urlencode($token) . '&passo=' . $proximo);
    }
    exit;
}

// Exercícios já escolhidos para este grupo
$conteudo_atual = json_decode($pedido['conteudo'] ?? '{}', true) ?? [];
$ja_escolhidos  = $conteudo_atual['exercicios_escolhidos'][$grupo_atual] ?? [];

$emoji_grupo = match($grupo_atual) {
    'Peitoral' => '🏋️', 'Costas' => '🔙', 'Pernas' => '🦵',
    'Glúteos' => '🍑', 'Bíceps' => '💪', 'Tríceps' => '💪',
    'Ombros' => '🎯', 'Trapézio' => '🔺', 'Panturrilhas' => '🦶',
    'Antebraços' => '🤜', 'Eretores da espinha' => '🧘', 'Cardio Academia' => '🏃',
    default => '💪'
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escolha seus exercícios — ClicTreino</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0a0a;color:#f0f0f0;font-family:system-ui,-apple-system,sans-serif;min-height:100vh}
.header{background:#111;border-bottom:1px solid #1e1e1e;padding:.875rem 1.25rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.logo{font-size:16px;font-weight:700;color:#fff}.logo span{color:#e53e1a}
.aviso{background:#111a0a;border-bottom:1px solid #1e2e10;padding:7px 1.25rem;font-size:11px;color:#7a9a5a}

/* PROGRESSO */
.progress-wrap{padding:1rem 1.25rem;background:#111;border-bottom:1px solid #1e1e1e}
.progress-info{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.progress-label{font-size:13px;font-weight:500;color:#fff}
.progress-count{font-size:12px;color:#555}
.progress-bar{height:4px;background:#1e1e1e;border-radius:4px;overflow:hidden}
.progress-fill{height:100%;background:#e53e1a;border-radius:4px;transition:width .3s ease}

/* GRUPO HEADER */
.grupo-hero{padding:1.25rem;background:linear-gradient(135deg,#111 0%,#1a0a07 100%);border-bottom:1px solid #1e1e1e}
.grupo-tag{display:inline-block;background:#e53e1a;color:#fff;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:.5rem}
.grupo-titulo{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin-bottom:.375rem}
.grupo-sub{font-size:13px;color:#666;line-height:1.5}

/* AVISO QUANTIDADE */
.aviso-qtd{margin:1rem 1.25rem 0;background:#1a1a0a;border:1px solid #2a2a10;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px}
.aviso-qtd-icon{font-size:20px;flex-shrink:0}
.aviso-qtd-text{font-size:13px;color:#a0a060;line-height:1.5}
.aviso-qtd-text strong{color:#d4d060;font-size:15px}

/* CONTADOR */
.contador-wrap{margin:1rem 1.25rem 0;display:flex;align-items:center;justify-content:space-between}
.contador{font-size:13px;color:#666}
.contador span{color:#e53e1a;font-weight:700;font-size:16px}

/* GRID DE EXERCÍCIOS */
.main{padding:1rem 1.25rem;max-width:700px;margin:0 auto}
.ex-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:1.5rem}
.ex-item{position:relative;cursor:pointer}
.ex-item input[type=checkbox]{position:absolute;opacity:0;width:0;height:0}
.ex-label{display:flex;flex-direction:column;border:2px solid #1e1e1e;border-radius:12px;overflow:hidden;background:#111;transition:border-color .15s,transform .1s;cursor:pointer}
.ex-label:hover{border-color:#333;transform:scale(1.02)}
.ex-item input:checked + .ex-label{border-color:#e53e1a;background:rgba(229,62,26,.08)}
.ex-gif-wrap{width:100%;aspect-ratio:1;background:#1a1a1a;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:32px;position:relative}
.ex-gif-wrap img{width:100%;height:100%;object-fit:cover}
.ex-check{position:absolute;top:6px;right:6px;width:22px;height:22px;background:#e53e1a;border-radius:50%;display:none;align-items:center;justify-content:center;font-size:13px}
.ex-item input:checked + .ex-label .ex-check{display:flex}
.ex-info{padding:8px 10px}
.ex-nome{font-size:11px;font-weight:500;color:#fff;line-height:1.3;margin-bottom:3px}
.ex-nivel{font-size:9px;padding:1px 6px;border-radius:4px;display:inline-block}
.nivel-ini{background:#EAF3DE;color:#27500A}
.nivel-int{background:#FAEEDA;color:#7A4F00}
.nivel-ava{background:#FAECE7;color:#712B13}

/* BOTÃO */
.btn-wrap{position:sticky;bottom:0;background:#0a0a0a;border-top:1px solid #1e1e1e;padding:1rem 1.25rem}
.btn-avancar{width:100%;padding:15px;background:#e53e1a;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;opacity:.4;pointer-events:none}
.btn-avancar.pronto{opacity:1;pointer-events:all}
.btn-avancar:hover.pronto{background:#c9341a}

/* VAZIO */
.vazio{text-align:center;padding:3rem 1rem;color:#444}
.vazio-icon{font-size:40px;margin-bottom:1rem}

@media(max-width:400px){
  .ex-grid{grid-template-columns:repeat(2,1fr)}
}
</style>
</head>
<body>

<header class="header">
  <div class="logo">Guia de <span>Exercícios</span></div>
  <div style="font-size:11px;color:#555"><?= $passo+1 ?>/<?= $total_passos ?></div>
</header>

<div class="aviso">⚠️ Conteúdo educativo — não substitui orientação de profissional de Educação Física</div>

<!-- PROGRESSO -->
<div class="progress-wrap">
  <div class="progress-info">
    <span class="progress-label"><?= htmlspecialchars($grupo_atual) ?></span>
    <span class="progress-count">Grupo <?= $passo+1 ?> de <?= $total_passos ?></span>
  </div>
  <div class="progress-bar">
    <div class="progress-fill" style="width:<?= round((($passo+1)/$total_passos)*100) ?>%"></div>
  </div>
</div>

<!-- GRUPO HERO -->
<div class="grupo-hero">
  <div class="grupo-tag"><?= htmlspecialchars($nivel_aluno) ?></div>
  <div class="grupo-titulo"><?= $emoji_grupo ?> <?= htmlspecialchars($grupo_atual) ?></div>
  <div class="grupo-sub">Escolha os exercícios que farão parte do seu guia</div>
</div>

<!-- AVISO QUANTIDADE -->
<div class="aviso-qtd">
  <div class="aviso-qtd-icon">ℹ️</div>
  <div class="aviso-qtd-text">
    Escolha até <strong><?= $limite ?></strong> exercício<?= $limite > 1 ? 's' : '' ?> para <?= htmlspecialchars($grupo_atual) ?> — mínimo 1.
    <?php if ($limite === 1): ?>
    Com <?= $dias ?> dia<?= $dias > 1 ? 's' : '' ?> de treino, 1 exercício por grupo é o ideal.
    <?php else: ?>
    Nível <?= htmlspecialchars($nivel_aluno) ?> · <?= $dias ?> dias por semana.
    <?php endif; ?>
  </div>
</div>

<!-- CONTADOR -->
<div class="contador-wrap">
  <div class="contador">Selecionados: <span id="contador">0</span> / <?= $limite ?></div>
  <div style="font-size:11px;color:#444">Clique para selecionar</div>
</div>

<!-- FORM -->
<form method="POST" action="escolher_exercicios.php?token=<?= urlencode($token) ?>&passo=<?= $passo ?>" id="form-ex">
<div class="main">
  <?php if (empty($exercicios)): ?>
  <div class="vazio">
    <div class="vazio-icon">😕</div>
    <p>Nenhum exercício encontrado para <?= htmlspecialchars($grupo_atual) ?>.<br>
    Vamos avançar para o próximo grupo.</p>
    <input type="hidden" name="exercicios_ids[]" value="0">
  </div>
  <?php else: ?>
  <div class="ex-grid">
    <?php foreach ($exercicios as $ex): ?>
    <?php
      $gifUrl  = getGifUrl($ex['gif_pasta'] ?? '', $ex['gif_arquivo'] ?? '');
      $checked = in_array($ex['id'], $ja_escolhidos) ? 'checked' : '';
      $nivel_class = match($ex['nivel']) { 'Intermediário' => 'nivel-int', 'Avançado' => 'nivel-ava', default => 'nivel-ini' };
    ?>
    <div class="ex-item">
      <input type="checkbox" name="exercicios_ids[]" id="ex-<?= $ex['id'] ?>" value="<?= $ex['id'] ?>" <?= $checked ?> onchange="atualizarContador()">
      <label class="ex-label" for="ex-<?= $ex['id'] ?>">
        <div class="ex-gif-wrap">
          <?php if ($gifUrl): ?>
          <img src="<?= $gifUrl ?>" alt="<?= htmlspecialchars($ex['nome']) ?>" loading="lazy">
          <?php else: ?>
          <?= $emoji_grupo ?>
          <?php endif; ?>
          <div class="ex-check">✓</div>
        </div>
        <div class="ex-info">
          <div class="ex-nome"><?= htmlspecialchars($ex['nome']) ?></div>
          <span class="ex-nivel <?= $nivel_class ?>"><?= $ex['nivel'] ?></span>
        </div>
      </label>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="btn-wrap">
  <button type="submit" class="btn-avancar <?= !empty($ja_escolhidos) ? 'pronto' : '' ?>" id="btn-avancar">
    <?= $passo + 1 >= $total_passos ? 'Ver meu Guia →' : 'Próximo grupo →' ?>
  </button>
</div>
</form>

<script>
const LIMITE = <?= $limite ?>;

function atualizarContador() {
  const checks = document.querySelectorAll('input[type=checkbox]:checked');
  const total  = checks.length;
  document.getElementById('contador').textContent = total;

  // Desabilita os desmarcados quando atingir o limite
  document.querySelectorAll('input[type=checkbox]').forEach(cb => {
    if (!cb.checked) {
      cb.disabled = total >= LIMITE;
      cb.closest('.ex-item').style.opacity = (total >= LIMITE) ? '0.4' : '1';
    }
  });

  // Libera o botão com mínimo 1 selecionado (até o limite)
  const btn = document.getElementById('btn-avancar');
  if (total >= 1 || <?= empty($exercicios) ? 'true' : 'false' ?>) {
    btn.classList.add('pronto');
  } else {
    btn.classList.remove('pronto');
  }
}

// Inicializa
atualizarContador();
</script>
</body>
</html>
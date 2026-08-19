<?php
// ============================================================
// selecao.php — ClicTreino
// Tela de seleção dos grupos musculares por dia
// URL: app.clictreino.com.br/selecao.php?token=XXXX
// ============================================================
require_once 'config.php';
require_once 'logica_divisao.php';

$token = trim($_GET['token'] ?? '');
if (empty($token)) { header('Location: index.php'); exit; }

// Busca pedido
$stmt = $pdo->prepare("SELECT p.*, f.id as ficha_id, f.perfil, f.dias_semana, f.escolhas_aluno, c.nome as cliente_nome FROM pedidos p LEFT JOIN fichas f ON f.pedido_id = p.id LEFT JOIN clientes c ON c.id = p.cliente_id WHERE p.token_ficha = ? LIMIT 1");
$stmt->execute([$token]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido || $pedido['mp_status'] !== 'approved') { header('Location: index.php'); exit; }

// Se já tem escolhas salvas, redireciona direto para a ficha
if (!empty($pedido['escolhas_aluno'])) {
    header('Location: escolher_exercicios.php?token=' . urlencode($token));
    exit;
}

// Lê perfil do quiz
$perfil = json_decode($pedido['perfil'] ?? '{}', true);
$sexo   = strtoupper($perfil['sexo'] ?? 'M');
$dias   = (int)($perfil['dias_semana'] ?? 3);
$nivel  = $perfil['nivel'] ?? 'Iniciante';

// Grupos disponíveis no banco
$grupos_disponiveis = getGruposDisponiveis($pdo);

// Divisão sugerida
$divisao_sugerida = getDivisaoSugerida($dias, $sexo);

// Salva escolhas se POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $escolhas = [];
    foreach ($divisao_sugerida as $i => $dia) {
        $grupos_escolhidos = $_POST['grupos_' . $i] ?? $dia['grupos'];
        if (is_string($grupos_escolhidos)) $grupos_escolhidos = [$grupos_escolhidos];
        $escolhas[] = [
            'letra'  => $dia['letra'],
            'nome'   => $_POST['nome_' . $i] ?? $dia['nome'],
            'grupos' => array_values(array_filter($grupos_escolhidos)),
        ];
    }
    $json = json_encode($escolhas, JSON_UNESCAPED_UNICODE);
    $stmt2 = $pdo->prepare("UPDATE fichas SET escolhas_aluno = ?, dias_semana = ? WHERE pedido_id = ?");
    $stmt2->execute([$json, $dias, $pedido['id']]);
    header('Location: escolher_exercicios.php?token=' . urlencode($token));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monte seu Guia — ClicTreino</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0a0a;color:#f0f0f0;font-family:system-ui,-apple-system,sans-serif;min-height:100vh}
.header{background:#111;border-bottom:1px solid #1e1e1e;padding:.875rem 1.25rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.logo{font-size:16px;font-weight:700;color:#fff}.logo span{color:#e53e1a}
.step-badge{font-size:11px;color:#555;background:#1a1a1a;border:1px solid #2a2a2a;padding:3px 10px;border-radius:20px}
.aviso{background:#111a0a;border-bottom:1px solid #1e2e10;padding:7px 1.25rem;font-size:11px;color:#7a9a5a}
.hero{padding:1.5rem 1.25rem 1rem;background:linear-gradient(135deg,#111 0%,#1a0a07 100%);border-bottom:1px solid #1e1e1e}
.hero-tag{display:inline-block;background:#e53e1a;color:#fff;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:.625rem}
.hero h1{font-size:22px;font-weight:700;line-height:1.2;margin-bottom:.375rem}
.hero h1 span{color:#e53e1a}
.hero p{font-size:13px;color:#666;line-height:1.6}
.main{padding:1.25rem;max-width:680px;margin:0 auto}
.dias-info{display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap}
.info-badge{background:#1a1a1a;border:1px solid #252525;border-radius:8px;padding:6px 12px;font-size:12px;color:#888}
.info-badge strong{color:#fff}
.dia-card{background:#111;border:1px solid #1e1e1e;border-radius:12px;margin-bottom:12px;overflow:hidden}
.dia-card-header{display:flex;align-items:center;gap:12px;padding:12px 14px;background:#161616;border-bottom:1px solid #1e1e1e}
.dia-letra{width:38px;height:38px;border-radius:8px;background:#e53e1a;color:#fff;font-size:17px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.dia-nome-input{flex:1;background:transparent;border:none;color:#fff;font-size:14px;font-weight:500;outline:none;min-width:0}
.dia-nome-input::placeholder{color:#444}
.grupos-wrap{padding:12px 14px}
.grupos-label{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px}
.grupos-grid{display:flex;flex-wrap:wrap;gap:7px}
.grupo-btn{padding:7px 13px;border-radius:8px;border:1px solid #2a2a2a;background:#1a1a1a;color:#888;font-size:12px;font-weight:500;cursor:pointer;transition:all .15s;user-select:none}
.grupo-btn.ativo{background:rgba(229,62,26,.12);border-color:#e53e1a;color:#fff}
.grupo-btn:hover{border-color:#444;color:#ccc}
.hidden-input{display:none}
.btn-confirmar{display:block;width:100%;padding:15px;background:#e53e1a;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;margin-top:0;margin-bottom:1.25rem;transition:background .15s}
.btn-confirmar:hover{background:#c9341a}
.aviso-legal{margin-top:1.25rem;background:#111a0a;border:1px solid #1e2e10;border-radius:10px;padding:10px 14px;font-size:11px;color:#7a9a5a;line-height:1.6}
</style>
</head>
<body>
<header class="header">
  <div class="logo">Guia de <span>Exercícios</span></div>
  <div class="step-badge">Passo 1 de 2</div>
</header>
<div class="aviso">⚠️ Conteúdo educativo — não substitui orientação de profissional de Educação Física</div>
<div class="hero">
  <div class="hero-tag">Monte seu plano</div>
  <h1>Olá, <span><?= htmlspecialchars($pedido['cliente_nome'] ?? 'atleta') ?></span>!</h1>
  <p>Sugerimos a divisão abaixo com base no seu perfil.<br>Você pode trocar os grupos musculares de cada dia.</p>
</div>

<div class="main">
  <div class="dias-info">
    <div class="info-badge">💪 <strong><?= $dias ?> dias</strong> por semana</div>
    <div class="info-badge">📊 Nível: <strong><?= htmlspecialchars($nivel) ?></strong></div>
    <div class="info-badge">👤 <strong><?= $sexo === 'F' ? 'Feminino' : 'Masculino' ?></strong></div>
  </div>

  <button type="submit" form="form-selecao" class="btn-confirmar">Escolher Exercícios →</button>

  <form method="POST" action="selecao.php?token=<?= urlencode($token) ?>" id="form-selecao">
  <?php foreach ($divisao_sugerida as $i => $dia): ?>
  <div class="dia-card">
    <div class="dia-card-header">
      <div class="dia-letra"><?= $dia['letra'] ?></div>
      <input class="dia-nome-input" type="text" name="nome_<?= $i ?>" value="<?= htmlspecialchars($dia['nome']) ?>" placeholder="Nome do treino">
    </div>
    <div class="grupos-wrap">
      <div class="grupos-label">Grupos musculares — clique para adicionar ou remover</div>
      <div class="grupos-grid" id="grupos-grid-<?= $i ?>">
        <?php foreach ($grupos_disponiveis as $grupo): ?>
        <?php $ativo = in_array($grupo, $dia['grupos']); ?>
        <button type="button"
          class="grupo-btn <?= $ativo ? 'ativo' : '' ?>"
          onclick="toggleGrupo(this, <?= $i ?>, '<?= htmlspecialchars($grupo, ENT_QUOTES) ?>')"
        ><?= htmlspecialchars($grupo) ?></button>
        <?php endforeach; ?>
      </div>
      <!-- Inputs hidden gerados via JS -->
      <div id="inputs-<?= $i ?>">
        <?php foreach ($dia['grupos'] as $g): ?>
        <input type="hidden" name="grupos_<?= $i ?>[]" value="<?= htmlspecialchars($g) ?>">
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  </form>

  <div class="aviso-legal">
    ⚠️ <strong>Aviso:</strong> Este guia é um material educativo de referência sobre exercícios físicos. Não constitui prescrição de treino. Consulte sempre um profissional de Educação Física habilitado antes de iniciar qualquer atividade física.
  </div>
</div>

<script>
function toggleGrupo(btn, diaIdx, grupo) {
  btn.classList.toggle('ativo');
  const container = document.getElementById('inputs-' + diaIdx);
  const existente = container.querySelector(`input[value="${grupo}"]`);
  if (existente) {
    existente.remove();
  } else {
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'grupos_' + diaIdx + '[]';
    inp.value = grupo;
    container.appendChild(inp);
  }
}
document.getElementById('form-selecao').addEventListener('submit', function(e) {
  const dias = document.querySelectorAll('[id^="inputs-"]');
  let ok = true;
  dias.forEach((d, i) => {
    if (d.querySelectorAll('input').length === 0) {
      alert('Selecione pelo menos 1 grupo muscular para o Treino ' + String.fromCharCode(65+i));
      ok = false;
    }
  });
  if (!ok) e.preventDefault();
});
</script>
</body>
</html>
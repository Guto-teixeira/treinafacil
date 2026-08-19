<?php
// pagar.php — QR Code Pix + polling de confirmação
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['pix'])) {
    header('Location: index.php');
    exit;
}

$pix = $_SESSION['pix'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClicTreino — Pague com Pix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --verde:  #00C853;
            --preto:  #0A0A0A;
            --cinza1: #141414;
            --cinza2: #1E1E1E;
            --cinza3: #2A2A2A;
            --texto:  #F0F0F0;
            --muted:  #888;
        }
        body {
            background: var(--preto);
            color: var(--texto);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .card {
            background: var(--cinza1);
            border: 1px solid var(--cinza3);
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            padding: 32px;
            text-align: center;
        }
        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: var(--verde);
            margin-bottom: 4px;
        }
        h2 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .sub {
            color: var(--muted);
            font-size: .85rem;
            margin-bottom: 24px;
        }
        .qr-box {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .qr-box img { width: 200px; height: 200px; display: block; }

        .valor {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.2rem;
            color: var(--verde);
            margin-bottom: 4px;
        }

        .copia-cola {
            background: var(--cinza2);
            border: 1px solid var(--cinza3);
            border-radius: 8px;
            padding: 12px;
            font-size: .75rem;
            color: var(--muted);
            word-break: break-all;
            text-align: left;
            margin-bottom: 10px;
            max-height: 72px;
            overflow: hidden;
        }
        .btn-copiar {
            width: 100%;
            background: var(--cinza2);
            border: 1px solid var(--verde);
            color: var(--verde);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.1rem;
            letter-spacing: 1px;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: background .2s;
            margin-bottom: 20px;
        }
        .btn-copiar:hover { background: var(--cinza3); }

        .status-box {
            background: var(--cinza2);
            border: 1px solid var(--cinza3);
            border-radius: 10px;
            padding: 14px;
            font-size: .85rem;
            color: var(--muted);
        }
        .status-box .dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #f59e0b;
            margin-right: 6px;
            animation: pulse 1.2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .3; }
        }

        /* Tela de sucesso */
        .sucesso {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .sucesso .check {
            font-size: 4rem;
            line-height: 1;
        }
        .sucesso h2 { font-size: 1.4rem; }
        .btn-ficha {
            display: inline-block;
            background: var(--verde);
            color: #000;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.2rem;
            letter-spacing: 1px;
            border-radius: 8px;
            padding: 14px 28px;
            text-decoration: none;
            margin-top: 8px;
        }
        .salvar-info {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 4px;
        }
    </style>
</head>
<body>
<div class="card" id="tela-pix">
    <div class="logo">ClicTreino</div>
    <h2>Pague com Pix</h2>
    <p class="sub">Escaneie o QR Code ou copie o código abaixo</p>

    <?php if (!empty($pix['qr_img'])): ?>
    <div class="qr-box">
        <img src="data:image/png;base64,<?= $pix['qr_img'] ?>" alt="QR Code Pix">
    </div>
    <?php endif; ?>

    <div class="valor">R$ 7,90</div>
    <p class="sub" style="margin-bottom:12px">para <strong><?= htmlspecialchars($pix['nome']) ?></strong></p>

    <?php if (!empty($pix['qr_code'])): ?>
    <div class="copia-cola" id="codigo-pix"><?= htmlspecialchars($pix['qr_code']) ?></div>
    <button class="btn-copiar" onclick="copiarPix()">📋 Copiar código Pix</button>
    <?php endif; ?>

    <div class="status-box">
        <span class="dot"></span>
        Aguardando pagamento...
    </div>
</div>

<!-- Tela de sucesso (aparece após confirmação) -->
<div class="card sucesso" id="tela-sucesso">
    <div class="check">✅</div>
    <h2>Pagamento confirmado!</h2>
    <p style="color:var(--muted);font-size:.9rem">Sua ficha de treino está pronta 💪</p>
    <a href="" id="link-ficha" class="btn-ficha">ACESSAR MINHA FICHA</a>
    <p class="salvar-info">💾 Salve este link nos favoritos para acessar a qualquer hora</p>
</div>

<script>
const pedidoId = <?= json_encode($pix['pedido_id']) ?>;
const token    = <?= json_encode($pix['token']) ?>;

function copiarPix() {
    const codigo = document.getElementById('codigo-pix').textContent.trim();
    navigator.clipboard.writeText(codigo).then(() => {
        const btn = document.querySelector('.btn-copiar');
        btn.textContent = '✅ Código copiado!';
        setTimeout(() => btn.textContent = '📋 Copiar código Pix', 2500);
    });
}

async function verificarStatus() {
    try {
        const r = await fetch(`status.php?pedido_id=${pedidoId}`);
        const d = await r.json();
        if (d.status === 'approved') {
            document.getElementById('tela-pix').style.display = 'none';
            const sucesso = document.getElementById('tela-sucesso');
            sucesso.style.display = 'flex';
            document.getElementById('link-ficha').href = `ficha.php?token=${token}`;
            clearInterval(poll);
        }
    } catch(e) {}
}

// Polling a cada 4 segundos
const poll = setInterval(verificarStatus, 4000);
verificarStatus();
</script>
</body>
</html>